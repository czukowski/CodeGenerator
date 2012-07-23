<?php
/**
 * SimpleOptimizerTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Math;

class SimpleOptimizerTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @dataProvider  provide_construct
	 */
	public function test_construct($arguments, $expected)
	{
		$this->setExpectedExceptionFromArgument($expected);
		$this->setup_object(array('arguments' => $arguments));
		$actual_function = $this->_object_property($this->object, '_function')
			->getValue($this->object);
		$this->assertSame($arguments[0], $actual_function);
		$actual_parameters = $this->_object_property($this->object, '_parameters')
			->getValue($this->object);
		$this->assertSame($arguments[1], $actual_parameters);
	}

	public function provide_construct()
	{
		return array(
			// Invalid argument types
			array(
				array(NULL, NULL),
				new \InvalidArgumentException,
			),
			array(
				array(NULL, array()),
				new \InvalidArgumentException,
			),
			array(
				array(function() {}, NULL),
				new \InvalidArgumentException,
			),
			// Valid argument types
			array(
				array(function() {}, array()),
				NULL,
			),
			array(
				array(function() {}, new \ArrayObject(array(array('foo'), array('bar')))),
				NULL,
			),
			array(
				array(array($this, __FUNCTION__), array()),
				NULL,
			),
			array(
				array('max', array(array(1, 2, 3))),
				NULL,
			),
			// 2nd argument is not array of arrays
			array(
				array(function() {}, new \ArrayObject(array('foo', 'bar'))),
				new \InvalidArgumentException,
			),
			array(
				array(array($this, __FUNCTION__), array(NULL)),
				new \InvalidArgumentException,
			),
			array(
				array('min', array(1, 2, 3)),
				new \InvalidArgumentException,
			),
		);
	}

	/**
	 * @dataProvider  provide_execute
	 */
	public function test_execute($arguments, $expected)
	{
		$this->setup_object(array('arguments' => $arguments));
		$actual = $this->object->execute();
		$this->assertEquals($expected, $actual);
	}

	public function provide_execute()
	{
		return array(
			// Get min of max
			array(
				array(
					'max',
					array(
						0 => array(1, 2, 3),
						1 => array(4, 5, 6),
						2 => array(1, 1, 1),
					),
				),
				array(
					2 => array(1, 1, 1),
				),
			),
			// Get min sum, two best params sets
			array(
				array(
					'array_sum',
					array(
						0 => array(array(1, 2, 3)),
						1 => array(array(4, 5, 6)),
						2 => array(array(1, 1, 2)),
						3 => array(array(1, 2, 1)),
					),
				),
				array(
					2 => array(array(1, 1, 2)),
					3 => array(array(1, 2, 1)),
				),
			),
			// Get max sum: multiply the min sum result by -1
			array(
				array(
					function($array) {
						return -1 * array_sum($array);
					},
					array(
						0 => array(array(1, 2, 3)),
						1 => array(array(4, 5, 6)),
						2 => array(array(1, 1, 2)),
						3 => array(array(1, 2, 1)),
					),
				),
				array(
					1 => array(array(4, 5, 6)),
				),
			),
		);
	}

}