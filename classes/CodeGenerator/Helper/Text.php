<?php
/**
 * Text functions helper class. Based on Kohana Framework Text class.
 * 
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class Text extends \CodeGenerator\Singleton
{
	/**
	 * Splits the long text into multiple lines
	 * 
	 *     $text = $text->word_wrap($text);
	 * 
	 * @param   string   $str      Text to word wrap
	 * @param   integer  $limit    Number of characters to limit to
	 * @param   string   $newline  Newline character
	 * @return  string
	 */
	public function word_wrap($str, $limit = 100, $newline = "\n")
	{
		$text = '';
		$string_helper = $this->config->helper('string');
		$string_length = $string_helper->strlen($str);
		do
		{
			$line_text = $this->limit_chars($str, $limit, '', TRUE);
			$line_length = $string_helper->strlen($line_text);
			$str = ltrim($string_helper->substr($str, $line_length));
			$string_length = $string_helper->strlen($str);
			$text .= $line_text.$newline;
		}
		while ($string_length > 0);
		return rtrim($text, $newline);
	}

	/**
	 * Limits a phrase to a given number of characters.
	 *
	 *     $text = $text->limit_chars($text);
	 *
	 * @param   string   $str             Phrase to limit characters of
	 * @param   integer  $limit           Number of characters to limit to
	 * @param   string   $end_char        End character or entity
	 * @param   boolean  $preserve_words  Enable or disable the preservation of words while limiting
	 * @return  string
	 */
	public function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		$end_char = ($end_char === NULL) ? '…' : $end_char;

		$limit = (int) $limit;

		if (trim($str) === '' OR $this->config->helper('string')->strlen($str) <= $limit)
		{
			return $str;
		}

		if ($limit <= 0)
		{
			return $end_char;
		}

		if ($preserve_words === FALSE)
		{
			return rtrim($this->config->helper('string')->substr($str, 0, $limit)).$end_char;
		}

		// Don't preserve words. The limit is considered the top limit.
		// No strings with a length longer than $limit should be returned.
		if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))
		{
			return $end_char;
		}

		return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);
	}
}