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
	private $function;
	/**
	 * @var  array  Possible solutions space
	 */
	private $solutions_space;
	/**
	 * @var  array  Cursors
	 */
	private $cursors = array();
	/**
	 * @var  array  Array of the best found solutions
	 */
	private $best_parameters;

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
		$this->function = $function;
		$this->solutions_space = $space;
		$this->cursors = array_fill(0, count($space), 0);
	}

	/**
	 * Finds and returns best parameters
	 * 
	 * @return  array
	 */
	public function execute()
	{
		$best_solution = NULL;
		$this->best_parameters = array();
		do
		{
			$params = $this->get_next_solution();
			$value = call_user_func_array($this->function, $params);
			if ($best_solution === NULL OR $value <= $best_solution)
			{
				if ($value < $best_solution)
				{
					$this->best_parameters = array();
				}
				$best_solution = $value;
				$this->best_parameters[] = $params;
			}
		}
		while ($this->increment_cursor());
		// Return unique best parameters, do not sort
		return array_unique($this->best_parameters, 0);
	}

	/**
	 * Returns next possible solution to evaluate and increments the cursors
	 */
	private function get_next_solution()
	{
		$possible_solution = array();
		foreach ($this->cursors as $dim => $cursor)
		{
			$possible_solution[] = $this->solutions_space[$dim][$cursor];
		}
		return $possible_solution;
	}

	/**
	 * Increments cursors to eventually cover the whole solution space
	 */
	private function increment_cursor()
	{
		$dim = 0;
		while ($this->has_more_increments($dim))
		{
			$this->cursors[$dim] = 0;
			$dim++;
		}
		if ($this->has_more_dimensions($dim))
		{
			$this->cursors[$dim]++;
			return TRUE;
		}
	}

	/**
	 * Determine if the cursor may be incremented in this dimension
	 */
	private function has_more_increments($dim)
	{
		return $this->has_more_dimensions($dim)
			AND $this->cursors[$dim] === count($this->solutions_space[$dim]) - 1;
	}

	private function has_more_dimensions($dim)
	{
		return $dim < count($this->solutions_space);
	}
}