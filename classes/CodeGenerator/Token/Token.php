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

abstract class Token extends \CodeGenerator\Object
{
	/**
	 * @var  array  Token attributes list with default values
	 */
	private $attributes = array();
	/**
	 * @var  array  List of array attributes
	 */
	private $array_atributes = array();
	/**
	 * @var  array  Token attributes to validation mapping
	 */
	private $validation = array();
	/**
	 * @var  array  Token transform settings
	 */
	protected $transform = array();

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
		$this->set_parent($attribute, $value);
		$this->attributes[$attribute][] = $value;
		$this->assert_attribute_valid($attribute, $this->attributes[$attribute]);
		return $this;
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
		if ($this->is_attribute_array($attribute))
		{
			if ( ! $this->config->helper('arrays')->is_array($value) OR $value instanceof Block)
			{
				$value = array($value);
			}
			foreach ($value as $item)
			{
				$this->set_parent($attribute, $item);
			}
		}
		else
		{
			$this->set_parent($attribute, $value);
		}
		$this->attributes[$attribute] = $this->transform_attribute($attribute, $value);
		return $this;
	}

	/**
	 * Tests attribute exists in the token
	 * 
	 * @param   string   $attribute
	 * @return  boolean
	 */
	public function has($attribute)
	{
		return array_key_exists($attribute, $this->attributes);
	}

	/**
	 * Transforms attribute to object if needed
	 * 
	 * @param  string  $attribute
	 */
	protected function transform_attribute($attribute, $value)
	{
		if ( ! isset($this->transform[$attribute]))
		{
			return $value;
		}
		return $this->config->helper('tokenFactory')
			->transform($this->transform[$attribute], $value, $this);
	}

	/**
	 * Asserts that token attribute exists
	 * 
	 * @param   string  $attribute
	 * @throws  \InvalidArgumentException
	 */
	private function assert_attribute_exists($attribute)
	{
		if ( ! $this->has($attribute))
		{
			throw new \InvalidArgumentException($this->get_type().'.'.$attribute.' does not exist');
		}
	}

	/**
	 * @param   string   $attribute
	 * @return  boolean
	 */
	private function is_attribute_array($attribute)
	{
		return in_array($attribute, $this->array_atributes);
	}

	/**
	 * Asserts that token attribute is array
	 * 
	 * @param   string  $attribute
	 * @throws  \InvalidArgumentException
	 */
	private function assert_attribute_array($attribute)
	{
		if ( ! $this->is_attribute_array($attribute))
		{
			throw new \InvalidArgumentException($this->get_type().'.'.$attribute.' is not array');
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
		if ( ! isset($this->validation[$attribute]))
		{
			return;
		}
		$method_name = 'validate_'.$this->validation[$attribute];
		if (is_callable(array($this, $method_name)))
		{
			$validator = $this;
		}
		elseif (is_callable(array($this->config->helper('validator'), $method_name)))
		{
			$validator = $this->config->helper('validator');
		}
		if (isset($validator) AND ! $validator->$method_name($value))
		{
			throw new \InvalidArgumentException('Invalid value for '.$this->get_type().'.'.$attribute);
		}
	}

	/**
	 * Sets token parent attribute
	 */
	private function set_parent($attribute, $item)
	{
		if ($attribute !== 'parent' AND $item instanceof self)
		{
			$item->set('parent', $this);
		}
	}

	/**
	 * Returns the current class name without namespace as a token name
	 * 
	 * @return  string
	 */
	public function get_type()
	{
		return preg_replace('#^([a-z0-9]\\\\)+#i', '', get_class($this));
	}

	/**
	 * Token render method. Used by `__toString()`, therefore must not throw exceptions.
	 * 
	 * @return  string
	 */
	abstract public function render();

	/**
	 * Called before rendering, used to finalize attribute transformations where necessary.
	 */
	protected function before_render()
	{}

	/**
	 * If attribute is set, returns its name, else NULL
	 */
	protected function render_boolean_attribute($attribute)
	{
		if ($this->get($attribute) === TRUE)
		{
			return $attribute;
		}
	}

	/**
	 * @param  \CodeGenerator\Config  $config
	 */
	public function __construct(\CodeGenerator\Config $config)
	{
		parent::__construct($config);
		$this->initialize();
	}

	/**
	 * Constructor calls this function for descendant classes to initialize attributes and validations
	 */
	protected function initialize()
	{
		$this->initialize_attributes(array(
			'parent' => NULL,
		));
		$this->initialize_validation(array(
			'parent' => 'parent_token',
		));
	}

	/**
	 * Merges the current attributes list with the supplied values; to be used in `initialize()` method
	 */
	protected function initialize_attributes($attributes)
	{
		foreach ($attributes as $name => $default_value)
		{
			$this->attributes[$name] = $this->transform_attribute($name, $default_value);
			if (is_array($default_value))
			{
				$this->array_atributes[] = $name;
			}
		}
	}

	/**
	 * Merges the current validation rules list with the supplied values; to be used in `initialize()` method
	 */
	protected function initialize_validation($validation)
	{
		foreach ($validation as $attribute => $rule)
		{
			$this->validation[$attribute] = $rule;
		}
	}

	/**
	 * Validate token to be set as parent - may be NULL or another token
	 */
	public function validate_parent_token($value)
	{
		return ($value === NULL OR ($value instanceof self AND $value !== $this));
	}

	/**
	 * Token string cast method
	 * 
	 * @return  string
	 */
	public function __toString()
	{
		$this->before_render();
		return $this->render();
	}
}