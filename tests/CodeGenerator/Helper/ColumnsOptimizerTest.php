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
				array(7, 7, 15, 12),
			),
			// Adday of mixed tokens
			array(
				array(
					'Test description',
					'',
					$this->_create_token(array('@param', 'string', '$str', 'Input string')),
					$this->_create_token(array('@return', 'integer', 'Function result')),
				),
				array(7, 7, 15, 12),
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
}