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
				array(
					'items' => array(
						'$a = 1;',
						'$b = $a;',
					),
					'indentation' => 2,
				),
				"\t\t\$a = 1;\n".
				"\t\t\$b = \$a;",
			),
			array(
				array(
					'items' => array(
						"\$a = 1;\n\$b = \$a;",
					),
					'indentation' => 1,
				),
				"\t\$a = 1;\n".
				"\t\$b = \$a;",
			),
			array(
				array(
					'items' => array(
						'$a = 1;',
						'$b = $a;',
					),
					'indentation' => 1,
					'glue' => "\n\n",
				),
				"\t\$a = 1;\n".
				"\n".
				"\t\$b = \$a;",
			),
			array(
				array(
					'items' => 'Weird argument',
				),
				NULL,
			),
		);
	}
}