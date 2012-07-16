<?php
/**
 * Code Format config class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class FormatTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @dataProvider  provide_get_set
	 */
	public function test_get_set($arguments, $key, $value, $expected)
	{
		$this->setup_object(array(
			'arguments' => $arguments,
		));
		if ($value !== NULL)
		{
			$this->object->{$key} = $value;
		}
		$this->setExpectedExceptionFromArgument($expected);
		$this->assertEquals($expected, $this->object->{$key});
	}

	public function provide_get_set()
	{
		// [arguments, key, set_value, expect_value]
		return array(
			// Abstract values
			array(array(), 'key1', NULL, new \OutOfBoundsException),
			array(array(), 'key1', 'value1', 'value1'),
			array(array(array('key1' => 'VALUE1')), 'key1', NULL, 'VALUE1'),
			array(array(array('key1' => 'VALUE1')), 'key1', 'value1', 'value1'),
			// Indent has hardcoded default value
			array(array(), 'indent', NULL, "\t"),
			array(array(), 'indent', '    ', '    '),
		);
	}
}