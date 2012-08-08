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
	 * @dataProvider  provide_get_widths
	 */
	public function test_get_widths($widths)
	{
		$this->setup_column_object($widths);
		$this->assertEquals($widths, $this->object->get_widths());
	}

	public function provide_get_widths()
	{
		// [setup_widths]
		return array(
			array(array(1)),
			array(array(1, 2)),
			array(array(1, 2, 3)),
		);
	}

	/**
	 * @dataProvider  provide_set_widths
	 */
	public function test_widths($widths, $arguments, $expected)
	{
		$this->setup_column_object($widths);
		$this->set_expected_exception_from_argument($expected);
		$this->assertInstanceOf(__NAMESPACE__.'\Columns', $this->object->set_widths($arguments));
		$actual = $this->get_object_property($this->object, 'widths')
			->getValue($this->object);
		$this->assertEquals($actual, $expected);
	}

	public function provide_set_widths()
	{
		// [setup_widths, arguments, expected]
		return array(
			array(array(1), array(1, 2), array(1, 2)),
			array(array(1, 2), array(1), array(1)),
			array(array(), array(1, 5, 10), array(1, 5, 10)),
			array(array(), array(1, NULL, 10), new \InvalidArgumentException),
			array(array(), 'woot', new \InvalidArgumentException),
		);
	}

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
		$this->get_object_property($this->object, 'widths')
			->setValue($this->object, $widths);
	}

	protected function get_class_constructor_arguments()
	{
		$this->config = new \CodeGenerator\Config(array(
			'format' => array(
				'column_delimiter' => '-',
			),
		));
		return array($this->config);
	}
}