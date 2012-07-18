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

class FormatTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @dataProvider  provide_get
	 */
	public function test_format_get($config, $arguments, $expected)
	{
		$this->_test_get('format', $config, $arguments, $expected);
	}

	/**
	 * @dataProvider  provide_get
	 */
	public function test_option_get($config, $arguments, $expected)
	{
		$this->_test_get('options', $config, $arguments, $expected);
	}

	private function _test_get($method, $config, array $arguments, $expected)
	{
		$actual = $this->_test_accessor_method($method, $config, $arguments, $expected);
		if (is_array($expected))
		{
			foreach ($expected as $key => $value)
			{
				$this->assertEquals($value, $actual[$key]);
			}
		}
		else
		{
			$this->assertEquals($expected, $actual);
		}
	}

	/**
	 * @dataProvider  provide_set
	 */
	public function test_format_set($config, $arguments, $expected)
	{
		$this->_test_set('format', $config, $arguments, $expected);
	}

	/**
	 * @dataProvider  provide_set
	 */
	public function test_option_set($config, $arguments, $expected)
	{
		$this->_test_set('options', $config, $arguments, $expected);
	}

	private function _test_set($method, $config, array $arguments, $expected)
	{
		$instance = $this->_test_accessor_method($method, $config, $arguments, $expected);
		$this->assertSame($this->object, $instance);
		$actual = $this->object->$method();
		foreach ($expected as $key => $value)
		{
			$this->assertEquals($value, $actual[$key]);
		}
	}

	private function _test_accessor_method($method_name, $config, array $arguments, $expected)
	{
		$this->setup_object(array(
			'arguments' => array(array($method_name => $config)),
		));
		$this->setExpectedExceptionFromArgument($expected);
		$method = new \ReflectionMethod($this->object, $method_name);
		return $method->invokeArgs($this->object, $arguments);
	}

	public function provide_set()
	{
		// [config, arguments, expected]
		return array(
			array(array(), array('key1', 'value1'), new \InvalidArgumentException),
			array(array('key1' => 'VALUE1'), array('key1', 'value1'), array('key1' => 'value1')),
		);
	}

	public function provide_get()
	{
		// [config, arguments, expected]
		return array(
			array(array(), array('key1'), new \InvalidArgumentException),
			array(array(), array('key1', 'value1', 'something else'), new \InvalidArgumentException),
			array(array(), array(), array()),
			array(array('key1' => 'VALUE1'), array(), array('key1' => 'VALUE1')),
			array(array('key1' => 'VALUE1'), array('key1'), 'VALUE1'),
		);
	}
}