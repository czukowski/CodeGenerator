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
	 * @return  \CodeGenerator\Token
	 */
	public function create($type, $attributes = array())
	{
		if ( ! $type OR ! is_string($type))
		{
			throw new \InvalidArgumentException('Invalid token type name or alias');
		}
		$classname = $this->get_classname($type);
		if ( ! $this->config->helper('arrays')->is_array($attributes))
		{
			throw new \InvalidArgumentException('TokenFactory.create() takes an array as the 2nd argument');
		}
		$token = new $classname($this->config);
		$attributes = $this->config->helper('arrays')->merge($attributes, $this->get_type_defaults($type));
		foreach ($attributes as $name => $value)
		{
			$token->set($name, $value);
		}
		return $token;
	}

	/**
	 * Return class name from the argument
	 */
	private function get_classname($type)
	{
		$classname = '\CodeGenerator\Token\\'.$this->get_type_by_alias($type);
		if ( ! class_exists($classname))
		{
			throw new \InvalidArgumentException('Token '.ucfirst($type).' does not exist');
		}
		return $classname;
	}

	/**
	 * Gets type from an alias in config, if any
	 */
	private function get_type_by_alias($alias)
	{
		return $this->config->get_options('factory.aliases.'.ucfirst($alias), ucfirst($alias));
	}

	/**
	 * Get type default attribute values in config
	 */
	private function get_type_defaults($type)
	{
		return $this->config->get_options('factory.attributes.'.$this->get_type_by_alias($type), array());
	}

	/**
	 * Transform strings into tokens
	 * 
	 * @param   string  $type
	 * @param   mixed   $object
	 * @return  \CodeGenerator\Token
	 */
	public function transform($type, $object)
	{
		if ( ! is_object($object))
		{
			$transform_attribute = $this->get_type_transform_attribute($type);
			$attributes = $transform_attribute ? array($transform_attribute => $object) : array();
			$object = $this->create($type, $attributes);
		}
		$classname = $this->get_classname($type);
		if ($object AND $object instanceof $classname)
		{
			return $object;
		}
		throw new \InvalidArgumentException('Cannot transform the object to '.ucfirst($type).' token');
	}

	/**
	 * Get type default attribute name for converting from non-object arguments
	 */
	private function get_type_transform_attribute($type)
	{
		return $this->config->get_options('factory.transform.'.$this->get_type_by_alias($type));
	}
}