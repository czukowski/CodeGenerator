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
	 * Token objects getter/setter
	 * 
	 * @return  mixed
	 */
	public function tokens()
	{
		if (func_num_args() === 0)
		{
			return $this->_all_tokens;
		}
		elseif (is_array(func_get_arg(0)))
		{
			$this->_all_tokens = func_get_arg(0);
			$this->_column_tokens = $this->_filter_column_tokens($this->_all_tokens);
			$this->_setup_tokens();
			return $this;
		}
		throw new \InvalidArgumentException('Columns.tokens() takes an array as argument');
	}

	/**
	 * Filters column-type tokens
	 * 
	 * @param   array  $tokens
	 * @return  array
	 */
	private function _filter_column_tokens($tokens)
	{
		return array_filter($tokens, function($token) {
			return $token instanceof Token\Columns;
		});
	}

	/**
	 * Determine and setup the actual column widths for all tokens as a pre-requisite for the alignment
	 */
	private function _setup_tokens()
	{
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
	 * Align token column widths
	 * 
	 * @return  Columns
	 */
	public function align()
	{
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
		return $this;
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
		$sum = 0;
		$actual_values = $this->_widths->get_column($column);
		for ($i = 0; $i < count($actual_values); $i++)
		{
			$sum += abs($actual_values[$i] - $width);
		}
		return $sum;
	}

	private function _get_column_parameters($column)
	{
		$actual_values = $this->_widths->get_column($column);
		$parameters = array();
		$possible_widths = array_unique(array_filter($actual_values, function($item) {
			return (bool) $item;
		}));
		rsort($possible_widths, SORT_NUMERIC);
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