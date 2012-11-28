<?php
/**
 * TokenTreeTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenTreeTest extends Testcase
{
	/**
	 * @dataProvider  provide_find_token
	 */
	public function test_find_token($token, $type, $count, $expected)
	{
		$sample = $this->get_sample_factory()
			->get_sample();
		$actual = $this->object->find_token($sample[$token], $type);
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
	public function test_find_in_parents($token, $type, $expected)
	{
		$sample = $this->get_sample_factory()
			->get_sample();
		$actual = $this->object->find_in_parents($sample[$token], $type);
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
	public function test_find_in_children($token, $type, $count, $expected)
	{
		$sample = $this->get_sample_factory()
			->get_sample();
		$actual = $this->object->find_in_children($sample[$token], $type);
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