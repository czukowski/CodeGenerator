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
	 * @var  Matrix  Actual column widths for each token (auto width)
	 */
	private $_widths = array();
	/**
	 * @var  array  Column-type tokens that will be aligned, other will be ignored (auto width)
	 */
	private $_tokens = array();
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
		for ($i = 0; $i < $this->_widths->get_dimension(0); $i++)
		{
			$params = $this->_get_column_parameters($i);
			$solver = new Optimizer(array($this, 'evaluate_parameters'), $params);
			$solution = $solver->execute();
			$best_params = reset($solution);
			foreach ($this->_tokens as $token)
			{
				$token->widths($i, $best_params['width']);
			}
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
		$this->_tokens = $this->_filter_column_tokens($tokens);
		$this->_widths = $this->_create_widths_matrix($this->_tokens);
	}

	/**
	 * Filters column-type tokens
	 */
	private function _filter_column_tokens($tokens)
	{
		return array_filter($tokens, function($token) {
			return $token instanceof Token\Columns;
		});
	}

	/**
	 * Creates actual widths matrix
	 */
	private function _create_widths_matrix($tokens)
	{
		$matrix = array();
		foreach ($tokens as $token)
		{
			$matrix[] = $this->_get_actual_widths($token->columns());
		}
		return new Matrix($matrix);
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

	/**
	 * Get possible solutions for column widths
	 */
	private function _get_column_parameters($column)
	{
		$actual_values = $this->_widths->get_column($column);
		$parameters = array();
		$possible_widths = array_unique(array_filter($actual_values, function($item) {
			return (bool) $item;
		}));
		if ($column === $this->_widths->get_dimension(1) - 1)
		{
			// Use max width for the last column
			$possible_widths = array(max($possible_widths));
		}
		foreach ($possible_widths as $possible_width)
		{
			$parameters[] = array(
				'column' => $column,
				'width' => $possible_width,
			);
		}
		return $parameters;
	}

	/**
	 * Evaluates a 'fitness' of a set width for a column
	 * 
	 * @param   integer  $column
	 * @param   integer  $width
	 * @return  float
	 */
	public function evaluate_parameters($column, $width)
	{
		$pad = $truncate = 0;
		$widths = array_filter($this->_widths->get_column($column));
		$count = count($widths);
		for ($i = 0; $i < $count; $i++)
		{
			$delta = $widths[$i] - $width;
			if ($delta > 0)
			{
				$pad += $delta;
			}
			if ($delta < 0)
			{
				$truncate -= $delta;
			}
		}
		return $this->_sigma($pad, $count, $count + $truncate + $pad + 1, 1, 0) * $pad
			+ $this->_sigma($truncate, $count, $count + $truncate + $pad + 1, 0, 1) * $truncate;
	}

	/**
	 * Sigma function _/¯
	 */
	private function _sigma($argument, $x1, $x2, $y1, $y2)
	{
		$value = ($y2 - $y1) / ($x2 - $x1) * ($argument - $x1) + $y1;
		if ($y2 > $y1)
		{
			return max($y1, min($y2, $value));
		}
		else
		{
			return min($y1, max($y2, $value));
		}
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
		$column_space = $this->config->options('column_min_space');
		$this->_setup_alignment($token);
		for ($i = 0; $i < count($this->_columns); $i++)
		{
			$buffer[] = $this->_columns[$i];
			$overflow = $this->_actual_widths[$i] - $this->_fixed_widths[$this->_cursor];
			// Overflow
			$this->_cursor++;
			while ($overflow > 0)
			{
				$this->_increment_count($overflow);
				$overflow -= $this->_fixed_widths[$this->_cursor] + $column_space;
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
			$this->_fixed_widths[] = $overflow - $this->config->options('column_min_space');
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
}