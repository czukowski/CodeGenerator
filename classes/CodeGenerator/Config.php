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

class Config
{
	/**
	 * @var  array  Default config values
	 */
	private $config = array(
		'format' => array(
			'brace_close' => '}',
			'brace_open' => "\n{",
			'column_delimiter' => ' ',
			'indent' => "\t",
			'line_end' => "\n",
			'line_break' => "\r",
		),
		'options' => array(
			'charset' => 'utf-8',
			'column_min_space' => 2,
			'line_width' => 100,
			'char_length' => array(
				'x09' => 4,  // tab (\t) char
			),
			'names' => 'underscore',
			'word_wrap' => TRUE,
			'factory' => array(
				'aliases' => array(
					'Interface' => 'Type',
					'Class' => 'Type',
					'Function' => 'Method',
					'If' => 'Construct',
					'Elseif' => 'Construct',
					'Else' => 'Construct',
					'For' => 'Construct',
					'Foreach' => 'Construct',
					'While' => 'Construct',
					'Do' => 'Construct',
					'Switch' => 'Construct',
					'Case' => 'SwitchCase',
				),
				'attributes' => array(
					'Interface' => array(
						'type' => 'interface',
					),
					'If' => array(
						'type' => 'if',
					),
					'Elseif' => array(
						'type' => 'elseif',
					),
					'For' => array(
						'type' => 'for',
					),
					'Foreach' => array(
						'type' => 'foreach',
					),
					'While' => array(
						'type' => 'while',
					),
					'Do' => array(
						'type' => 'do',
					),
					'Switch' => array(
						'type' => 'switch',
					),
				),
				'transform' => array(
					'Block' => 'items',
					'DocComment' => 'text',
				),
			),
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
		$this->config = $this->helper('arrays')
			->merge($this->config, $config);
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
			$this->helpers[$name] = $this->create_helper_instance($name);
		}
		elseif ( ! $this->helpers[$name] instanceof Singleton)
		{
			return $this->create_helper_instance($name);
		}
		return $this->helpers[$name];
	}

	/**
	 * Creates helper instance
	 * 
	 * @param   string  $name
	 * @return  \CodeGenerator\Helper\*
	 * @throws  \InvalidArgumentException
	 */
	private function create_helper_instance($name)
	{
		$classname = __NAMESPACE__.'\\Helper\\'.ucfirst($name);
		if ( ! class_exists($classname))
		{
			throw new \InvalidArgumentException('There\'s no '.ucfirst($name).' helper');
		}
		return new $classname($this);
	}

	/**
	 * Gets config value(s)
	 * 
	 * @param   string  $path     Config path
	 * @param   mixed   $default  Default value
	 * @return  mixed
	 */
	public function get($path, $default = NULL)
	{
		return $this->helper('arrays')
			->path($this->config, $path, $default);
	}

	/**
	 * Format config getter
	 * 
	 * @param   string  $path     Config path
	 * @param   mixed   $default  Default value
	 * @return  mixed
	 */
	public function get_format($path, $default = NULL)
	{
		return $this->get('format.'.$path, $default);
	}

	/**
	 * Options config getter
	 * 
	 * @param   string  $path     Config path
	 * @param   mixed   $default  Default value
	 * @return  mixed
	 */
	public function get_options($path, $default = NULL)
	{
		return $this->get('options.'.$path, $default);
	}
}