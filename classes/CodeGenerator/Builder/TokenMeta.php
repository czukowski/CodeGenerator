<?php
/**
 * Token meta class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Builder;

abstract class TokenMeta
{
	private $name;
	private $attributes;

	/**
	 * @return  array
	 */
	public function get_attributes()
	{
		return $this->attributes;
	}

	/**
	 * @return  string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * @param  array  $attributes
	 */
	protected function set_attributes($attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * @param  string  $name
	 */
	protected function set_name($name)
	{
		$this->name = $name;
	}
}