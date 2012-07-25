<?php
/**
 * Helper class for column tokens that allows to auto-align them (set column widths automatically).
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
	 * @var  Matrix  Actual column widths for each token
	 */
	private $_widths = array();
	/**
	 * @var  array  All tokens passed to the object
	 */
	private $_all_tokens = array();
	/**
	 * @var  array  Column-type tokens that will be aligned, other will be ignored
	 */
	private $_column_tokens = array();

	/**
	 * Align token column widths
	 * 
	 * @param  array  Array of tokens to align
	 */
	public function align($tokens)
	{
		$this->_setup_tokens($tokens);
		for ($i = 0; $i < $this->_widths->get_dimension(0); $i++)
		{
			$params = $this->_get_column_parameters($i);
			$solver = new Optimizer(array($this, 'evaluate_parameters'), $params);
			$solution = $solver->execute();
			$best_params = reset($solution);
			foreach ($this->_column_tokens as $token)
			{
				$token->widths($i, $best_params['width']);
			}
		}
	}

	/**
	 * Determine and setup the actual column widths for all tokens as a pre-requisite for the alignment
	 */
	private function _setup_tokens($tokens)
	{
		if ( ! is_array($tokens))
		{
			throw new \InvalidArgumentException('Columns.tokens() takes an array as argument');
		}
		$this->_all_tokens = func_get_arg(0);
		$this->_column_tokens = $this->_filter_column_tokens($this->_all_tokens);
		$matrix = array();
		foreach ($this->_column_tokens as $token)
		{
			$widths = array();
			foreach ($token->columns() as $column)
			{
				$widths[] = $this->config->helper('string')
					->strlen($column);
			}
			$matrix[] = $widths;
		}
		$this->_widths = new Matrix($matrix);
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
}