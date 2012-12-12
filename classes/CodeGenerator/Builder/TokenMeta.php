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

class TokenMeta
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
	public function set_attributes($attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * @param  string  $name
	 */
	public function set_name($name)
	{
		$this->name = $name;
	}
}