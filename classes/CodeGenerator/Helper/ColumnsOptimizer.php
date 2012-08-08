<?php
/**
 * Helper class for column tokens that allows to auto-align them (set column widths automatically)
 * and distribute the columns according to fixed column widths.
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;
use CodeGenerator\Token,
	CodeGenerator\Math\Matrix,
	CodeGenerator\Math\SimpleOptimizer as Optimizer;

class ColumnsOptimizer extends \CodeGenerator\Object
{
	/**
	 * @var  array  Column-type tokens that will be aligned, other will be ignored (auto width)
	 */
	private $_column_tokens = array();
	/**
	 * @var  Matrix  Possible solutions space for the columns content widths (auto width)
	 */
	private $_solutions_space = array();
	/**
	 * @var  array  Actual columns content widths (align)
	 */
	private $_actual_widths;
	/**
	 * @var  array  Set column widths (align)
	 */
	private $_fixed_widths;
	/**
	 * @var  array  Token columns (align)
	 */
	private $_columns;
	/**
	 * @var  integer  Current columns count (align)
	 */
	private $_columns_count;
	/**
	 * @var  integer  Current cursor (align)
	 */
	private $_cursor;

	/**
	 * Compute the optimal column widths for the array of tokens
	 * 
	 * @param  array  Array of tokens to align
	 */
	public function auto_width($tokens)
	{
		$this->_setup_auto_width($tokens);
		if (empty($this->_solutions_space))
		{
			return;
		}
		$solver = new Optimizer(array($this, 'evaluate_parameters'), $this->_solutions_space);
		$solution = $solver->execute();
		$best_params = reset($solution);
		foreach ($this->_column_tokens as $token)
		{
			$token->widths($best_params);
		}
	}

	/**
	 * Determine and setup the actual column widths for all tokens as a pre-requisite for the alignment
	 */
	private function _setup_auto_width($tokens)
	{
		if ( ! is_array($tokens))
		{
			throw new \InvalidArgumentException('Columns.tokens() takes an array as argument');
		}
		$this->_column_tokens = array_values(array_filter($tokens, function($token) {
			return $token instanceof Token\Columns;
		}));
		$this->_setup_solutions_space();
	}

	/**
	 * Setup possible solutions space
	 */
	private function _setup_solutions_space()
	{
		$columns = array();
		for ($i = 0; $i < count($this->_column_tokens); $i++)
		{
			$widths = $this->_get_actual_widths($this->_column_tokens[$i]->columns());
			for ($j = 0; $j < count($widths); $j++)
			{
				if ( ! isset($columns[$j])) {
					$columns[$j] = array();
				}
				$columns[$j][] = $widths[$j];
			}
		}

		$this->_solutions_space = array();
		for ($i = 0; $i < count($columns); $i++)
		{
			$actual_values = $columns[$i];
			$this->_solutions_space[$i] = array();
			$possible_widths = array_unique(array_filter($actual_values, function($item) {
				return (bool) $item;
			}));
			if ($i === count($this->_column_tokens) - 1)
			{
				// Use max width for the last column
				$possible_widths = array(max($possible_widths));
			}
			foreach ($possible_widths as $possible_width)
			{
				$this->_solutions_space[$i][] = $possible_width;
			}
		}
	}

	/**
	 * Evaluates a 'fitness' of widths for the tokens set
	 * 
	 * @return  float
	 */
	public function evaluate_parameters()
	{
		$whitespace_count = 0;
		$widths = func_get_args();
		foreach ($this->_column_tokens as $token)
		{
			$token->widths($widths);
			foreach ($this->align($token) as $part)
			{
				if ($part instanceof Token\Whitespace)
				{
					$whitespace_count += $part->width();
				}
			}
		}
		return $whitespace_count;
	}

	/**
	 * Aligns token columns according to its set widths
	 * 
	 * @param   Token\Columns  $token
	 * @return  array
	 */
	public function align(Token\Columns $token)
	{
		$buffer = array();
		$column_space = $this->config->get_options('column_min_space');
		$this->_setup_alignment($token);
		for ($i = 0; $i < count($this->_columns); $i++)
		{
			$buffer[] = $this->_columns[$i];
			$overflow = $this->_actual_widths[$i];
			if (isset($this->_fixed_widths[$this->_cursor]))
			{
				$overflow -= $this->_fixed_widths[$this->_cursor];
			}
			// Overflow
			$this->_cursor++;
			while ($overflow > 0)
			{
				$this->_increment_count($overflow);
				if (isset($this->_fixed_widths[$this->_cursor]))
				{
					$overflow -= $this->_fixed_widths[$this->_cursor];
				}
				else
				{
					$overflow = $column_space;
				}
				$overflow -= $column_space;
				$this->_cursor++;
				$this->_increment_count($overflow);
			}
			// Underflow
			$buffer[] = $this->_create_whitespace(-1 * $overflow + $column_space);
		}
		array_pop($buffer);
		return $buffer;
	}

	private function _setup_alignment(Token\Columns $token)
	{
		$this->_columns = $token->columns();
		$this->_columns_count = count($this->_columns);
		$this->_actual_widths = $this->_get_actual_widths($this->_columns);
		$this->_fixed_widths = $token->widths();
		$this->_cursor = 0;
	}

	/**
	 * Increments columns count if the cursor has reached the end
	 */
	private function _increment_count($overflow)
	{
		if ($this->_cursor === $this->_columns_count)
		{
			$this->_columns_count++;
			$this->_fixed_widths[$this->_cursor] = $overflow - $this->config->get_options('column_min_space');
		}
	}

	/**
	 * Creates Whitespace token of the specified width
	 */
	private function _create_whitespace($width)
	{
		$whitespace = new Token\Whitespace($this->config);
		$whitespace->width($width);
		return $whitespace;
	}

	/**
	 * Return columns actual widths
	 */
	private function _get_actual_widths($columns)
	{
		$strings = $this->config->helper('string');
		$widths = array();
		foreach ($columns as $column)
		{
			$widths[] = $strings->strlen($column);
		}
		return $widths;
	}
}