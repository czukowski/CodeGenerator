<?php
/**
 * ColumnsTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class ColumnsOptimizerTest extends Testcase
{
	/**
	 * @dataProvider  provide_tokens
	 */
	public function test_align($tokens, $expected)
	{
		$this->setExpectedExceptionFromArgument($expected);
		$this->object->tokens($tokens);
		$actual = $this->object->align();
		$this->assertSame($this->object, $actual);
		$tokens = $this->_object_property($this->object, '_column_tokens')
			->getValue($this->object);
		foreach ($tokens as $token)
		{
			$this->assertEquals($expected, $token->widths());
		}
	}

	/**
	 * @dataProvider  provide_tokens
	 */
	public function test_tokens($tokens, $expected)
	{
		$this->setExpectedExceptionFromArgument($expected);
		$actual = $this->object->tokens($tokens);
		$this->assertSame($this->object, $actual);
		$this->assertSame($tokens, $this->object->tokens());
	}

	public function provide_tokens()
	{
		return array(
			// Array of column tokens
			array(
				array(
					$this->_create_token(array('@param', 'string', '$str', 'Input string')),
					$this->_create_token(array('@return', 'integer', 'Function result')),
				),
				array(7, 7, 4, 12),
			),
			// Adday of mixed tokens
			array(
				array(
					'Test description',
					'',
					$this->_create_token(array('@param', 'string', '$str', 'Input string')),
					$this->_create_token(array('@return', 'integer', 'Function result')),
				),
				array(7, 7, 4, 12),
			),
			// Array of column tokens
			array(
				array(
					$this->_create_token(array('@param', 'string', '$input', 'Input string')),
					$this->_create_token(array('@param', 'integer', '&$output', 'Error code')),
					$this->_create_token(array('@param', 'bool', '$flag', 'Flags')),
					$this->_create_token(array('@return', 'integer')),
				),
				array(7, 7, 8, 12),
			),
			// Array of column tokens
			array(
				array(
					$this->_create_token(array('@param', 'string', '$input', 'Input string')),
					$this->_create_token(array('@param', 'integer', '&$output', 'Error code')),
					$this->_create_token(array('@throws', '\InvalidArgumentException')),
				),
				array(7, 7, 8, 12),
			),
			// Not array
			array(
				$this->_create_token(array('@param', 'string', '$str', 'Input string')),
				new \InvalidArgumentException,
			),
		);
	}

	/**
	 * @param   array  $columns
	 * @return  \CodeGenerator\Token\Columns
	 */
	private function _create_token($columns)
	{
		$token = $this->get_mock(array(
			'classname' => '\CodeGenerator\Token\Columns',
		));
		$token->expects($this->any())
			->method('columns')
			->will($this->returnValue($columns));
		$token->expects($this->any())
			->method('render')
			->will($this->returnValue(''));
		return $token;
	}

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
	 * @dataProvider  provide_sigma
	 */
	public function test_sigma($argument, $x1, $x2, $y1, $y2, $expected)
	{
		$actual = $this->_object_method($this->object, '_sigma')
			->invoke($this->object, $argument, $x1, $x2, $y1, $y2);
		$this->assertEquals($expected, $actual, '', 0.0001);
	}

	public function provide_sigma()
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