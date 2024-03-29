<?php
/**
 * ValidatorTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class ValidatorTest extends Testcase
{	
	/**
	 * @dataProvider  provide_boolean
	 */
	public function test_validate_boolean($value, $expected)
	{
		$this->setup_object();
		$this->assertSame($expected, $this->object->validate_boolean($value));
	}

	public function provide_boolean()
	{
		return array(
			array(NULL, FALSE),
			array(TRUE, TRUE),
			array(FALSE, TRUE),
			array(0, FALSE),
			array(3.14, FALSE),
			array('123', FALSE),
			array(new \stdClass, FALSE),
		);
	}

	/**
	 * @dataProvider  provide_constraint
	 */
	public function test_validate_constraint($constraint, $expected)
	{
		$this->setup_object();
		$this->assertSame($expected, $this->object->validate_constraint($constraint));
	}

	public function provide_constraint()
	{
		return array(
			array(NULL, FALSE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(0, FALSE),
			array('123', FALSE),
			array('I18n', TRUE),
			array('Classname', TRUE),
			array('Class Name', TRUE),
			array('\\Classname', TRUE),
			array('\\Class name', TRUE),
			array('\\0penCard', FALSE),
			array('\\0pen Card', FALSE),
			array('\\Classname\\', FALSE),
			array('\\\\Classname', FALSE),
			array('\\\\Class name', FALSE),
			array('\\Classname\\Subname', TRUE),
			array('\\Class name\\Subname', TRUE),
			array('\\Classname\\Sub name', TRUE),
			array('\\Class name\\Sub name', TRUE),
			array('\\Classname\\$ubname', FALSE),
		);
	}

	/**
	 * @dataProvider  provide_integer
	 */
	public function test_validate_integer($value, $expected)
	{
		$this->setup_object();
		$this->assertSame($expected, $this->object->validate_integer($value));
	}

	public function provide_integer()
	{
		return array(
			array(NULL, FALSE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(0, TRUE),
			array(-10, TRUE),
			array(512, TRUE),
			array('123', FALSE),
			array(new \stdClass(), FALSE),
		);
	}

	/**
	 * @dataProvider  provide_name
	 */
	public function test_validate_name($name, $expected)
	{
		$this->setup_object();
		$this->assertSame($expected, $this->object->validate_name($name));
	}

	public function provide_name()
	{
		return array(
			array(NULL, FALSE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(0, FALSE),
			array('123', FALSE),
			array('1 23', FALSE),
			array('a123', TRUE),
			array('_a123', TRUE),
			array('a1 23', TRUE),
			array('a_bC', TRUE),
			array('a_b-C', FALSE),
			array('a_b C', TRUE),
			array('a_b0C', TRUE),
			array('var', TRUE),
			array('property name', TRUE),
			array('$var', FALSE),
		);
	}
}