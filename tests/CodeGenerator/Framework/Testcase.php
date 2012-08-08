<?php
/**
 * Base testcase class for PHPUnit
 * 
 * @package    CodeGenerator
 * @category   Framework
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Framework;

abstract class Testcase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var  object  Tested object instance
	 */
	protected $object;

	protected function set_expected_exception_from_argument($expected)
	{
		if ($expected instanceof \Exception)
		{
			$this->setExpectedException(get_class($expected));
		}
	}

	protected function setup_object($options = array())
	{
		$options = $this->get_setup_options($options);
		$class = $this->get_class_reflection($options['classname']);
        $this->object = $class->newInstanceArgs($options['arguments']);
	}

	protected function setup_mock($options = array())
	{
		$this->object = $this->get_mock($options);
	}

	protected function get_mock($options = array())
	{
		$options = $this->get_setup_options($options);
		return $this->getMock($options['classname'], $options['methods'], $options['arguments'], $options['mock_classname']);
	}

	private function get_setup_options($options = array())
	{
		$options['classname'] = isset($options['classname']) ? $options['classname'] : $this->get_class_name();
		$options['methods'] = isset($options['methods']) ? $options['methods'] : $this->get_class_abstract_methods($options['classname']);
		$options['arguments'] = isset($options['arguments']) ? $options['arguments'] : $this->get_class_constructor_arguments();
		$options['mock_classname'] = isset($options['mock_classname']) ? $options['mock_classname'] : '';
		return $options;
	}

	protected function get_class_abstract_methods($classname)
	{
		$methods = array();
		foreach ($this->get_class_reflection($classname)->getMethods(\ReflectionMethod::IS_ABSTRACT) as $method)
		{
			$methods[] = $method->getName();
		}
		return $methods;
	}

	protected function get_class_constructor_arguments()
	{
		// No arguments by default
		return array();
	}

	protected function get_class_name()
	{
		return preg_replace('#Test$#', '', get_class($this));
	}

	protected function get_class_reflection($classname)
	{
		return new \ReflectionClass($classname);
	}

	protected function get_object_method($object, $name)
	{
		$method = new \ReflectionMethod($object, $name);
		$method->setAccessible(TRUE);
		return $method;
	}

	protected function get_object_property($object, $name)
	{
		$property = new \ReflectionProperty($object, $name);
		$property->setAccessible(TRUE);
		return $property;
	}
}