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
	 * @dataProvider  provide_get_self
	 */
	public function test_get_self($from, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->get_self($from);
		$this->assertSame($expected, $actual);
	}

	public function provide_get_self()
	{
		$samples = $this->get_sample_factory();
		return array(
			array($samples->get_sample('doccomment2'), $samples->get_sample('doccomment2')),
			array($samples->get_sample('method1'), $samples->get_sample('method1')),
			array(array($samples->get_sample('method1'), $samples->get_sample('method1')), new \InvalidArgumentException),
			array('some value', new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_get_parent
	 */
	public function test_get_parent($from, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->get_parent($from);
		$this->assertSame($expected, $actual);
	}

	public function provide_get_parent()
	{
		$samples = $this->get_sample_factory();
		return array(
			array($samples->get_sample('doccomment2'), $samples->get_sample('property1')),
			array($samples->get_sample('method1'), $samples->get_sample('class')),
			array(array($samples->get_sample('method1'), $samples->get_sample('method1')), new \InvalidArgumentException),
			array('some value', new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_get_by_type
	 */
	public function test_get_by_type($from, $argument, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->get_by_type($from, $argument);
		$this->assertSame($expected, $actual);
	}

	public function provide_get_by_type()
	{
		$samples = $this->get_sample_factory();
		$methods = array(
			$samples->get_sample('method1'),
			$samples->get_sample('method2'),
		);
		return array(
			array($samples->get_sample('method1'), 'Argument', array($samples->get_sample('arg1'))),
			array($samples->get_sample('method1'), 'Block', array($samples->get_sample('methodbody1'))),
			array($samples->get_sample('doccomment2'), 'Annotation', array($samples->get_sample('ann4'))),
			array($samples->get_sample('class'), 'Method', $methods),
			array($samples->get_sample('class'), 'Function', $methods),
			array($samples->get_sample('class'), 'Property', array($samples->get_sample('property1'))),
			array($samples->get_sample('class'), 'DocComment', array($samples->get_sample('doccomment1'))),
			array($methods, 'Argument', new \InvalidArgumentException),
			array('some value', 'DocComment', new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_get_by_attribute
	 */
	public function test_get_by_attribute($from, $argument, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->get_by_attribute($from, $argument);
		$this->assertSame($expected, $actual);
	}

	public function provide_get_by_attribute()
	{
		$samples = $this->get_sample_factory();
		$methods = array(
			$samples->get_sample('method1'),
			$samples->get_sample('method2'),
		);
		return array(
			array($samples->get_sample('method1'), 'arguments', array($samples->get_sample('arg1'))),
			array($samples->get_sample('doccomment2'), 'annotations', array($samples->get_sample('ann4'))),
			array($samples->get_sample('class'), 'methods', $methods),
			array($samples->get_sample('class'), 'properties', array($samples->get_sample('property1'))),
			array($samples->get_sample('class'), 'comment', $samples->get_sample('doccomment1')),
			array($samples->get_sample('class'), 'comment', $samples->get_sample('doccomment1')),
			array($methods, 'name', new \InvalidArgumentException),
			array('some value', 'name', new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_get_ord
	 */
	public function test_get_ord($from, $argument, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->get_ord($from, $argument);
		$this->assertSame($expected, $actual);
	}

	public function provide_get_ord()
	{
		$samples = $this->get_sample_factory();
		$methods = array(
			$samples->get_sample('method1'),
			$samples->get_sample('method2'),
		);
		return array(
			array($methods, 0, $samples->get_sample('method1')),
			array($methods, 1, $samples->get_sample('method2')),
			array($methods, '[0]', $samples->get_sample('method1')),
			array($methods, '[1]', $samples->get_sample('method2')),
			array($methods, '[2]', new \OutOfRangeException),
			array($samples->get_sample('method1'), '[0]', new \InvalidArgumentException),
			array('some value', '[0]', new \InvalidArgumentException),
			array(array(1, 2, 3), '2', 3),
			array(array(1, 2, 3), 3, new \OutOfRangeException),
		);
	}

	/**
	 * @dataProvider  provide_get_attribute
	 */
	public function test_get_attribute($from, $argument, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->get_attribute($from, $argument);
		$this->assertSame($expected, $actual);
	}

	public function provide_get_attribute()
	{
		$samples = $this->get_sample_factory();
		return array(
			array($samples->get_sample('method1'), 'name', '__construct'),
			array($samples->get_sample('class')->get('methods'), 'name', new \InvalidArgumentException),
			array('some value', 'name', new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_format_attribute
	 */
	public function test_format_attribute($from, $argument, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->format_attribute($from, $argument);
		$this->assertSame($expected, $actual);
	}

	public function provide_format_attribute()
	{
		return array(
			array('array values', 'name', 'array_values'),
			array('math\sigma function', 'class-name', 'Math\Sigma_Function'),
			array('method name', 'nonexistentformat', new \InvalidArgumentException),
			array($this->get_sample_factory()->get_sample('method1'), 'name', new \InvalidArgumentException),
			array($this->get_sample_factory()->get_sample('class')->get('methods'), 'name', new \InvalidArgumentException),
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