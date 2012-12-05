<?php
/**
 * Strings class
 * 
 * @author     Kohana Team
 * @copyright  (c) 2008-2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class StringTest extends Testcase
{
	/**
	 * @dataProvider  provide_is_ascii
	 */
	public function test_is_ascii($input, $expected)
	{
		$this->assertSame($expected, $this->object->is_ascii($input));
	}

	public function provide_is_ascii()
	{
		return array(
			array("\0", TRUE),
			array("\$eno\r", TRUE),
			array('Señor', FALSE),
			array(array('Se', 'nor'), TRUE),
			array(array('Se', 'ñor'), FALSE),
		);
	}

	/**
	 * @dataProvider  provide_strlen
	 */
	public function test_strlen($input, $expected)
	{
		$this->assertSame($expected, $this->object->strlen($input));
	}

	public function provide_strlen()
	{
		return array(
			array('Cocoñùт', 7),
			array('Coconut', 7),
		);
	}

	/**
	 * @dataProvider  provide_substr
	 */
	public function test_substr($input, $offset, $length, $expected)
	{
		$this->assertSame($expected, $this->object->substr($input, $offset, $length));
	}

	public function provide_substr()
	{
		return array(
			array('Cocoñùт', 3, 2, 'oñ'),
			array('Cocoñùт', 3, 9, 'oñùт'),
			array('Cocoñùт', 3, NULL, 'oñùт'),
			array('Cocoñùт', 3, -2, 'oñ'),
		);
	}

	/**
	 * @dataProvider  provide_str_pad
	 */
	public function test_str_pad($input, $length, $pad, $type, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$this->assertSame($expected, $this->object->str_pad($input, $length, $pad, $type));
	}

	public function provide_str_pad()
	{
		return array(
			array('Cocoñùт', 10, 'š', STR_PAD_RIGHT, 'Cocoñùтššš'),
			array('Cocoñùт', 10, 'š', STR_PAD_LEFT,  'šššCocoñùт'),
			array('Cocoñùт', 10, 'š', STR_PAD_BOTH,  'šCocoñùтšš'),
			array('Coconut', 10, '-', STR_PAD_RIGHT, 'Coconut---'),
			array('Cocoñùт', 1, '-', STR_PAD_RIGHT, 'Cocoñùт'),
			array('Cocoñùт', 10, '-', 777, new \PHPUnit_Framework_Error('String.str_pad', 256, NULL, NULL, array())),
		);
	}

	/**
	 * @dataProvider  provide_strpos
	 */
	public function test_strpos($input, $str, $offset, $expected)
	{
		$this->assertSame($expected, $this->object->strpos($input, $str, $offset));
	}

	public function provide_strpos()
	{
		return array(
			array('Coconut', 'o', 0, 1),
			array('Cocoñùт', 'o', 0, 1),
			array('Cocoñùт', 'ñ', 1, 4),
		);
	}

	/**
	 * @dataProvider  provide_trim
	 */
	public function test_trim($input, $input2, $expected)
	{
		$this->assertSame($expected, $this->object->trim($input, $input2));
	}

	public function provide_trim()
	{
		return array(
			array(' bar ', NULL, 'bar'),
			array('bar',   'b',  'ar'),
			array('barb',  'b',  'ar'),
		);
	}

	/**
	 * @dataProvider  provide_ltrim
	 */
	public function test_ltrim($input, $charlist, $expected)
	{
		$this->assertSame($expected, $this->object->ltrim($input, $charlist));
	}

	public function provide_ltrim()
	{
		return array(
			array(' bar ', NULL, 'bar '),
			array('bar',   'b',  'ar'),
			array('barb',  'b',  'arb'),
			array('ñùт',   'ñ',  'ùт'),
		);
	}

	/**
	 * @dataProvider  provide_rtrim
	 */
	public function test_rtrim($input, $input2, $expected)
	{
		$this->assertSame($expected, $this->object->rtrim($input, $input2));
	}

	public function provide_rtrim()
	{
		return array(
			array(' bar ', NULL, ' bar'),
			array('bar',   'b',  'bar'),
			array('barb',  'b',  'bar'),
			array('Cocoñùт',  'т',  'Cocoñù'),
		);
	}
}