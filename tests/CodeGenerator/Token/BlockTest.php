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

	/**
	 * @dataProvider  provide_render_block
	 */
	public function test_render_block($lines, $indent, $expected)
	{
		$this->setup_mock();
		$this->set_expected_exception_from_argument($expected);
		$this->object->set_indentation($indent);
		$actual = $this->get_object_method($this->object, 'render_block')
			->invoke($this->object, $lines);
		$this->assertEquals($expected, $actual);
	}

	public function provide_render_block()
	{
		return array(
			array(
				array(
					'$a = 1;',
					'$b = $a;',
				),
				2,
				"\t\t\$a = 1;\n".
				"\t\t\$b = \$a;",
			),
			array(
				array(
					"\$a = 1;\n\$b = \$a;",
				),
				2,
				"\t\t\$a = 1;\n".
				"\t\t\$b = \$a;",
			),
			array(
				'Weird argument', 0, new \InvalidArgumentException,
			),
			array(
				new \stdClass(), 10, new \InvalidArgumentException,
			),
		);
	}
}