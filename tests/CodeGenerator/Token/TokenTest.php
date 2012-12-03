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
		$token_1 = $this->get_config()
			->helper('tokenFactory')
			->create('DocComment');
		$token_2 = $this->get_config()
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
				array(), array('token' => NULL), 'token', $token_1, $token_1,
			),
			array(
				array(), array('ts' => array()), 'ts', array($token_1, $token_2), array($token_1, $token_2),
			),
			array(
				array(), array('token' => array()), 'token', $token_2, array($token_2),
			),
			array(
				array('foo' => TRUE), array('foo' => NULL), 'foo', 'bar', 'bar',
			),
			array(
				array('foo' => FALSE), array('foo' => NULL), 'foo', 'bar', new \InvalidArgumentException,
			),
		);
	}

	/**
	 * @dataProvider  provide_has
	 */
	public function test_has($attributes, $has_key, $expected)
	{
		$this->setup_with_validator_helper($attributes);
		$actual = $this->object->has($has_key);
		$this->assertEquals($expected, $actual);
	}

	public function provide_has()
	{
		// [attributes, key, set_value, expected_set]
		return array(
			array(
				array('foo' => 'bar'), 'foo', TRUE,
			),
			array(
				array('foo' => 'bar', 'rab' => 'oof'), 'foo', TRUE,
			),
			array(
				array('foo' => 'bar'), 'bar', FALSE,
			),
			array(
				array('foo' => NULL), 'foo', TRUE,
			),
			array(
				array('foo' => NULL), 'bar', FALSE,
			),
		);
	}

	private function assert_parent_token($tokens)
	{
		foreach ( (array) $tokens as $token)
		{
			if ($token instanceof Token)
			{
				$this->assertSame($this->object, $token->get('parent'));
			}
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
	 * @dataProvider  provide_get_attributes
	 */
	public function test_get_attributes($attributes, $expected)
	{
		$this->setup_with_validator_helper($attributes);
		$this->assertEquals($expected, $this->object->get_attributes());
	}

	public function provide_get_attributes()
	{
		return array(
			array(
				array(), array(),
			),
			array(
				array('foo' => 'bar'), array('foo'),
			),
			array(
				array('foo' => 'bar', 'rab' => 'oof'), array('foo', 'rab'),
			)
		);
	}

	/**
	 * @dataProvider  provide_get_children
	 */
	public function test_get_children($token, $count, $expected)
	{
		$sample = $this->get_sample_factory()
			->get_sample();
		$actual = $sample[$token]->get_children();
		$this->assertInternalType('array', $actual);
		$this->assertEquals($count, count($actual));
		foreach ($expected as $child)
		{
			$this->assertTrue(in_array($sample[$child], $actual));
		}
	}

	public function provide_get_children()
	{
		// [sample_token, expected_children_count, expected_sample_children]
		return array(
			array('property1', 1, array('doccomment2')),
			array('class', 3, array('doccomment1', 'property1', 'method1')),
			array('method1', 3, array('arg1', 'methodbody1')), // one auto-generated (comment)
			array('methodbody1', 0, array()), // no children
			array('arg1', 0, array()),
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