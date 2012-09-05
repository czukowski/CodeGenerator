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
		$default_attributes = $this->get_config()
			->get_options('factory.attributes.'.ucfirst($name), array());
		$expected_attributes = $this->get_config()
			->helper('arrays')
			->merge($default_attributes, $attributes);
		$this->assert_attributes_equal($expected_attributes, $actual);
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
			array('Interface', array(), '\CodeGenerator\Token\Type'),
			array('Class', array(), '\CodeGenerator\Token\Type'),
			array('Function', array(), '\CodeGenerator\Token\Method'),
			array('If', array(), '\CodeGenerator\Token\Construct'),
			array('Elseif', array(), '\CodeGenerator\Token\Construct'),
			array('Else', array(), '\CodeGenerator\Token\Construct'),
			array('For', array(), '\CodeGenerator\Token\Construct'),
			array('Foreach', array(), '\CodeGenerator\Token\Construct'),
			array('While', array(), '\CodeGenerator\Token\Construct'),
			array('Do', array(), '\CodeGenerator\Token\Construct'),
			array('Switch', array(), '\CodeGenerator\Token\Construct'),
		);
	}

	/**
	 * @dataProvider  provide_transform
	 */
	public function test_transform($type, $object, $parent, $expected, $expected_attributes = array())
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->transform($type, $object, $parent);
		$this->assertInstanceOf($expected, $actual);
		$this->assertSame($parent, $actual->get('parent'));
		$this->assert_attributes_equal($expected_attributes, $actual);
	}

	public function provide_transform()
	{
		$factory = $this->get_config()
			->helper('tokenFactory');
		$block = $factory->create('Block');
		return array(
			array('Block', array('123'), NULL, '\CodeGenerator\Token\Block'),
			array('Block', array(), $block, '\CodeGenerator\Token\Block'),
			array('DocComment', new \stdClass, NULL, new \InvalidArgumentException),
			array('DocComment', 123, $block, '\CodeGenerator\Token\DocComment', array('text' => 123)),
			array(
				'DocComment',
				$factory->create('DocComment', array(
					'annotations' => array('@return  array'),
					'text' => 'Returns object attributes',
				)),
				$block,
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