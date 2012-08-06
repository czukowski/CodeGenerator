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
	 * @dataProvider  provide_auto_width
	 */
	public function test_auto_width($tokens, $expected)
	{
		$this->setExpectedExceptionFromArgument($expected);
		$this->object->auto_width($tokens);
		$tokens = $this->_object_property($this->object, '_tokens')
			->getValue($this->object);
		foreach ($tokens as $token)
		{
			$this->assertEquals($expected, $token->widths());
		}
	}

	public function provide_auto_width()
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
			// Array of mixed tokens
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

	/**
	 * @dataProvider  provide_align
	 */
	public function test_align($token, $widths, $expected)
	{
		$token->widths($widths);
		$actual = $this->object->align($token);
		$this->assertEquals($expected, implode('', $actual));
	}

	public function provide_align()
	{
		$param = $this->_create_token(array('@param', 'string', '$str', 'Input string'));
		$return = $this->_create_token(array('@return', 'integer', 'Function result'));
		$return_short = $this->_create_token(array('@return', 'integer'));
		return array(
			// '1  1  1  1'
			// '@param   string  $str  Input string'
			array($param, array(1, 1, 1, 1), '@param   string  $str  Input string'),
			// '666666  666666  4444  121212121212'
			// '@param  string  $str  Input string'
			array($param, array(6, 6, 4, 12), '@param  string  $str  Input string'),
			// '7777777  7777777  4444  121212121212'
			// '@param   string   $str  Input string'
			array($param, array(7, 7, 4, 12), '@param   string   $str  Input string'),
			// '1010101010  1010101010  1010101010  1010101010'
			// '@param      string      $str        Input string'
			array($param, array(10, 10, 10, 10), '@param      string      $str        Input string'),
			// '666666  666666  4444'
			// '@return         integer  Function result'
			array($return, array(6, 6, 4), '@return         integer  Function result'),
			// '7777777  666666  4444'
			// '@return  integer       Function result'
			array($return, array(7, 6, 4), '@return  integer       Function result'),
			// '7777777  7777777  4444'
			// '@return  integer  Function result'
			array($return, array(7, 7, 4), '@return  integer  Function result'),
			// '333  333'
			// '@return   integer'
			array($return_short, array(3, 3), '@return   integer'),
			// '666666  666666'
			// '@return         integer'
			array($return_short, array(6, 6), '@return         integer'),			
		);
	}
}