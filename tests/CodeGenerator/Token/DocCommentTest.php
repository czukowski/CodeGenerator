<?php
/**
 * DocCommentTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class DocCommentTest extends Testcase
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
				array('text' => 'Method description'),
				"/**\n".
				" * Method description\n".
				" */",
			),
			array(
				array('annotations' => array(
					$this->_create_annotation('var', array('string')),
				)),
				"/**\n".
				" * @var  string\n".
				" */",
			),
			array(
				array('annotations' => array(
					$this->_create_annotation('param', array('string', '$input', 'Input string')),
					$this->_create_annotation('param', array('integer', '&$output', 'Error code')),
					$this->_create_annotation('param', array('bool', '$flag', 'Flags')),
					$this->_create_annotation('return', array('integer')),
				)),
				"/**\n".
				" * @param   string   \$input    Input string\n".
				" * @param   integer  &\$output  Error code\n".
				" * @param   bool     \$flag     Flags\n".
				" * @return  integer\n".
				" */",
			),
			array(
				array('annotations' => array(
					$this->_create_annotation('param', array('string', '$input', 'Input string')),
					$this->_create_annotation('param', array('integer', '&$output', 'Error code')),
					$this->_create_annotation('throws', array('\InvalidArgumentException')),
				)),
				"/**\n".
				" * @param   string   \$input    Input string\n".
				" * @param   integer  &\$output  Error code\n".
				" * @throws  \InvalidArgumentException\n".
				" */",
			),
		);
	}

	private function _create_annotation($name, $columns)
	{
		$this->setup_with_attributes(array(
			'name' => $name,
			'columns' => $columns,
		), array(
			'classname' => __NAMESPACE__.'\Annotation',
		));
		return $this->object;
	}
}