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
	public function test_render_block($lines, $glue, $indent, $expected)
	{
		$this->setup_mock();
		$this->object->set('indentation', $indent);
		$actual = $this->get_object_method($this->object, 'render_block')
			->invoke($this->object, $lines, $glue);
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
				NULL,
				2,
				"\t\t\$a = 1;\n".
				"\t\t\$b = \$a;",
			),
			array(
				array(
					"\$a = 1;\n\$b = \$a;",
				),
				NULL,
				1,
				"\t\$a = 1;\n".
				"\t\$b = \$a;",
			),
			array(
				array(
					'$a = 1;',
					'$b = $a;',
				),
				"\n\n",
				1,
				"\t\$a = 1;\n".
				"\n".
				"\t\$b = \$a;",
			),
			array(
				'Weird argument', NULL, 0, NULL,
			),
			array(
				new \stdClass(), NULL, 10, NULL,
			),
		);
	}

	/**
	 * @dataProvider  provide_render_block_comment
	 */
	public function test_render_block_comment($comment, $expected)
	{
		$this->setup_mock();
		$actual = $this->get_object_method($this->object, 'render_block_comment')
			->invoke($this->object, $comment);
		$this->assertEquals($expected, $actual);
	}

	public function provide_render_block_comment()
	{
		return array(
			array(123, NULL),
			array(
				'123',
				"/**\n".
				" * 123\n".
				" */",
			),
			array(
				$this->get_config()
					->helper('tokenFactory')
					->create('DocComment', array(
						'annotations' => array('@return  array'),
						'text' => 'Returns object attributes',
					)),
				"/**\n".
				" * Returns object attributes\n".
				" * \n".
				" * @return  array\n".
				" */",
			)
		);
	}
}