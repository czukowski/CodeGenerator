<?php
/**
 * ConstructTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class ConstructTest extends Testcase
{
	/**
	 * @dataProvider  provide_render
	 */
	public function test_render($attributes, $expected)
	{
		$this->setup_with_attributes($attributes);
		$this->assertEquals($expected, (string) $this->object);
	}

	public function provide_render()
	{
		$factory = $this->get_config()
			->helper('tokenFactory');
		return array(
			// Type attribute not set
			array(
				array(
					'body' => 'return TRUE;',
				),
				'',
			),
			// Body attribute not set
			array(
				array(
					'type' => 'if',
					'condition' => 'TRUE',
				),
				"if (TRUE)\n".
				"{}",
			),
			// Else with condition (ignored)
			array(
				array(
					'type' => 'else',
					'condition' => '$value === FALSE',
					'body' => 'return TRUE;',
				),
				"else\n".
				"{\n".
				"\treturn TRUE;\n".
				"}",
			),
			// If
			array(
				array(
					'type' => 'if',
					'condition' => '$model->is_valid()',
					'body' => array(
						'$model->save();',
						'return;',
					),
				),
				"if (\$model->is_valid())\n".
				"{\n".
				"\t\$model->save();\n".
				"\treturn;\n".
				"}",
			),
			// While
			array(
				array(
					'type' => 'while',
					'condition' => 'TRUE',
					'body' => array(
						'$this->loop();',
					),
				),
				"while (TRUE)\n".
				"{\n".
				"\t\$this->loop();\n".
				"}",
			),
			// Do (special case)
			array(
				array(
					'type' => 'do',
					'condition' => 'TRUE',
					'body' => array(
						'$this->loop();',
					),
				),
				"do\n".
				"{\n".
				"\t\$this->loop();\n".
				"}\n".
				"while (TRUE);",
			),
			// Switch
			array(
				array(
					'type' => 'switch',
					'condition' => '$error_code',
					'body' => array(
						$factory->create('Case', array(
							'match' => 'TRUE',
							'body' => 'throw new \\Exception;',
							'break' => TRUE,
						)),
						$factory->create('Case', array(
							'match' => 'FALSE',
							'body' => 'return $result;',
							'break' => TRUE,
						)),
						$factory->create('Case', array(
							'default' => TRUE,
							'body' => 'throw new \\LogicException;',
						)),
					),
				),
				"switch (\$error_code)\n".
				"{\n".
				"\tcase TRUE:\n".
				"\t\tthrow new \Exception;\n".
				"\t\tbreak;\n".
				"\tcase FALSE:\n".
				"\t\treturn \$result;\n".
				"\t\tbreak;\n".
				"\tdefault:\n".
				"\t\tthrow new \LogicException;\n".
				"}",
			),
		);
	}

	/**
	 * @dataProvider  provide_is_condition_in_heading
	 */
	public function test_is_condition_in_heading($value, $expected)
	{
		$this->setup_object();
		$actual = $this->get_object_method($this->object, 'is_condition_in_heading')
			->invoke($this->object, $value);
		$this->assertEquals($expected, $actual);
	}

	public function provide_is_condition_in_heading()
	{
		return array(
			array('if', TRUE),
			array('elseif', TRUE),
			array('else', FALSE),
			array('for', TRUE),
			array('foreach', TRUE),
			array('while', TRUE),
			array('do', FALSE),
			array('switch', TRUE),
		);
	}

	/**
	 * @dataProvider  provide_validate_type
	 */
	public function test_validate_type($value, $expected)
	{
		$this->setup_object();
		$this->assertEquals($expected, $this->object->validate_type($value));
	}

	public function provide_validate_type()
	{
		return array(
			array('if', TRUE),
			array('elseif', TRUE),
			array('else', TRUE),
			array('for', TRUE),
			array('foreach', TRUE),
			array('while', TRUE),
			array('do', TRUE),
			array('switch', TRUE),
			array('something else', FALSE),
			array(7.62, FALSE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(NULL, FALSE),
			array(new \stdClass, FALSE),
		);
	}

}