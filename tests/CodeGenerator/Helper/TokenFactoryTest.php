<?php
/**
 * TokenFactoryTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenFactoryTest extends Testcase
{
	/**
	 * @dataProvider  provide_create
	 */
	public function test_create($name, $attributes, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->create($name, $attributes);
		$this->assertInstanceOf($expected, $actual);
		$this->assert_attributes_equal($attributes, $actual);
	}

	public function provide_create()
	{
		return array(
			array('', array(), new \InvalidArgumentException),
			array('NonExistingToken', array('attr' => 'val'), new \InvalidArgumentException),
			array('Whitespace', 'not array', new \InvalidArgumentException),
			array('Annotation', array(), '\CodeGenerator\Token\Annotation'),
			array('Argument', array('name' => 'var'), '\CodeGenerator\Token\Argument'),
			array('Function', array('name' => 'something'), '\CodeGenerator\Token\Method'),
			array('Method', array('name' => 'something'), '\CodeGenerator\Token\Method'),
		);
	}

	/**
	 * @dataProvider  provide_transform
	 */
	public function test_transform($type, $object, $expected, $expected_attributes = array())
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->transform($type, $object);
		$this->assertInstanceOf($expected, $actual);
		$this->assert_attributes_equal($expected_attributes, $actual);
	}

	public function provide_transform()
	{
		return array(
			array('DocComment', new \stdClass, new \InvalidArgumentException),
			array('DocComment', 123, '\CodeGenerator\Token\DocComment', array('text' => 123)),
			array(
				'DocComment', 
				$this->get_config()
					->helper('tokenFactory')
					->create('DocComment', array(
						'annotations' => array('@return  array'),
						'text' => 'Returns object attributes',
					)),
				'\CodeGenerator\Token\DocComment',
			)
		);
	}

	private function assert_attributes_equal($expected, $token)
	{
		foreach ($expected as $attribute_name => $expected_value)
		{
			$actual_value = $token->get($attribute_name);
			$this->assertEquals($expected_value, $actual_value);
		}
	}
}