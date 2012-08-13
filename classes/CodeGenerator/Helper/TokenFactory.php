<?php
/**
 * Token factory class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenFactory extends \CodeGenerator\Singleton
{
	/**
	 * Creates a token and sets its attributes
	 * 
	 * @param  string  $type
	 * @param  array   $attributes
	 */
	public function create($type, $attributes = array())
	{
		$classname = '\CodeGenerator\Token\\'.ucfirst($type);
		if ( ! class_exists($classname))
		{
			throw new \InvalidArgumentException('Token '.ucfirst($type).' does not exist');
		}
		elseif ( ! $this->config->helper('arrays')->is_array($attributes))
		{
			throw new \InvalidArgumentException('TokenFactory.create() takes an array as the 2nd argument');
		}
		$token = new $classname($this->config);
		foreach ($attributes as $name => $value)
		{
			$token->set($name, $value);
		}
		return $token;
	}
}