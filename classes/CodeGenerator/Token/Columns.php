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
	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'widths' => array(),
		));
		$this->initialize_validation(array(
			'widths' => 'widths',
		));
	}

	/**
	 * Returns all columns
	 * 
	 * @reutrn  array
	 */
	abstract public function get_columns();

	/**
	 * @param   array   $values
	 * @return  boolean
	 */
	public function validate_widths($values)
	{
		if ( ! $this->config->helper('arrays')->is_array($values))
		{
			return FALSE;
		}
		foreach ($values as $value)
		{
			if ( ! is_int($value))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @return  string
	 */
	protected function render_columns()
	{
		return implode('', $this->config->helper('columnsOptimizer')->align($this));
	}
}