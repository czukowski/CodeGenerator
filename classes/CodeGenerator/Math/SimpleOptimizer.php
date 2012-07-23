<?php
/**
 * Simple Optimizer class. Takes a function and possible solutions, returns solutions
 * which give minimal function value.
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
	 * @var  array  Parameters for the possible solutions
	 */
	private $_parameters;
	/**
	 * @var  array  Array of the best found solutions
	 */
	private $_best_parameters;

	/**
	 * @param   callback  $function    Callback function
	 * @param   mixed     $parameters  Array or Traversable object
	 * @throws  \InvalidArgumentException
	 */
	public function __construct($function, $parameters)
	{
		if ( ! is_callable($function) OR ! (is_array($parameters) OR $parameters instanceof \Traversable))
		{
			throw new \InvalidArgumentException('SimpleOptimizer.__construct() takes a callback and array as arguments');
		}
		foreach ($parameters as $params)
		{
			if ( ! is_array($params))
			{
				throw new \InvalidArgumentException('SimpleOptimizer.__construct() 2nd argument must be array of arrays');
			}
		}
		$this->_function = $function;
		$this->_parameters = $parameters;
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
		foreach ($this->_parameters as $index => $params)
		{
			$value = call_user_func_array($this->_function, $params);
			if ($best_solution === NULL OR $value <= $best_solution)
			{
				if ($value < $best_solution)
				{
					$this->_best_parameters = array();
				}
				$best_solution = $value;
				$this->_best_parameters[$index] = $params;
			}
		}
		return $this->_best_parameters;
	}
}