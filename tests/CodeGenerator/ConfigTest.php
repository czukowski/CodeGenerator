<?php
/**
 * CodeGenerator config class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class ConfigTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @dataProvider  provide_helper
	 */
	public function test_helper($name, $expected, $is_singleton)
	{
		$this->setup_object();
		$this->set_expected_exception_from_argument($expected);
		$actual1 = $this->object->helper($name);
		$actual2 = $this->object->helper($name);
		$this->assertInstanceOf($expected, $actual1);
		if ($is_singleton === TRUE)
		{
			$this->assertSame($actual1, $actual2);
		}
		else
		{
			$this->assertNotSame($actual1, $actual2);
		}
	}

	public function provide_helper()
	{
		return array(
			array('string', '\CodeGenerator\Helper\String', TRUE),
			array('columnsOptimizer', '\CodeGenerator\Helper\ColumnsOptimizer', FALSE),
			array('fake', new \InvalidArgumentException, NULL),
		);
	}

	/**
	 * @dataProvider  provide_get
	 */
	public function test_get($config, $path, $default, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($config),
		));
		$actual = $this->object->get($path, $default);
		$this->assertEquals($expected, $actual);
	}

	public function provide_get()
	{
		$config = $this->get_config();
		$default = array();
		return array(
			array($default, 'format.brace_close', NULL, '}'),
			array($config, 'format.brace_close', '}', ' } '),
			array($default, 'options.column_min_space', NULL, 2),
			array($config, 'options.column_min_space', 2, 1),
			array($default, 'format.some.thing', '+=+', '+=+'),
			array($config, 'options.some.thing', '+=+', '+=+'),
		);
	}

	/**
	 * @dataProvider  provide_get_format
	 */
	public function test_get_format($config, $path, $default, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($config),
		));
		$actual = $this->object->get_format($path, $default);
		$this->assertEquals($expected, $actual);
	}

	public function provide_get_format()
	{
		$config = $this->get_config();
		$default = array();
		return array(
			array($default, 'brace_close', NULL, '}'),
			array($config, 'brace_close', '}', ' } '),
			array($default, 'some.format', '+=+', '+=+'),
			array($config, 'some.format', '+=+', '+=+'),
		);
	}

	/**
	 * @dataProvider  provide_get_options
	 */
	public function test_get_options($config, $path, $default, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($config),
		));
		$actual = $this->object->get_options($path, $default);
		$this->assertEquals($expected, $actual);
	}

	public function provide_get_options()
	{
		$config = $this->get_config();
		$default = array();
		return array(
			array($default, 'column_min_space', NULL, 2),
			array($config, 'column_min_space', 2, 1),
			array($default, 'some.option', '+=+', '+=+'),
			array($config, 'some.option', '+=+', '+=+'),
		);
	}

	private function get_config()
	{
		return array(
			'format' => array(
				'brace_close' => ' } ',
				'brace_open' => ' {',
				'column_delimiter' => '+',
				'indent' => '    ',
			),
			'options' => array(
				'charset' => 'utf-8',
				'column_min_space' => 1,
				'line_width' => 100,
				'wrap_comment_text' => TRUE,
			),
		);
	}
}