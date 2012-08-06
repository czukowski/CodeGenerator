<?php
/**
 * Simple Optimizer class. Takes a function and possible solutions space, evaluates every possible solution
 * and returns those that give minimal function value.
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Math;

class SimpleOptimizer
{
	/**
	 * @var  callback  Evaluation function
	 */
	private $_function;
	/**
	 * @var  array  Possible solutions space
	 */
	private $_solutions_space;
	/**
	 * @var  array  Cursors
	 */
	private $_cursors = array();
	/**
	 * @var  array  Array of the best found solutions
	 */
	private $_best_parameters;

	/**
	 * @param   callback  $function    Callback function
	 * @param   mixed     $space       Array or Traversable object
	 * @throws  \InvalidArgumentException
	 */
	public function __construct($function, $space)
	{
		if ( ! is_callable($function) OR ! (is_array($space) OR $space instanceof \Traversable))
		{
			throw new \InvalidArgumentException('SimpleOptimizer.__construct() takes a callback and array as arguments');
		}
		if (($count = count($space)) === 0)
		{
			throw new \InvalidArgumentException('SimpleOptimizer.__construct() 2nd argument must be non-empty array');
		}
		foreach ($space as $params)
		{
			if ( ! is_array($params) || empty($params))
			{
				throw new \InvalidArgumentException('SimpleOptimizer.__construct() 2nd argument must be array of non-empty arrays');
			}
		}
		$this->_function = $function;
		$this->_solutions_space = $space;
		$this->_cursors = array_fill(0, count($space), 0);
	}

	/**
	 * Finds and returns best parameters
	 * 
	 * @return  array
	 */
	public function execute()
	{
		$best_solution = NULL;
		$this->_best_parameters = array();
		do
		{
			$params = $this->_next_solution();
			$value = call_user_func_array($this->_function, $params);
			if ($best_solution === NULL OR $value <= $best_solution)
			{
				if ($value < $best_solution)
				{
					$this->_best_parameters = array();
				}
				$best_solution = $value;
				$this->_best_parameters[] = $params;
			}
		}
		while ($this->_increment_cursor());
		// Return unique best parameters, do not sort
		return array_unique($this->_best_parameters, 0);
	}

	/**
	 * Returns next possible solution to evaluate and increments the cursors
	 */
	private function _next_solution()
	{
		$possible_solution = array();
		foreach ($this->_cursors as $dim => $cursor)
		{
			$possible_solution[] = $this->_solutions_space[$dim][$cursor];
		}
		return $possible_solution;
	}

	/**
	 * Increments cursors to eventually cover the whole solution space
	 */
	private function _increment_cursor()
	{
		$dim = 0;
		while ($this->_more_increments($dim))
		{
			$this->_cursors[$dim] = 0;
			$dim++;
		}
		if ($this->_more_dimensions($dim))
		{
			$this->_cursors[$dim]++;
			return TRUE;
		}
	}

	/**
	 * Determine if the cursor may be incremented in this dimension
	 */
	private function _more_increments($dim)
	{
		return $this->_more_dimensions($dim)
			AND $this->_cursors[$dim] === count($this->_solutions_space[$dim]) - 1;
	}

	private function _more_dimensions($dim)
	{
		return $dim < count($this->_solutions_space);
	}
}