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
	 * @dataProvider  provide_find_path
	 */
	public function test_find_path($token, $path, $expected)
	{
		$sample = $this->get_sample_factory()
			->setup()
			->call_before_render()
			->get_sample();
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->find_path($sample[$token], $path);
		if ($expected === NULL)
		{
			$this->assertNull($actual);
			return;
		}
		if (is_array($expected))
		{
			foreach ($expected as $item)
			{
				$this->assertTrue(in_array($sample[$item], $actual, TRUE));
			}
		}
		elseif (is_string($expected))
		{
			$this->assertSame($sample[$expected], $actual);
		}
		else
		{
			$this->assertEquals($expected, $actual);
		}
	}

	public function provide_find_path()
	{
		// [source_token, find_path, expected]
		return array(
			// Invalid path
			array('method1', '\\', new \InvalidArgumentException),
			array('class', '123', new \InvalidArgumentException),
			// Self
			array('class', 'Method.', new \InvalidArgumentException),
			array('method1', '.', 'method1'),
			array('method1', './', 'method1'),
			// Parent
			array('method1', 'arguments..', new \InvalidArgumentException),
			array('doccomment2', '..', 'property1'),
			array('doccomment2', '../', 'property1'),
			// Parent - skipping block
			array('method1', '..', 'class'),
			array('method1', '../', 'class'),
			// Get by type
			array('method1', 'Argument', array('arg1')),
			array('method1', 'Block', array('methodbody1')),
			array('doccomment2', 'Annotation', array('ann4')),
			array('class', 'Method', array('method1')),
			array('class', 'Function', array('method1')),
			array('class', 'Property', array('property1')),
			array('class', 'DocComment', array('doccomment1')),
			// Get by attribute
			array('method1', 'arguments', array('arg1')),
			array('doccomment2', 'annotations', array('ann4')),
			array('class', 'methods', array('method1')),
			array('class', 'properties', array('property1')),
			array('class', 'comment', 'doccomment1'),
			// Get ordinal
			array('method1', '../[2]', new \InvalidArgumentException),
			array('method1', 'arguments[123]', new \OutOfRangeException),
			array('method1', 'arguments[0]', 'arg1'),
			array('class', 'methods[0]', 'method1'),
			array('class', 'methods[1]', 'method2'),
			// Get attribute value (this test cannot test for string and array attributes)
			array('method1', 'arguments.constraint', new \InvalidArgumentException),
			array('method1', 'arguments[0].nonexistentattribute', new \InvalidArgumentException),
			array('class', 'methods[1].static', FALSE),
			array('class', 'properties[0].constraint', NULL),
			// Get formatted attribute value
//			array('method1', 'arguments|name', new \InvalidArgumentException),
//			array('method1', 'arguments[0]|name', new \InvalidArgumentException),
//			array('method1', 'arguments[0].name|nonexistentformat', new \InvalidArgumentException),
//			array('method1', 'arguments[0].name|name', 'array_values'),
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
			return;
		}
		$this->assertSame($sample[$expected], $actual);
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
			$this->assertTrue(in_array($sample[$item], $actual, TRUE));
		}
	}

	public function provide_find_in_children()
	{
		// [source_token, find_attribute, expected_count, found_in_token]
		return array(
			array('method1', 'Argument', 1, array('arg1')),
			array('method1', 'Block', 1, array('methodbody1')),
			array('class', 'Method', 2, array('method1', 'method2')),
			array('class', 'Function', 2, array('method1', 'method2')), // Aliased
			array('class', 'Argument', 1, array('arg1')), // Deep
			array('class', 'DocComment', 4, array('doccomment1', 'doccomment2')), // Two auto-generated
			array('property1', 'TokenThatNotExists', 0, array()), // Not found
		);
	}

}