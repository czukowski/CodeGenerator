<?php
/**
 * BlockTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class BlockTest extends Testcase
{
	/**
	 * @dataProvider  provide_indent
	 */
	public function test_indent($set_value, $expected)
	{
		$this->setup_mock();
		$this->assertEquals(0, $this->object->get_indentation());
		$this->set_expected_exception_from_argument($expected);
		$this->assertSame($this->object, $this->object->set_indentation($set_value));
		$this->assertEquals($expected, $this->object->get_indentation());
	}

	public function provide_indent()
	{
		return array(
			array(0, 0),
			array(10, 10),
			array(NULL, new \InvalidArgumentException),
			array(FALSE, new \InvalidArgumentException),
			array('', new \InvalidArgumentException),
		);
	}
}