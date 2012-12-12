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
	 * Class constructor.
	 * 
	 * @param   string  $token_name
	 * @param   array   $attributes
	 */
	public function __construct($token_name, array $attributes)
	{
		$this->name = $token_name;
		$this->attributes = $attributes;
	}

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
}