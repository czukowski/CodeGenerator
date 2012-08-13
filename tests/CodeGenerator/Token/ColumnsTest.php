<?php
/**
 * ColumnsTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class ColumnsTest extends Testcase
{
	/**
	 * @dataProvider  provide_render_columns
	 */
	public function test_render_columns($widths, $columns, $expected)
	{
		$this->setup_column_object($widths, $columns);
		$actual = $this->get_object_method($this->object, 'render_columns')
			->invoke($this->object);
		$this->assertEquals($expected, $actual);
	}

	public function provide_render_columns()
	{
		// [widths, columns, expected]
		return array(
			array(
				array(), array(), '',
			),
			array(
				array(), array('@var', 'array'), '@var--array',
			),
			array(
				array(1, 2), array('@var', 'array'), '@var---array',
			),
			array(
				array(2, 2), array('@var', 'array'), '@var----array',
			),
			array(
				array(3, 2), array('@var', 'array'), '@var-----array',
			),
			array(
				array(4, 2), array('@var', 'array'), '@var--array',
			),
			array(
				array(5, 2), array('@var', 'array'), '@var---array',
			),
			array(
				array(10, 10), array('@var', 'array'), '@var--------array',
			),
		);
	}

	private function setup_column_object($widths = array(), $columns = array())
	{
		$this->setup_mock();
		$this->object->expects($this->any())
			->method('get_columns')
			->will($this->returnValue($columns));
		$this->object->set('widths', $widths);
	}

	protected function setup_config()
	{
		$this->config = new \CodeGenerator\Config(array(
			'format' => array(
				'column_delimiter' => '-',
			),
		));
	}

	/**
	 * @dataProvider  provide_validate_widths
	 */
	public function test_validate_widths($values, $expected)
	{
		$this->setup_mock();
		$actual = $this->object->validate_widths($values);
		$this->assertEquals($expected, $actual);
	}

	public function provide_validate_widths()
	{
		return array(
			array(array(), TRUE),
			array(array(2, 3), TRUE),
			array('something', FALSE),
			array(array(1, TRUE), FALSE),
		);
	}
}