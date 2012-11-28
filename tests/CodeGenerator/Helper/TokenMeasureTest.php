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

	/**
	 * @dataProvider  provide_find_token
	 */
	public function test_find_token($token, $find, $count, $expected)
	{
		$sample = $this->get_sample_factory()
			->get_sample();
		$actual = $this->object->find_token($sample[$token], $find);
		$this->assertEquals($count, count($actual));
		foreach ($expected as $item)
		{
			$this->assertTrue(in_array($sample[$item], $actual));
		}
	}

	public function provide_find_token()
	{
		// [source_token, find_attribute, expected_count, found_in_token]
		return array(
			array('method1', 'Argument', 1, array('arg1')),
			array('method1', 'Block', 2, array('methodbody1')), // Both in parents (auto-generated methods container) and children
			array('doccomment1', 'DocComment', 1, array('doccomment1')), // Found self
			array('class', 'Method', 1, array('method1')),
			array('property1', 'TokenThatNotExists', 0, array()), // Not found
		);
	}

	/**
	 * @dataProvider  provide_find_in_parents
	 */
	public function test_find_in_parents($token, $find, $expected)
	{
		$sample = $this->get_sample_factory()
			->get_sample();
		$actual = $this->object->find_in_parents($sample[$token], $find);
		if ($expected === NULL)
		{
			$this->assertNull($actual);
		}
		else
		{
			$this->assertSame($sample[$expected], $actual);
		}
	}

	public function provide_find_in_parents()
	{
		// [source_token, find_attribute, found_in_token]
		return array(
			array('method1', 'TokenThatNotExists', NULL), // Not found
			array('method1', 'Property', NULL), // Not found
			array('doccomment1', 'DocComment', 'doccomment1'), // Found self
			array('class', 'Class', 'class'), // Found self, aliased
			array('method1', 'Class', 'class'), // Aliased
			array('methodbody1', 'Type', 'class'),
		);
	}

	/**
	 * @dataProvider  provide_find_in_children
	 */
	public function test_find_in_children($token, $find, $count, $expected)
	{
		$sample = $this->get_sample_factory()
			->get_sample();
		$actual = $this->object->find_in_children($sample[$token], $find);
		$this->assertEquals($count, count($actual));
		foreach ($expected as $item)
		{
			$this->assertTrue(in_array($sample[$item], $actual));
		}
	}

	public function provide_find_in_children()
	{
		// [source_token, find_attribute, expected_count, found_in_token]
		return array(
			array('method1', 'Argument', 1, array('arg1')),
			array('method1', 'Block', 1, array('methodbody1')),
			array('class', 'Method', 1, array('method1')),
			array('class', 'Function', 1, array('method1')), // Aliased
			array('class', 'Argument', 1, array('arg1')), // Deep
			array('class', 'DocComment', 3, array('doccomment1', 'doccomment2')), // One auto-generated
			array('property1', 'TokenThatNotExists', 0, array()), // Not found
		);
	}

}