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
		foreach ($attributes as $attribute_name => $expected_value)
		{
			$actual_value = $actual->get($attribute_name);
			$this->assertEquals($expected_value, $actual_value);
		}
	}

	public function provide_create()
	{
		return array(
			array('', array(), new \InvalidArgumentException),
			array('NonExistingToken', array('attr' => 'val'), new \InvalidArgumentException),
			array('Whitespace', 'not array', new \InvalidArgumentException),
			array('Annotation', array(), '\CodeGenerator\Token\Annotation'),
			array('Argument', array('name' => 'var'), '\CodeGenerator\Token\Argument'),
		);
	}
}