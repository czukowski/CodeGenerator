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
	 * @dataProvider  provide_render_block
	 */
	public function test_render_block($lines, $indent, $expected)
	{
		$this->setup_mock();
		$this->set_expected_exception_from_argument($expected);
		$this->object->set('indentation', $indent);
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
				1,
				"\t\$a = 1;\n".
				"\t\$b = \$a;",
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