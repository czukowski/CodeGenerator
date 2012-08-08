<?php
/**
 * MatrixTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Math;

class MatrixTest extends \CodeGenerator\Framework\Testcase
{
	private $testMatrices = array(
		'square' => array(
			array(1, 2, 3),
			array(4, 5, 6),
			array(7, 8, 9),
		),
		'transponed' => array(
			array(1, 4, 7),
			array(2, 5, 8),
			array(3, 6, 9),
		),
		'jagged' => array(
			array(1, 2, 3),
			array(1),
			array(1, 2),
		),
		'fixed' => array(
			array(1, 2, 3),
			array(1, NULL, NULL),
			array(1, 2, NULL),
		),
		'rectangle' => array(
			array(1, 0, 0, 0, 0),
			array(0, 1, 0, 0, 0),
			array(0, 0, 1, 0, 0),
		),
		'rectangle_t' => array(
			array(1, 0, 0),
			array(0, 1, 0),
			array(0, 0, 1),
			array(0, 0, 0),
			array(0, 0, 0),
		),
		'invalid' => array(
			1, 2, 3,
		),
		'eye' => array(
			array(1, 0, 0),
			array(0, 1, 0),
			array(0, 0, 1),
		),
	);

	/**
	 * @dataProvider  provide_construct
	 */
	public function test_construct($matrix, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$this->setup_object(array('arguments' => array($matrix)));
		$actual = $this->get_object_property($this->object, '_matrix')
			->getValue($this->object);
		$this->assertSame($expected, $actual);
	}

	public function provide_construct()
	{
		return array(
			array($this->testMatrices['square'], $this->testMatrices['square']),
			array($this->testMatrices['jagged'], $this->testMatrices['fixed']),
			array($this->testMatrices['rectangle'], $this->testMatrices['rectangle']),
			array($this->testMatrices['invalid'], new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_get_dimension
	 */
	public function test_get_dimension($matrix, $arguments, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$this->setup_object(array('arguments' => array($matrix)));
		$actual = $this->get_object_method($this->object, 'get_dimension')
			->invokeArgs($this->object, $arguments);
		$this->assertSame($expected, $actual);
	}

	public function provide_get_dimension()
	{
		return array(
			array($this->testMatrices['square'], array(0), 3),
			array($this->testMatrices['square'], array(1), 3),
			array($this->testMatrices['square'], array(), array(3, 3)),
			array($this->testMatrices['rectangle'], array(0), 5),
			array($this->testMatrices['rectangle'], array(1), 3),
			array($this->testMatrices['rectangle'], array(), array(5, 3)),
			array($this->testMatrices['rectangle'], array(NULL), new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_get
	 */
	public function test_get($matrix, $i, $j, $expected)
	{
		$this->setup_object(array('arguments' => array($matrix)));
		$this->set_expected_exception_from_argument($expected);
		$actual = $this->object->get($i, $j);
		$this->assertSame($expected, $actual);
	}

	public function provide_get()
	{
		return array(
			array($this->testMatrices['square'], NULL, NULL, $this->testMatrices['square']),
			array($this->testMatrices['square'], NULL, 0, array(1, 4, 7)),
			array($this->testMatrices['square'], 0, NULL, array(1, 2, 3)),
			array($this->testMatrices['square'], 0, 0, 1),
			array($this->testMatrices['square'], 3, NULL, new \InvalidArgumentException),
			array($this->testMatrices['square'], NULL, 3, new \InvalidArgumentException),
			array($this->testMatrices['square'], 3, 3, new \InvalidArgumentException),
		);
	}

	/**
	 * @dataProvider  provide_set
	 */
	public function test_set($matrix, $i, $j, $value, $expected)
	{
		$this->setup_object(array('arguments' => array($matrix)));
		$this->set_expected_exception_from_argument($expected);
		$this->object->set($i, $j, $value);
		$actual = $this->object->get();
		$this->assertSame($expected, $actual);
	}

	public function provide_set()
	{
		return array(
			array($this->testMatrices['square'], NULL, NULL, $this->testMatrices['jagged'], $this->testMatrices['fixed']),
			array($this->testMatrices['square'], NULL, 0, array(0, 0, 0), array(array(0, 2, 3), array(0, 5, 6), array(0, 8, 9))),
			array($this->testMatrices['square'], 0, NULL, array(0, 0, 0), array(array(0, 0, 0), array(4, 5, 6), array(7, 8, 9))),
			array($this->testMatrices['square'], 1, 1, 0, array(array(1, 2, 3), array(4, 0, 6), array(7, 8, 9))),
		);
	}

	/**
	 * @dataProvider  provide_transpose
	 */
	public function test_transpose($matrix, $expected)
	{
		$this->setup_object(array('arguments' => array($matrix)));
		$actual = $this->object->transpose();
		$this->assertSame($expected, $actual->get());
	}

	public function provide_transpose()
	{
		return array(
			array($this->testMatrices['square'], $this->testMatrices['transponed']),
			array($this->testMatrices['eye'], $this->testMatrices['eye']),
			array($this->testMatrices['rectangle'], $this->testMatrices['rectangle_t']),
		);
	}
}