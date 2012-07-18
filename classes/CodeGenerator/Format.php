<?php
/**
 * Code Format config class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class Format
{
	/**
	 * @var  array  Default config values
	 */
	private $config = array(
		'format' => array(
			'column_delimiter' => ' ',
			'indent' => "\t",
		),
		'options' => array(
			'column_min_space' => 2,
			'line_width' => 100,
			'wrap_comment_text' => TRUE,
		),
	);
	/**
	 * @var  array  Helpers object cache
	 */
	private $helpers = array();

	/**
	 * Class constructor
	 * 
	 * @param  array  $config
	 */
	public function __construct($config = array())
	{
		$this->config = array_merge_recursive($this->config, $config);
	}

	/**
	 * Helpers getter/lazy initialization
	 * 
	 * @param   string  $name
	 * @reutrn  object
	 */
	public function helper($name)
	{
		if ( ! isset($this->helpers[$name]))
		{
			$classname = __NAMESPACE__.'\\Helper\\'.ucfirst($name);
			if ( !class_exists($classname))
			{
				throw new \InvalidArgumentException('There\'s no '.ucfirst($name).' helper');
			}
			$this->helpers[$name] = new $classname($this);
		}
		return $this->helpers[$name];
	}

	/**
	 * Format config getter/setter. See below for description and usage examples.
	 * 
	 * @return  mixed
	 */
	public function format()
	{
		return $this->_get_set('format', func_get_args());
	}

	/**
	 * Options config getter/setter. See below for description and usage examples.
	 * 
	 * @return  mixed
	 */
	public function options()
	{
		return $this->_get_set('options', func_get_args());
	}

	/**
	 * Universal getter and setter method for `format()` and `options()` methods. Takes 0 to 2 arguments.
	 * 
	 *     $format = $config->format();                   // Gets all 'format' options
	 *     $line_width = $config->options('line_width');  // Gets line width option
	 *     $config->format('indent', "\t"));              // Sets 'indent' format
	 * 
	 * @param   string  $property
	 * @param   array   $arguments
	 * @return  mixed
	 * @throws  \InvalidArgumentException
	 */
	private function _get_set($item, array $arguments)
	{
		$count = count($arguments);
		if ($count === 0)
		{
			return $this->config[$item];
		}
		elseif ($count === 1 AND array_key_exists($arguments[0], $this->config[$item]))
		{
			return $this->config[$item][$arguments[0]];
		}
		elseif ($count === 2 AND array_key_exists($arguments[0], $this->config[$item]))
		{
			$this->config[$item][$arguments[0]] = $arguments[1];
			return $this;
		}
		elseif ($count > 2)
		{
			throw new \InvalidArgumentException('Format.'.ucfirst($item).'() takes one or two arguments.');
		}
		throw new \InvalidArgumentException('Format.'.$item.'.'.$arguments[0].' does not exist');
	}

}