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
	private $config = array(
		'indent' => "\t",
	);

	public function __construct($config = array())
	{
		$this->config += $config;
	}

	public function __get($key)
	{
		if (array_key_exists($key, $this->config))
		{
			return $this->config[$key];
		}
		throw new \OutOfBoundsException('"'.$key.'" not found');
	}

	public function __set($key, $value)
	{
		$this->config[$key] = $value;
	}
}