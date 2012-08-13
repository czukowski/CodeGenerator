<?php
/**
 * WhitespaceTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class WhitespaceTest extends Testcase
{
	/**
	 * @dataProvider  provide_render
	 */
	public function test_render($attributes, $expected)
	{
		$this->setup_with_attributes($attributes);
		$actual = $this->object->render();
		$this->assertEquals($expected, $actual);
	}

	public function provide_render()
	{
		return array(
			array(
				array('width' => 3), '   ',
			),
			array(
				array('char' => ' ', 'width' => 3), '   ',
			),
			array(
				array('char' => '-', 'width' => 1), '-',
			),
			array(
				array('char' => '-=', 'width' => 2), '-=-=',
			),
			array(
				array('char' => '@', 'width' => 10), '@@@@@@@@@@',
			),
		);
	}
}