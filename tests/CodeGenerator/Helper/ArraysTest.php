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
}