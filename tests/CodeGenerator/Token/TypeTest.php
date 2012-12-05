<?php
/**
 * TypeTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class TypeTest extends Testcase
{
	/**
	 * @dataProvider  provide_render
	 */
	public function test_render($attributes, $expected)
	{
		$this->setup_with_attributes($attributes);
		$this->assertEquals($expected, (string) $this->object);
	}

	public function provide_render()
	{
		return array(
			// Name attribute not set
			array(
				array(
					'type' => 'class',
				),
				'',
			),
			// Interface w/o body
			array(
				array(
					'type' => 'interface',
					'name' => 'IDoSomething',
				),
				"interface IDoSomething\n".
				"{}",
			),
			// Phrase name
			array(
				array(
					'type' => 'interface',
					'name' => 'i do something',
				),
				"interface IDoSomething\n".
				"{}",
			),
			// Basic class w/extends and implements
			array(
				array(
					'type' => 'class',
					'name' => 'Vehicle',
					'extends' => 'Tech object',
					'implements' => array('I Do Something', 'I Move'),
				),
				"class Vehicle extends TechObject implements IDoSomething, IMove\n".
				"{}",
			),
			// Final class
			array(
				array(
					'type' => 'class',
					'name' => 'HTTPRequest',
					'final' => TRUE,
					'implements' => array('IRequest'),
					'methods' => array('// Here go class methods'),
				),
				"final class HTTPRequest implements IRequest\n".
				"{\n".
				"\t// Here go class methods\n".
				"}",
			),
			// Class with methods and properties
			array(
				array(
					'type' => 'class',
					'name' => 'Foo',
					'properties' => array(
						'private $bar;',
					),
					'methods' => array(
						$this->create_method('get_bar', array(), array('return $this->bar;')),
						$this->create_method('set_bar', array('$value'), array('$this->bar = $value;')),
					),
				),
				"class Foo\n".
				"{\n".
				"\tprivate \$bar;\n".
				"\n".
				"\tpublic function get_bar()\n".
				"\t{\n".
				"\t\treturn \$this->bar;\n".
				"\t}\n".
				"\n".
				"\tpublic function set_bar(\$value)\n".
				"\t{\n".
				"\t\t\$this->bar = \$value;\n".
				"\t}\n".
				"}",
			),
			// Namespace and doc comments options
			array(
				array(
					'comment' => "@package Fubar",
					'type' => 'interface',
					'name' => 'ISuperObject',
					'namespace' => '\Fu bar',
					'use' => array(array('\Fu bar\Bar', 'Bar'), '\Fu bar\Fu'),
					'methods' => array('// Here go class methods'),
				),
				"/**\n".
				" * @package Fubar\n".
				" */\n".
				"namespace \FuBar;\n".
				"use \FuBar\Bar as Bar,\n".
				"\t\FuBar\Fu;\n".
				"\n".
				"interface ISuperObject\n".
				"{\n".
				"\t// Here go class methods\n".
				"}",
			),
		);
	}

	private function create_method($name, $arguments, $body)
	{
		return $this->get_config()
			->helper('tokenFactory')
			->create('Method', array(
				'name' => $name,
				'access' => 'public',
				'arguments' => $arguments,
				'body' => $body,
			));
	}

	/**
	 * @dataProvider  provide_validate_type
	 */
	public function test_validate_type($value, $expected)
	{
		$this->setup_object();
		$this->assertEquals($expected, $this->object->validate_type($value));
	}

	public function provide_validate_type()
	{
		return array(
			array('class', TRUE),
			array('interface', TRUE),
			array('something else', FALSE),
			array(7.62, FALSE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(NULL, FALSE),
			array(new \stdClass, FALSE),
		);
	}

	/**
	 * @dataProvider  provide_validate_use
	 */
	public function test_validate_use($value, $expected)
	{
		$this->setup_object();
		$this->assertEquals($expected, $this->object->validate_use($value));
	}

	public function provide_validate_use()
	{
		return array(
			array('classname', FALSE),
			array('class name', FALSE),
			array('\namespace name\class name', FALSE),
			array(array('classname'), TRUE),
			array(array('class name'), TRUE),
			array(array('\namespace name\class name'), TRUE),
			array(array('\namespace name\class name', 'name'), TRUE),
			array(array(array('\namespace name\class name', 'class name'), 'name'), TRUE),
			array(array(), FALSE),
			array(array('\namespace name\class name', 'name', 3.14), FALSE),
			array(array(array('\namespace name\class name', 'class name', 'another name'), 'name'), FALSE),
			array('something/else', FALSE),
			array(7.62, FALSE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(NULL, FALSE),
			array(new \stdClass, FALSE),
		);
	}
}