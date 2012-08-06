<?php
/**
 * SigmaTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Math;

class SigmaTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * How sigma function works:
	 * 
	 * y = a * x + b
	 * y1 = a * x1 + b
	 * y2 = a * x2 + b
	 * b = y1 - a * x1
	 * y2 = a * x2 + y1 - a * x1
	 * y2 - y1 = a * (x2 - x1)
	 * a = (y2 - y1) / (x2 - x1)
	 * 
	 * The result is bounded by y1 and y2
	 * 
	 * @dataProvider  provide_value
	 */
	public function test_value($argument, $x1, $x2, $y1, $y2, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($x1, $x2, $y1, $y2),
		));
		$actual = $this->object->value($argument);
		$this->assertEquals($expected, $actual, '', 0.0001);
	}

	public function provide_value()
	{
		// [argument, x1, x2, y1, y2, expected]
		return array(
			// Non-decreasing
			array(0, 0, 1, 0, 1, 0),
			array(1, 0, 1, 0, 1, 1),
			array(0.5, 0, 1, 0, 1, 0.5),
			array(0.9, 0, 1, 0, 1, 0.9),
			array(-1, -1, 3, 0, 1, 0),
			array(0, -1, 3, 0, 1, 0.25),
			array(3, -1, 3, 0, 1, 1),
			// Truncated
			array(-1, 0, 1, 0, 1, 0),
			array(3, 0, 1, 0, 1, 1),
			array(-7, -1, 3, 0, 1, 0),
			array(9, -1, 3, 0, 1, 1),
			// Non-increasing
			array(0, 0, 1, 1, 0, 1),
			array(0.5, 0, 1, 1, 0, 0.5),
			array(0.9, 0, 1, 1, 0, 0.1),
			array(1, 0, 1, 1, 0, 0),
			array(-1, -1, 3, 1, 0, 1),
			array(0, -1, 3, 1, 0, 0.75),
			array(3, -1, 3, 1, 0, 0),
			// Truncated
			array(-1, 0, 1, 1, 0, 1),
			array(3, 0, 1, 1, 0, 0),
			array(-7, -1, 3, 1, 0, 1),
			array(9, -1, 3, 1, 0, 0),
		);
	}
}