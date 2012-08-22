<?php
/**
 * PropertyTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class PropertyTest extends Testcase
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
				array('name' => 'instance', 'static' => TRUE),
				'public static $instance;',
			),
			array(
				array('name' => 'list', 'default' => 'array()'),
				'public $list = array();',
			),
			array(
				array('access' => 'private', 'name' => 'user', 'comment' => '@var  Auth user', 'default' => 'NULL'),
				"/**\n".
				" * @var  Auth user\n".
				" */\n".
				"private \$user = NULL;",
			),
		);
	}
}