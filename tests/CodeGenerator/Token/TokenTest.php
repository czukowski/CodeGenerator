<?php
/**
 * TokenTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class TokenTest extends Testcase
{
	/**
	 * @dataProvider  provide_add
	 */
	public function test_add($validation, $attributes, $add_key, $add_value, $expected)
	{
		$this->setup_with_attributes($attributes, $validation);
		$this->setExpectedExceptionFromArgument($expected);
		$this->assertSame($this->object, $this->object->add($add_key, $add_value));
		$this->assertEquals($expected, $this->get_attribute_value($add_key));
	}

	public function provide_add()
	{
		// [validation, attributes, add_key, add_value, expected]
		return array(
			array(
				array(), array('me' => array('foo')), 'me', 'bar', 'me' => array('foo', 'bar'),
			),
			array(
				array(), array('me' => array('foo')), 'me', NULL, 'me' => array('foo', NULL),
			),
			array(
				array(), array('me' => array('foo')), 'him', 'bar', new \InvalidArgumentException,
			),
			array(
				array(), array('me' => 'foo'), 'me', 'fubar', new \InvalidArgumentException,
			),
			array(
				array('validate_me' => TRUE), array('me' => array('foo')), 'me', 'bar', array('foo', 'bar'),
			),
			array(
				array('validate_me' => FALSE), array('me' => array('foo')), 'me', 'bar', new \InvalidArgumentException,
			),
		);
	}

	/**
	 * @dataProvider  provide_get
	 */
	public function test_get($attributes, $get_key, $expected)
	{
		$this->setup_with_attributes($attributes);
		$this->setExpectedExceptionFromArgument($expected);
		$this->assertEquals($expected, $this->object->get($get_key));
	}

	public function provide_get()
	{
		// [attributes, key, expected_get]
		return array(
			array(
				array('foo' => 'bar'), 'foo', 'bar',
			),
			array(
				array('foo' => 'bar', 'rab' => 'oof'), 'foo', 'bar',
			),
			array(
				array('foo' => 'bar'), 'boo', new \InvalidArgumentException,
			),
		);
	}

	/**
	 * @dataProvider  provide_set
	 */
	public function test_set($validation, $attributes, $set_key, $set_value, $expected)
	{
		$this->setup_with_attributes($attributes, $validation);
		$this->setExpectedExceptionFromArgument($expected);
		$this->assertSame($this->object, $this->object->set($set_key, $set_value));
		$this->assertEquals($expected, $this->get_attribute_value($set_key));
	}

	public function provide_set()
	{
		// [validation, attributes, key, set_value, expected_set]
		return array(
			array(
				array(), array('foo' => 'bar'), 'foo', 'bar', 'bar',
			),
			array(
				array(), array('foo' => 'bar', 'rab' => 'oof'), 'foo', '123', '123',
			),
			array(
				array(), array('foo' => 'bar'), 'boo', 'bar', new \InvalidArgumentException,
			),
			array(
				array('validate_foo' => TRUE), array('foo' => NULL), 'foo', 'bar', 'bar',
			),
			array(
				array('validate_foo' => FALSE), array('foo' => NULL), 'foo', 'bar', new \InvalidArgumentException,
			),
		);
	}

	protected function setup_with_attributes($attributes, $validation = array())
	{
		$options = $validation ? array('validate_methods' => $validation) : array();
		$this->setup_mock($options);
		$this->_object_property($this->object, 'attributes')
			->setValue($this->object, $attributes);
	}

	protected function setup_mock($options = array())
	{
		if (array_key_exists('validate_methods', $options))
		{
			$classname = isset($options['classname']) ? $options['classname'] : $this->_class_name();
			$methods = isset($options['methods']) ? $options['methods'] : $this->_class_abstract_methods($classname);
			$options['methods'] = array_merge($methods, array_keys($options['validate_methods']));
		}
		parent::setup_mock($options);
		if (array_key_exists('validate_methods', $options))
		{
			foreach ($options['validate_methods'] as $method => $return_value)
			{
				$this->object->expects($this->any())
					->method($method)
					->will($this->returnValue($return_value));
			}
		}
	}

	private function get_attribute_value($key)
	{
		$attributes = $this->_object_property($this->object, 'attributes')
			->getValue($this->object);
		$this->assertArrayHasKey($key, $attributes);
		return $attributes[$key];
	}

	/**
	 * @dataProvider  provide_indent
	 */
	public function test_indent($set_value, $expected)
	{
		$this->setup_mock();
		$this->assertEquals(0, $this->object->indent());
		$this->setExpectedExceptionFromArgument($expected);
		$this->assertSame($this->object, $this->object->indent($set_value));
		$this->assertEquals($expected, $this->object->indent());
	}

	public function provide_indent()
	{
		return array(
			array(0, 0),
			array(10, 10),
			array(NULL, new \InvalidArgumentException),
			array(FALSE, new \InvalidArgumentException),
			array('', new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_token
	 */
	public function test_token($mock_classname, $expected)
	{
		$this->setup_mock(array(
			'mock_classname' => $mock_classname,
		));
		$this->assertEquals($expected, $this->object->token());
	}

	public function provide_token()
	{
		$timestamp = time();
		return array(
			array('Mock_'.$timestamp, 'Mock_'.$timestamp),
		);
	}

	public function test_render()
	{
		$this->setup_mock();
		$this->object->expects($this->any())
			->method('render')
			->will($this->returnValue($this->_class_name()));
		$this->assertEquals($this->_class_name(), (string) $this->object);
	}
}