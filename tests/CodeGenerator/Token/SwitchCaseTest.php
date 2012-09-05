<?php
/**
 * SwitchCaseTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class SwitchCaseTest extends Testcase
{
	/**
	 * @dataProvider  provide_render
	 */
	public function test_render($attributes, $expected)
	{
		$this->setup_with_attributes($attributes);
		$this->assertEquals($expected, $this->object->render());
	}

	public function provide_render()
	{
		return array(
			// Both Match nor Default attributes are not set
			array(
				array(),
				'',
			),
			// Default
			array(
				array(
					'default' => TRUE,
				),
				'default:',
			),
			// Empty match
			array(
				array(
					'match' => NULL,
				),
				'',
			),
			// One match, no body
			array(
				array(
					'match' => 'NULL',
				),
				'case NULL:',
			),
			// Multiple matches, some body, break
			array(
				array(
					'match' => array('ERR_ONE', 'ERR_TWO'),
					'body' => 'throw new \Exception;',
					'break' => TRUE,
				),
				"case ERR_ONE:\n".
				"case ERR_TWO:\n".
				"\tthrow new \\Exception;\n".
				"\tbreak;",
			),
		);
	}
}