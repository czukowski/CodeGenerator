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
		$this->set_expected_exception_from_argument($expected);
		$this->setup_object(array('arguments' => $arguments));
		$actual_function = $this->get_object_property($this->object, '_function')
			->getValue($this->object);
		$this->assertSame($arguments[0], $actual_function);
		$actual_parameters = $this->get_object_property($this->object, '_solutions_space')
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
			array(
				array(function() {}, array()),
				new \InvalidArgumentException,
			),
			array(
				array(array($this, __FUNCTION__), array()),
				new \InvalidArgumentException,
			),
			// Valid argument types
			array(
				array(function() {}, new \ArrayObject(array(array('foo'), array('bar')))),
				NULL,
			),
			array(
				array('max', array(array(1, 2, 3))),
				NULL,
			),
			// 2nd argument is not array of arrays or empty
			array(
				array('max', array(array())),
				new \InvalidArgumentException,
			),
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
			// 1x3 space
			array(
				array(
					function($arg) {
						return $arg * $arg;
					},
					array(
						array(0.2, 0.1, 0.3),
					),
				),
				array(
					array(0.1),
				),
			),
			// 1x3 space - get min of max - arguments are arrays
			array(
				array(
					'max',
					array(
						array(
							array(1, 2, 3),
							array(4, 5, 6),
							array(1, 1, 1),
						),
					),
				),
				array(
					array(
						array(1, 1, 1),
					),
				),
			),
			// 1x3 space - get min sum - two best params sets
			array(
				array(
					'array_sum',
					array(
						array(
							array(1, 2, 3),
							array(4, 5, 6),
							array(1, 1, 2),
							array(1, 2, 1),
						),
					),
				),
				array(
					array(array(1, 1, 2)),
					array(array(1, 2, 1)),
				),
			),
			// 1x3 space - get max sum: multiply the min sum result by -1
			array(
				array(
					function($array) {
						return -1 * array_sum($array);
					},
					array(
						array(
							array(1, 2, 3),
							array(4, 5, 6),
							array(1, 1, 2),
							array(1, 2, 1),
						),
					),
				),
				array(
					array(array(4, 5, 6)),
				),
			),
			// 3x3 space - get min sum
			array(
				array(
					function() {
						return array_sum(func_get_args());
					},
					array(
						array(1, 0, 3),
						array(4, 0, 6),
						array(1, 1, 1),
					),
				),
				array(
					array(0, 0, 1),
				),
			),
		);
	}

	/**
	 * @dataProvider  provide_cursors
	 */
	public function test_cursors($space, $cursors)
	{
		$this->setup_object(array(
			'arguments' => array(function() {}, $space),
		));
		foreach ($cursors as $i => $expected_cursors)
		{
			$actual_cursors = $this->get_object_property($this->object, '_cursors')
				->getValue($this->object);
			$this->assertEquals($expected_cursors, $actual_cursors, 'Iteration '.$i);
			$this->get_object_method($this->object, '_increment_cursor')
				->invoke($this->object);
		}
	}

	public function provide_cursors()
	{
		return array(
			array(
				// 3x3 solution space
				array(
					array(0, 0, 0),
					array(0, 0, 0),
					array(0, 0, 0),
				),
				array(
					array(0, 0, 0),
					array(1, 0, 0),
					array(2, 0, 0),
					array(0, 1, 0),
					array(1, 1, 0),
					array(2, 1, 0),
					array(0, 2, 0),
					array(1, 2, 0),
					array(2, 2, 0),
					array(0, 0, 1),
					array(1, 0, 1),
					array(2, 0, 1),
					array(0, 1, 1),
					array(1, 1, 1),
					array(2, 1, 1),
					array(0, 2, 1),
					array(1, 2, 1),
					array(2, 2, 1),
					array(0, 0, 2),
					array(1, 0, 2),
					array(2, 0, 2),
					array(0, 1, 2),
					array(1, 1, 2),
					array(2, 1, 2),
					array(0, 2, 2),
					array(1, 2, 2),
					array(2, 2, 2),
					// Here it continues from the start
					array(0, 0, 0),
					array(1, 0, 0),
					array(2, 0, 0),
				),
			),
		);
	}
}