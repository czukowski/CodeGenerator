<?php
/**
 * Base class for tabular tokens
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

abstract class Columns extends Token
{
	/**
	 * @var  array  Column widths are stored here
	 */
	protected $widths = array();

	/**
	 * Returns all columns
	 * 
	 * @reutrn  array
	 */
	abstract public function get_columns();

	/**
	 * Gets column widths
	 * 
	 * @return  mixed
	 */
	public function get_widths()
	{
		return $this->widths;
	}

	/**
	 * @param   array   $widths
	 * @return  Columns
	 */
	public function set_widths($widths)
	{
		$this->assert_valid_widths($widths);
		$this->widths = $widths;
		return $this;
	}

	/**
	 * @param   array   $values
	 * @return  boolean
	 * @throws  \InvalidArgumentException
	 */
	private function assert_valid_widths($values)
	{
		if ( ! is_array($values))
		{
			throw new \InvalidArgumentException($this->token().'.widths() takes an array as argument');
		}
		foreach ($values as $value)
		{
			if ( ! is_int($value))
			{
				throw new \InvalidArgumentException($this->token().'.widths() argument must be array of integers');
			}
		}
	}

	/**
	 * @return  string
	 */
	protected function render_columns()
	{
		return implode('', $this->config->helper('columnsOptimizer')->align($this));
	}
}