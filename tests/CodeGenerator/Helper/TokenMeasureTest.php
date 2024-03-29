<?php
/**
 * TokenMeasureTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenMeasureTest extends Testcase
{
	/**
	 * @dataProvider  provide_get_indentation
	 */
	public function test_get_indentation($token, $expected)
	{
		$this->setup_object();
		$sample = $this->get_sample_factory()
			->setup()
			->call_before_render()
			->get_sample();
		$actual = $this->object->get_indentation($sample[$token]);
		$this->assertEquals($expected, $actual);
	}

	public function provide_get_indentation()
	{
		return array(
			array('methodbody1', 2),
			array('doccomment2', 1),
			array('method1', 1),
			array('class', 0),
		);
	}

	/**
	 * @dataProvider  provide_get_indentation_length
	 */
	public function test_get_indentation_length($token, $indent_char, $indent_length, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($this->create_config($indent_char, $indent_length)),
		));
		$sample = $this->get_sample_factory()
			->setup()
			->call_before_render()
			->get_sample();
		$actual = $this->object->get_indentation_length($sample[$token]);
		$this->assertEquals($expected, $actual);
	}

	private function create_config($indent_char, $indent_length)
	{
		$key = $this->get_mock(array('methods' => array('none')))
			->encode_string($indent_char);
		return new \CodeGenerator\Config(array(
			'format' => array(
				'indent' => $indent_char,
			),
			'options' => array(
				'char_length' => array(
					$key => $indent_length,
				),
			),
		));
	}

	public function provide_get_indentation_length()
	{
		$widths = array(
			array('    ', 4),
			array('  ', 2),
			array("\t", 4),
			array('-', 1),
		);
		$count = count($widths);
		$provide = array();
		foreach ($this->provide_get_indentation() as $i => $indentation_provider)
		{
			list($char, $width) = $widths[$i % $count];
			list($token, $indentation) = $indentation_provider;
			$provide[] = array($token, $char, $width, $indentation * $width);
		}
		return $provide;
	}

}