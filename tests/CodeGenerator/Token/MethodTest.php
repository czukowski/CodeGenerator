<?php
/**
 * MethodTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class MethodTest extends Testcase
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
			// Lambda function
			array(
				array(
					'body' => array('return TRUE;'),
				),
				"function()\n".
				"{\n".
				"\treturn TRUE;\n".
				"}",
			),
			// Abstract method
			array(
				array(
					'abstract' => TRUE,
					'access' => 'protected',
					'name' => 'render',
				),
				"abstract protected function render();",
			),
			// Abstract method, with body (not rendered)
			array(
				array(
					'abstract' => TRUE,
					'name' => 'render',
					'body' => array('return (string) $this;'),
				),
				"abstract function render();",
			),
			// Name only, empty body
			array(
				array(
					'name' => 'get_value',
				),
				"function get_value()\n".
				"{}",
			),
			// Name, some body, comment
			array(
				array(
					'comment' => $this->get_config()
						->helper('tokenFactory')
						->create('DocComment', array(
							'annotations' => array('@return  mixed'),
							'text' => 'Returns object value',
						)),
					'name' => 'get_value',
					'body' => array('return $this->value;'),
				),
				"/**\n".
				" * Returns object value\n".
				" * \n".
				" * @return  mixed\n".
				" */\n".
				"function get_value()\n".
				"{\n".
				"\treturn \$this->value;\n".
				"}",
			),
			// More attributes, body, comment as string
			array(
				array(
					'comment' => 'This function returns the sign of the argument',
					'access' => 'public',
					'static' => TRUE,
					'name' => 'sign',
					'arguments' => array(
						$this->get_config()
							->helper('tokenFactory')
							->create('argument', array('name' => 'number')),
					),
					'body' => array(
						'if ($number > 0) return 1;',
						'elseif ($number < 0) return -1;',
						'else return 0;',
					),
				),
				"/**\n".
				" * This function returns the sign of the argument\n".
				" */\n".
				"public static function sign(\$number)\n".
				"{\n".
				"\tif (\$number > 0) return 1;\n".
				"\telseif (\$number < 0) return -1;\n".
				"\telse return 0;\n".
				"}",
			),
		);
	}

	/**
	 * @dataProvider  provide_access
	 */
	public function test_validate_access($value, $expected)
	{
		$this->setup_object();
		$this->assertEquals($expected, $this->object->validate_access($value));
	}

	public function provide_access()
	{
		return array(
			array('public', TRUE),
			array('private', TRUE),
			array('protected', TRUE),
			array(NULL, TRUE),
			array(FALSE, FALSE),
			array(TRUE, FALSE),
			array(3.14, FALSE),
			array('friend', FALSE),
			array('huh?', FALSE),
		);
	}
}