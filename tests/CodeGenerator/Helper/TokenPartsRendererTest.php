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
	public function test_render_class_name($format, $name, $option, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($this->create_config('classname', $format)),
		));
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->render_class_name($name, $option);
		$this->assertSame($expected, $actual);
	}

	public function provide_render_class_name()
	{
		return array(
			// Phrase names
			array('camelcase', 'some class name', NULL, 'SomeClassName'),
			array('camelcase', 'Some Class Name', NULL, 'SomeClassName'),
			array('underscore', 'some class name', NULL, 'Some_Class_Name'),
			array('underscore', 'Some Class Name', NULL, 'Some_Class_Name'),
			// Explicit format option
			array('camelcase', 'some class name', 'underscore', 'Some_Class_Name'),
			array('camelcase', 'Some Class Name', 'underscore', 'Some_Class_Name'),
			array('underscore', 'some class name', 'camelcase', 'SomeClassName'),
			array('underscore', 'Some Class Name', 'camelcase', 'SomeClassName'),
			// Class names with namespaces
			array('camelcase', 'Namespace\Some Class Name', NULL, 'Namespace\SomeClassName'),
			array('camelcase', 'Namespace\Some Class Name', 'underscore', 'Namespace\Some_Class_Name'),
			array('underscore', 'Namespace\some class name', NULL, 'Namespace\Some_Class_Name'),
			array('underscore', 'Namespace\some class name', 'camelcase', 'Namespace\SomeClassName'),
			// Already formatted names will not be changed
			array('camelcase', 'Some_Class_Name', NULL, 'Some_Class_Name'),
			array('underscore', 'SomeClassName', NULL, 'SomeClassName'),
			// Invalid config option
			array('anything else', 'Some Class Name', NULL, new \LogicException),
		);
	}

	/**
	 * @dataProvider  provide_render_name
	 */
	public function test_render_name($format, $name, $option, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($this->create_config('default', $format)),
		));
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->render_name($name, $option);
		$this->assertSame($expected, $actual);
	}

	public function provide_render_name()
	{
		return array(
			// Phrase names
			array('camelcase', 'some variable name', NULL, 'someVariableName'),
			array('camelcase', 'Some Variable Name', NULL, 'someVariableName'),
			array('underscore', 'some variable name', NULL, 'some_variable_name'),
			array('underscore', 'Some Variable Name', NULL, 'Some_Variable_Name'),
			// Explicit format option
			array('camelcase', 'some variable name', 'underscore', 'some_variable_name'),
			array('camelcase', 'Some Variable Name', 'underscore', 'Some_Variable_Name'),
			array('underscore', 'some variable name', 'camelcase', 'someVariableName'),
			array('underscore', 'Some Variable Name', 'camelcase', 'someVariableName'),
			// Already formatted names will not be changed
			array('camelcase', 'Some_Class_Name', NULL, 'Some_Class_Name'),
			array('underscore', 'SomeClassName', NULL, 'SomeClassName'),
			// Invalid config option
			array('anything else', 'Some Variable Name', NULL, new \LogicException),
		);
	}

	private function create_config($items, $format)
	{
		return new \CodeGenerator\Config(array(
			'options' => array(
				'names' => array(
					$items => $format,
				),
			),
		));
	}
}