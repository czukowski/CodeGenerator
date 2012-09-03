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
					$this->create_annotation('var', array('string')),
				)),
				"/**\n".
				" * @var  string\n".
				" */",
			),
			array(
				array('annotations' => array(
					$this->create_annotation('param', array('string', '$input', 'Input string')),
					$this->create_annotation('param', array('integer', '&$output', 'Error code')),
					$this->create_annotation('param', array('bool', '$flag', 'Flags')),
					$this->create_annotation('return', array('integer')),
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
					$this->create_annotation('param', array('string', '$input', 'Input string')),
					$this->create_annotation('param', array('integer', '&$output', 'Error code')),
					$this->create_annotation('throws', array('\InvalidArgumentException')),
				)),
				"/**\n".
				" * @param   string   \$input    Input string\n".
				" * @param   integer  &\$output  Error code\n".
				" * @throws  \InvalidArgumentException\n".
				" */",
			),
			array(
				array(
					'annotations' => array(
						$this->create_annotation('param', array('string', '$param', 'Input parameter')),
						$this->create_annotation('return', array('mixed')),
					),
					'text' => 'Returns different values based on the argument',
				),
				"/**\n".
				" * Returns different values based on the argument\n".
				" * \n".
				" * @param   string  \$param  Input parameter\n".
				" * @return  mixed\n".
				" */",
			),
		);
	}

	/**
	 * @dataProvider  provide_word_wrap_render
	 */
	public function test_word_wrap_render($attributes, $word_wrap, $line_width, $expected)
	{
		$this->setup_word_wrap_render($attributes, $word_wrap, $line_width);
		$this->assertEquals($expected, $this->object->render());
	}

	public function provide_word_wrap_render()
	{
		$text = 'Suppose this is a really quite long method description';
		return array(
			array(
				array('text' => $text), FALSE, 30,
				"/**\n".
				" * $text\n".
				" */",
			),
			array(
				array('text' => $text), TRUE, 30,
				"/**\n".
				" * Suppose this is a really\n".
				" * quite long method\n".
				" * description\n".
				" */",
			),
		);
	}

	private function setup_word_wrap_render($attributes, $word_wrap, $line_width)
	{
		$this->setup_with_attributes($attributes, array(
			'arguments' => array(
				new \CodeGenerator\Config(array(
					'options' => array(
						'line_width' => $line_width,
						'word_wrap' => $word_wrap,
					),
				)),
			),
		));
	}

	private function create_annotation($name, $columns)
	{
		return $this->get_config()
			->helper('tokenFactory')
			->create('Annotation', array(
				'name' => $name,
				'columns' => $columns,
			));
	}
}