<?php
/**
 * TemplateParserTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TemplateParserTest extends Testcase
{
	/**
	 * @dataProvider  provide_parse
	 */
	public function test_parse($from, $string, $expected)
	{
		$actual = $this->object->parse($from, $string);
		$this->assertEquals($expected, $actual);
	}

	public function provide_parse()
	{
		$method = $this->get_sample_factory()
			->get_sample('method1');
		return array(
			// Invalid path/replacement
			array($method, '', ''),
			array($method, ' {} ', ' {} '),
			array($method, ' {{}} ', ' {{}} '),
			array($method, ' {{ }} ', ' {{ }} '),
			// Valid paths
			array($method, 'xxy{{arguments[0]name}}123', 'xxyarray values123'),
			array($method, 'xxy {{arguments[0]name|name}}-123', 'xxy array_values-123'),
			array($method, 'use {{../namespace|name}};', 'use CodeGenerator;'),
		);
	}
}