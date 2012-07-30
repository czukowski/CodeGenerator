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
	public function test_render($attributes, $width, $expected)
	{
		$this->setup_with_attributes($attributes);
		$this->object->width($width);
		$actual = $this->object->render();
		$this->assertEquals($expected, $actual);
	}

	public function provide_render()
	{
		return array(
			array(
				array('char' => ' '), 3, '   ',
			),
			array(
				array('char' => '-'), 1, '-',
			),
			array(
				array('char' => '-='), 2, '-=-=',
			),
			array(
				array('char' => '@'), 10, '@@@@@@@@@@',
			),
		);
	}

	/**
	 * @dataProvider  provide_get_width
	 */
	public function test_get_width($width, $expected)
	{
		$this->setup_object();
		$this->_object_width()->setValue($this->object, $width);
		$actual = $this->object->width();
		$this->assertEquals($expected, $actual);
	}

	public function provide_get_width()
	{
		return array(
			array(1, 1),
			array(10, 10),
		);
	}

	/**
	 * @dataProvider  provide_set_width
	 */
	public function test_set_width($width, $expected)
	{
		$this->setup_object();
		$this->setExpectedExceptionFromArgument($expected);
		$actual = $this->object->width($width);
		$this->assertSame($this->object, $actual);
		$this->assertEquals($width, $this->_object_width()->getValue($this->object));
	}

	public function provide_set_width()
	{
		return array(
			array(1, 1),
			array('a', new \InvalidArgumentException),
			array(3.14, new \InvalidArgumentException),
			array(TRUE, new \InvalidArgumentException),
			array(FALSE, new \InvalidArgumentException),
		);
	}

	private function _object_width()
	{
		return $this->_object_property($this->object, '_width');
	}
}