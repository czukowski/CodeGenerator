<?php
/**
 * AnnotationTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class AnnotationTest extends Testcase
{
	/**
	 * @dataProvider  provide_render
	 */
	public function test_render($attributes, $widths, $expected)
	{
		$this->setup_with_attributes($attributes);
		$this->object->widths($widths);
		$this->assertEquals($expected, $this->object->render());
	}

	public function provide_render()
	{
		return array(
			array(
				array(), array(), '',
			),
			array(
				array('columns' => array('string')), array(), '',
			),
			array(
				array('name' => 'test'), array(), '@test',
			),
			array(
				array('name' => 'test'), array(16), '@test',
			),
			array(
				array('name' => 'var', 'columns' => array('array', 'Test array')), array(), '@var  array  Test array',
			),
			array(
				array('name' => 'var', 'columns' => array('array', 'Test array')), array(5, 6), '@var   array   Test array',
			),
		);
	}
}