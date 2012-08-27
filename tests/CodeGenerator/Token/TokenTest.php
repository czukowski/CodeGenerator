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
		$this->setup_with_validator_helper($attributes, $validation);
		$this->set_expected_exception_from_argument($expected);
		$this->assertSame($this->object, $this->object->add($add_key, $add_value));
		$this->assertEquals($expected, $this->object->get($add_key));
		$this->assert_parent_token($add_value);
	}

	public function provide_add()
	{
		$block_token = $this->get_config()
			->helper('tokenFactory')
			->create('Block');
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
				array(), array('token' => array()), 'token', $block_token, array($block_token),
			),
			array(
				array('me' => TRUE), array('me' => array('foo')), 'me', 'bar', array('foo', 'bar'),
			),
			array(
				array('me' => FALSE), array('me' => array('foo')), 'me', 'bar', new \InvalidArgumentException,
			),
		);
	}

	/**
	 * @dataProvider  provide_get
	 */
	public function test_get($attributes, $get_key, $expected)
	{
		$this->setup_with_validator_helper($attributes);
		$this->set_expected_exception_from_argument($expected);
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
		$this->setup_with_validator_methods($attributes, $validation);
		$this->set_expected_exception_from_argument($expected);
		$this->assertSame($this->object, $this->object->set($set_key, $set_value));
		$this->assertEquals($expected, $this->object->get($set_key));
		$this->assert_parent_token($set_value);
	}

	public function provide_set()
	{
		$comment_token = $this->get_config()
			->helper('tokenFactory')
			->create('DocComment');
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
				array(), array('token' => NULL), 'token', $comment_token, $comment_token,
			),
			array(
				array('foo' => TRUE), array('foo' => NULL), 'foo', 'bar', 'bar',
			),
			array(
				array('foo' => FALSE), array('foo' => NULL), 'foo', 'bar', new \InvalidArgumentException,
			),
		);
	}

	private function assert_parent_token($token)
	{
		if ($token instanceof Token)
		{
			$this->assertSame($this->object, $token->get('parent'));
		}
	}

	protected function setup_with_validator_methods($attributes, $validation_methods = array())
	{
		$this->setup_mock(array(
			'methods' => array_merge($this->get_class_abstract_methods($this->get_class_name()), $this->get_validation_methods($validation_methods)),
		));
		$this->setup_validator($this->object, $validation_methods);
		$this->setup_properties($attributes, $validation_methods);
	}

	protected function setup_with_validator_helper($attributes, $validation_methods = array())
	{
		$this->setup_mock();
		if ($validation_methods)
		{
			$validator = $this->get_mock(array(
				'classname' => '\CodeGenerator\Helper\Validator',
				'methods' => $this->get_validation_methods($validation_methods),
				'arguments' => array($this->config),
			));
			$this->setup_validator($validator, $validation_methods);
		}
		$this->setup_properties($attributes, $validation_methods);
	}

	private function get_validation_methods($validations)
	{
		return array_map(function($name) {
			return 'validate_'.$name;
		}, array_keys($validations));
	}

	protected function setup_properties($attributes, $validations)
	{
		$this->get_object_method($this->object, 'initialize_attributes')
			->invoke($this->object, $attributes);
		if ($validations)
		{
			$this->get_object_method($this->object, 'initialize_validation')
				->invoke($this->object, array_combine(array_keys($validations), array_keys($validations)));
		}
	}

	protected function setup_validator($validator, $validation_methods)
	{
		foreach ($validation_methods as $method => $return_value)
		{
			$validator->expects($this->any())
				->method('validate_'.$method)
				->will($this->returnValue($return_value));
		}
		$this->replace_helper('validator', $validator);
	}

	/**
	 * @expectedException  InvalidArgumentException
	 */
	public function test_set_parent_self()
	{
		$this->setup_mock();
		$this->object->set('parent', $this->object);
	}

	/**
	 * @dataProvider  provide_parent
	 */
	public function test_set_parent($parent)
	{
		$this->setup_mock();
		$this->set_expected_exception_from_argument($parent);
		$this->object->set('parent', $parent);
		$this->assertEquals($parent, $this->object->get('parent'));
	}

	public function provide_parent()
	{
		return array(
			array(
				$this->get_config()
					->helper('tokenFactory')
					->create('Block'),
			),
		);
	}

	/**
	 * @dataProvider  provide_get_type
	 */
	public function test_get_type($mock_classname, $expected)
	{
		$this->setup_mock(array(
			'mock_classname' => $mock_classname,
		));
		$this->assertEquals($expected, $this->object->get_type());
	}

	public function provide_get_type()
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
			->will($this->returnValue($this->get_class_name()));
		$this->assertEquals($this->get_class_name(), (string) $this->object);
	}
}