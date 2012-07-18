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
namespace CodeGenerator;

class StringTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @dataProvider  provide_is_ascii
	 */
	public function test_is_ascii($input, $expected)
	{
		$this->assertSame($expected, String::is_ascii($input));
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
		$this->assertSame($expected, String::strlen($input));
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
		$this->assertSame($expected, String::substr($input, $offset, $length));
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
		$this->setExpectedExceptionFromArgument($expected);
		$this->assertSame($expected, String::str_pad($input, $length, $pad, $type));
	}

	public function provide_str_pad()
	{
		return array(
			array('Cocoñùт', 10, 'š', STR_PAD_RIGHT, 'Cocoñùтššš'),
			array('Cocoñùт', 10, 'š', STR_PAD_LEFT,  'šššCocoñùт'),
			array('Cocoñùт', 10, 'š', STR_PAD_BOTH,  'šCocoñùтšš'),
			array('Coconut', 10, '-', STR_PAD_RIGHT, 'Coconut---'),
			array('Cocoñùт', 1, '-', STR_PAD_RIGHT, 'Cocoñùт'),
			array('Cocoñùт', 10, '-', 777, new \PHPUnit_Framework_Error('String::str_pad', 256, NULL, NULL, array())),
		);
	}
}