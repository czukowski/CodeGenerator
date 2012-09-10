<?php
/**
 * Inflector test class
 * 
 * @author     Kohana Team
 * @author     Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class InflectorTest extends Testcase
{
	/**
	 * @dataProvider  provider_camelize_methods
	 */
	public function test_camelize_methods($input, $method, $expected)
	{
		$this->assertSame($expected, $this->object->$method($input));
	}

	public function provider_camelize_methods()
	{
		return array(
			// $value, $result
			array('mother cat', 'camelize', 'motherCat'),
			array('kittens in bed', 'camelize', 'kittensInBed'),
			array('mother cat', 'underscore', 'mother_cat'),
			array('kittens in bed', 'underscore', 'kittens_in_bed'),
		);
	}

	/**
	 * @dataProvider  provide_decamelize
	 */
	public function test_decamelize($input, $glue, $expected)
	{
		$this->assertSame($expected, $this->object->decamelize($input, $glue));
	}

	public function provide_decamelize()
	{
		return array(
			array('getText', '_', 'get_text'),
			array('getJSON', '_', 'get_json'),
			array('getLongText', '_', 'get_long_text'),
			array('getI18N', '_', 'get_i18n'),
			array('getL10n', '_', 'get_l10n'),
			array('getTe5t1ng', '_', 'get_te5t1ng'),
			array('OpenFile', '_', 'open_file'),
			array('CloseIoSocket', '_', 'close_io_socket'),
			array('fooBar', ' ', 'foo bar'),
			array('camelCase', '+', 'camel+case'),
		);
	}
}