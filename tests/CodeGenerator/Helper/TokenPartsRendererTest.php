<?php
/**
 * TokenPartsRendererTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenPartsRendererTest extends Testcase
{
	/**
	 * @dataProvider  provide_render_class_name
	 */
	public function test_render_class_name($format, $name, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($this->create_config($format)),
		));
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->render_class_name($name);
		$this->assertSame($expected, $actual);
	}

	public function provide_render_class_name()
	{
		return array(
			array('camelcase', 'some class name', 'SomeClassName'),
			array('camelcase', 'Some Class Name', 'SomeClassName'),
			array('underscore', 'some class name', 'Some_Class_Name'),
			array('underscore', 'Some Class Name', 'Some_Class_Name'),
			array('camelcase', 'Namespace\Some Class Name', 'Namespace\SomeClassName'),
			array('underscore', 'Namespace\some class name', 'Namespace\Some_Class_Name'),
			array('anything else', 'Some Class Name', new \LogicException),
		);
	}

	/**
	 * @dataProvider  provide_render_name
	 */
	public function test_render_name($format, $name, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($this->create_config($format)),
		));
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->render_name($name);
		$this->assertSame($expected, $actual);
	}

	public function provide_render_name()
	{
		return array(
			array('camelcase', 'some variable name', 'someVariableName'),
			array('camelcase', 'Some Variable Name', 'someVariableName'),
			array('underscore', 'some variable name', 'some_variable_name'),
			array('underscore', 'Some Variable Name', 'Some_Variable_Name'),
			array('anything else', 'Some Variable Name', new \LogicException),
		);
	}

	private function create_config($format)
	{
		return new \CodeGenerator\Config(array(
			'options' => array(
				'names' => $format,
			),
		));
	}
}