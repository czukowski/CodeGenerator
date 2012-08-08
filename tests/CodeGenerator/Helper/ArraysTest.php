<?php
/**
 * Arrays class tests
 * 
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @copyright  (c) 2008-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class ArraysTest extends Testcase
{
	/**
	 * @dataProvider  provider_is_array
	 */
	public function test_is_array($array, $expected)
	{
		$this->assertSame($expected, $this->object->is_array($array));
	}

	public function provider_is_array()
	{
		return array(
			array($a = array('one', 'two', 'three'), TRUE),
			array(new \ArrayObject($a), TRUE),
			array(new \ArrayIterator($a), TRUE),
			array('not an array', FALSE),
			array(new \stdClass, FALSE),
		);
	}

	/**
	 * @dataProvider  provide_is_assoc
	 */
	public function test_is_assoc(array $array, $expected)
	{
		$this->assertSame($expected, $this->object->is_assoc($array));
	}

	public function provide_is_assoc()
	{
		return array(
			array(array('one', 'two', 'three'), FALSE),
			array(array('one' => 'o clock', 'two' => 'o clock', 'three' => 'o clock'), TRUE),
		);
	}

	/**
	 * @dataProvider  provide_merge
	 */
	public function test_merge($expected, $array1, $array2)
	{
		$this->assertSame($expected, $this->object->merge($array1,$array2));
	}

	public function provide_merge()
	{
		return array(
			// Test how it merges arrays and sub arrays with assoc keys
			array(
				array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane')),
				array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane')),
				array('name' => 'mary', 'children' => array('jane')),
			),
			// See how it merges sub-arrays with numerical indexes
			array(
				array(array('test1','test3'), array('test2','test4')),
				array(array('test1'), array('test2')),
				array(array('test3'), array('test4')),
			),
			array(
				array(array('test1','test3'), array('test2','test4')),
				array(array('test1'), array('test2')),
				array(array('test3'), array('test4')),
			),
			array(
				array('digits' => array(0, 1, 2, 3)),
				array('digits' => array(0, 1)),
				array('digits' => array(2, 3)),
			),
			// See how it manages merging items with numerical indexes
			array(
				array(0, 1, 2, 3),
				array(0, 1),
				array(2, 3),
			),
			// Try and get it to merge assoc. arrays recursively
			array(
				array('foo' => 'bar', array('temp' => 'life')),
				array('foo' => 'bin', array('temp' => 'name')),
				array('foo' => 'bar', array('temp' => 'life')),
			),
			// Bug #3139
			array(
				array('foo'	=> array('bar')),
				array('foo'	=> 'bar'),
				array('foo'	=> array('bar')),
			),
			array(
				array('foo'	=> 'bar'),
				array('foo'	=> array('bar')),
				array('foo'	=> 'bar'),
			),
		);
	}

	/**
	 * @dataProvider  provider_path
	 */
	public function test_path($expected, $array, $path, $default = NULL, $delimiter = NULL)
	{
		$this->assertSame($expected, $this->object->path($array, $path, $default, $delimiter));
	}

	public function provider_path()
	{
		$array = array(
			'foobar' => array('definition' => 'lost'),
			'kohana' => 'awesome',
			'users'  => array(
				1 => array('name' => 'matt'),
				2 => array('name' => 'john', 'interests' => array('hocky' => array('length' => 2), 'football' => array())),
				3 => 'frank', // Issue #3194
			),
			'object' => new \ArrayObject(array('iterator' => TRUE)), // Iterable object should work exactly the same
		);

		return array(
			// Tests returns normal values
			array($array['foobar'], $array, 'foobar'),
			array($array['kohana'], $array, 'kohana'),
			array($array['foobar']['definition'], $array, 'foobar.definition'),
			// Custom delimiters
			array($array['foobar']['definition'], $array, 'foobar/definition', NULL, '/'),
			// We should be able to use NULL as a default, returned if the key DNX
			array(NULL, $array, 'foobar.alternatives',  NULL),
			array(NULL, $array, 'kohana.alternatives',  NULL),
			// Try using a string as a default
			array('nothing', $array, 'kohana.alternatives',  'nothing'),
			// Make sure you can use arrays as defaults
			array(array('far', 'wide'), $array, 'cheese.origins',  array('far', 'wide')),
			// Ensures path() casts ints to actual integers for keys
			array($array['users'][1]['name'], $array, 'users.1.name'),
			// Test that a wildcard returns the entire array at that "level"
			array($array['users'], $array, 'users.*'),
			// Now we check that keys after a wilcard will be processed
			array(array(0 => array(0 => 2)), $array, 'users.*.interests.*.length'),
			// See what happens when it can't dig any deeper from a wildcard
			array(NULL, $array, 'users.*.fans'),
			// Starting wildcards, issue #3269
			array(array('matt', 'john'), $array['users'], '*.name'),
			// Path as array, issue #3260
			array($array['users'][2]['name'], $array, array('users', 2, 'name')),
			array($array['object']['iterator'], $array, 'object.iterator'),
		);
	}
}