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
	 * @dataProvider  provide_name
	 */
	public function test_validate_name($name, $expected)
	{
		$this->setup_object();
		$this->assertEquals($expected, $this->object->validate_name($name));
	}

	public function provide_name()
	{
		return array(
			array(NULL, FALSE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(0, FALSE),
			array('123', FALSE),
			array('a123', TRUE),
			array('a_bC', TRUE),
			array('a_b-C', FALSE),
			array('a_b0C', TRUE),
			array('var', TRUE),
			array('$var', FALSE),
		);
	}

	/**
	 * @dataProvider  provide_constraint
	 */
	public function test_validate_constraint($constraint, $expected)
	{
		$this->setup_object();
		$this->assertEquals($expected, $this->object->validate_constraint($constraint));
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
			array('\\Classname', TRUE),
			array('\\0penCard', FALSE),
			array('\\Classname\\', FALSE),
			array('\\\\Classname', FALSE),
			array('\\Classname\\Subname', TRUE),
			array('\\Classname\\$ubname', FALSE),
		);
	}
}