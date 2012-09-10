<?php
/**
 * ArgumentTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class ArgumentTest extends Testcase
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
			array(
				array(), '',
			),
			array(
				array('constraint' => 'array'), '',
			),
			array(
				array('default' => 'array()'), '',
			),
			array(
				array('name' => 'var'), '$var',
			),
			array(
				array('name' => 'list', 'constraint' => 'array'), 'array $list',
			),
			array(
				array('name' => 'user', 'constraint' => '\Auth\User', 'default' => 'NULL'), '\Auth\User $user = NULL',
			),
			array(
				array('name' => 'user', 'constraint' => '\Auth\Priveleged User'), '\Auth\Priveleged_User $user',
			),
		);
	}
}