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
	CodeGenerator\Math\SimpleOptimizer as Optimizer;

class ColumnsOptimizer extends \CodeGenerator\Object
{
	/**
	 * @var  array  Column-type tokens that will be aligned, other will be ignored (auto width)
	 */
	private $column_tokens = array();
	/**
	 * @var  array  Possible solutions space for the columns content widths (auto width)
	 */
	private $solutions_space = array();
	/**
	 * @var  array  Actual columns content widths (align)
	 */
	private $actual_widths;
	/**
	 * @var  array  Set column widths (align)
	 */
	private $fixed_widths;
	/**
	 * @var  array  Token columns (align)
	 */
	private $columns;
	/**
	 * @var  integer  Current columns count (align)
	 */
	private $columns_count;
	/**
	 * @var  integer  Current cursor (align)
	 */
	private $cursor;

	/**
	 * Compute the optimal column widths for the array of tokens
	 * 
	 * @param  array  Array of tokens to align
	 */
	public function auto_width($tokens)
	{
		$this->setup_auto_width($tokens);
		if (empty($this->solutions_space))
		{
			return;
		}
		$solver = new Optimizer(array($this, 'evaluate_parameters'), $this->solutions_space);
		$solution = $solver->execute();
		$best_params = reset($solution);
		foreach ($this->column_tokens as $token)
		{
			$token->set_widths($best_params);
		}
	}

	/**
	 * Determine and setup the actual column widths for all tokens as a pre-requisite for the alignment
	 */
	private function setup_auto_width($tokens)
	{
		if ( ! is_array($tokens))
		{
			throw new \InvalidArgumentException('Columns.tokens() takes an array as argument');
		}
		$this->column_tokens = array_values(array_filter($tokens, function($token) {
			return $token instanceof Token\Columns;
		}));
		$this->setup_solutions_space();
	}

	/**
	 * Setup possible solutions space
	 */
	private function setup_solutions_space()
	{
		$columns = array();
		for ($i = 0; $i < count($this->column_tokens); $i++)
		{
			$widths = $this->get_actual_widths($this->column_tokens[$i]->get_columns());
			for ($j = 0; $j < count($widths); $j++)
			{
				if ( ! isset($columns[$j])) {
					$columns[$j] = array();
				}
				$columns[$j][] = $widths[$j];
			}
		}

		$this->solutions_space = array();
		for ($i = 0; $i < count($columns); $i++)
		{
			$actual_values = $columns[$i];
			$this->solutions_space[$i] = array();
			$possible_widths = array_unique(array_filter($actual_values, function($item) {
				return (bool) $item;
			}));
			if ($i === count($this->column_tokens) - 1)
			{
				// Use max width for the last column
				$possible_widths = array(max($possible_widths));
			}
			foreach ($possible_widths as $possible_width)
			{
				$this->solutions_space[$i][] = $possible_width;
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
		foreach ($this->column_tokens as $token)
		{
			$token->set_widths($widths);
			foreach ($this->align($token) as $part)
			{
				if ($part instanceof Token\Whitespace)
				{
					$whitespace_count += $part->get_width();
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
		$this->setup_alignment($token);
		for ($i = 0; $i < count($this->columns); $i++)
		{
			$buffer[] = $this->columns[$i];
			$overflow = $this->actual_widths[$i];
			if (isset($this->fixed_widths[$this->cursor]))
			{
				$overflow -= $this->fixed_widths[$this->cursor];
			}
			// Overflow
			$this->cursor++;
			while ($overflow > 0)
			{
				$this->increment_count($overflow);
				if (isset($this->fixed_widths[$this->cursor]))
				{
					$overflow -= $this->fixed_widths[$this->cursor];
				}
				else
				{
					$overflow = $column_space;
				}
				$overflow -= $column_space;
				$this->cursor++;
				$this->increment_count($overflow);
			}
			// Underflow
			$buffer[] = $this->create_whitespace(-1 * $overflow + $column_space);
		}
		array_pop($buffer);
		return $buffer;
	}

	private function setup_alignment(Token\Columns $token)
	{
		$this->columns = $token->get_columns();
		$this->columns_count = count($this->columns);
		$this->actual_widths = $this->get_actual_widths($this->columns);
		$this->fixed_widths = $token->get_widths();
		$this->cursor = 0;
	}

	/**
	 * Increments columns count if the cursor has reached the end
	 */
	private function increment_count($overflow)
	{
		if ($this->cursor === $this->columns_count)
		{
			$this->columns_count++;
			$this->fixed_widths[$this->cursor] = $overflow - $this->config->get_options('column_min_space');
		}
	}

	/**
	 * Creates Whitespace token of the specified width
	 */
	private function create_whitespace($width)
	{
		return new Token\Whitespace($this->config, $width);
	}

	/**
	 * Return columns actual widths
	 */
	private function get_actual_widths($columns)
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