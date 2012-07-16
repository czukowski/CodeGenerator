<?php
/**
 * Code Token class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;
use CodeGenerator\Format;

abstract class Token {

	protected $format;
	protected $indent = 0;
	protected $attributes = array();

	/**
	 * Class constructor
	 * 
	 * @param  Format  $format
	 */
	public function __construct(Format $format)
	{
		$this->format = $format;
	}

	/**
	 * Add a value to array token attribute
	 * 
	 * @param   string  $attribute
	 * @param   mixed   $value
	 * @return  Token
	 */
	public function add($attribute, $value)
	{
		$this->assert_attribute_exists($attribute);
		$this->assert_attribute_array($attribute);
		return $this->set($attribute, array_merge($this->attributes[$attribute], array($value)));
	}

	/**
	 * Get token attribute
	 * 
	 * @param   string  $attribute
	 * @return  mixed
	 */
	public function get($attribute)
	{
		$this->assert_attribute_exists($attribute);
		return $this->attributes[$attribute];
	}

	/**
	 * Set token attribute
	 * 
	 * @param   string  $attribute
	 * @param   mixed   $value
	 * @return  Token
	 */
	public function set($attribute, $value)
	{
		$this->assert_attribute_exists($attribute);
		$this->assert_attribute_valid($attribute, $value);
		$this->attributes[$attribute] = $value;
		return $this;
	}

	/**
	 * Asserts that token attribute exists
	 * 
	 * @param   string  $attribute
	 * @throws  \InvalidArgumentException
	 */
	private function assert_attribute_exists($attribute)
	{
		if ( ! array_key_exists($attribute, $this->attributes))
		{
			throw new \InvalidArgumentException($this->token().'.'.$attribute.' does not exist');
		}
	}

	/**
	 * Asserts that token attribute is array
	 * 
	 * @param   string  $attribute
	 * @throws  \InvalidArgumentException
	 */
	private function assert_attribute_array($attribute)
	{
		if ( ! is_array($this->attributes[$attribute]))
		{
			throw new \InvalidArgumentException($this->token().'.'.$attribute.' is not array');
		}
	}

	/**
	 * Asserts that token attribute value is valid
	 * 
	 * @param   string  $attribute
	 * @param   mixed   $value
	 * @throws  \InvalidArgumentException
	 */
	private function assert_attribute_valid($attribute, $value)
	{
		$method_name = 'validate_'.$attribute;
		if (method_exists($this, $method_name) AND ! $this->$method_name())
		{
			throw new \InvalidArgumentException('Invalid value for '.$this->token().'.'.$attribute);
		}
	}

	/**
	 * Gets or sets token base indentation
	 * 
	 * @param   integer  $level
	 * @return  Token
	 * @throws  \InvalidArgumentException
	 */
	public function indent($level = NULL)
	{
		if (func_num_args() === 0)
		{
			return $this->indent;
		}
		elseif (is_int($level))
		{
			$this->indent = $level;
			return $this;
		}
		throw new \InvalidArgumentException('Indentation must be integer');
	}

	/**
	 * Returns the current class name without namespace as a token name
	 * 
	 * @return  string
	 */
	public function token()
	{
		return preg_replace('#^([a-z0-9]\\\\)+#i', '', get_class($this));
	}

	/**
	 * Token render method
	 * 
	 * @return  string
	 */
	abstract public function render();

	/**
	 * Token string cast method
	 * 
	 * @return  string
	 */
	public function __toString()
	{
		return $this->render();
	}
}