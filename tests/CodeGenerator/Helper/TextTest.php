<?php
/**
 * Arrays class tests
 * 
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @copyright  (c) 2008-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TextTest extends Testcase
{
	/**
	 * @dataProvider  provider_limit_chars
	 */
	public function test_limit_chars($expected, $str, $limit, $end_char, $preserve_words)
	{
		$this->assertSame($expected, $this->object->limit_chars($str, $limit, $end_char, $preserve_words));
	}

	public function provider_limit_chars()
	{
		return array
		(
			array('', '', 100, NULL, FALSE),
			array('…', 'BOO!', -42, NULL, FALSE),
			array('making php bet…', 'making php better for the sane', 14, NULL, FALSE),
			array('Garçon! Un café s.v.p.', 'Garçon! Un café s.v.p.', 50, '__', FALSE),
			array('Garçon!__', 'Garçon! Un café s.v.p.', 8, '__', FALSE),
			// @issue 3238
			array('making php…', 'making php better for the sane', 14, NULL, TRUE),
			array('Garçon!__', 'Garçon! Un café s.v.p.', 9, '__', TRUE),
			array('Garçon!__', 'Garçon! Un café s.v.p.', 7, '__', TRUE),
			array('__', 'Garçon! Un café s.v.p.', 5, '__', TRUE),
		);
	}

	/**
	 * @dataProvider  provide_word_wrap
	 */
	public function test_word_wrap($expected, $str, $limit, $endline_char)
	{
		$this->assertSame($expected, $this->object->word_wrap($str, $limit, $endline_char));
	}

	public function provide_word_wrap()
	{
		return array(
			array("making php better\nfor the sane", 'making php better for the sane', 19, "\n"),
			array("making\nphp\nbetter\nfor\nthe\nsane", 'making php better for the sane', 6, "\n"),
			array("making\nphp\nbetter\nfor\nthe\nsane", 'making  php  better  for  the  sane', 6, "\n"),
		);
	}
}