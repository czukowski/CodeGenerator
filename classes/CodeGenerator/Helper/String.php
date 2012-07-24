<?php
/**
 * String functions helper class. Based on Kohana Framework UTF8 class.
 * 
 * @author     Kohana Team
 * @author     Harry Fuecks <hfuecks@gmail.com>
 * @copyright  (c) 2007-2010 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class String extends \CodeGenerator\Singleton
{	
	/**
	 * Tests whether a string contains only 7-bit ASCII bytes. This is used to
	 * determine when to use native functions or UTF-8 functions.
	 * 
	 *     $ascii = $string->is_ascii($str);
	 * 
	 * @param   mixed    String or array of strings to check
	 * @return  boolean
	 */
	public function is_ascii($str)
	{
		if (is_array($str))
		{
			$str = implode($str);
		}

		return ! preg_match('/[^\x00-\x7F]/S', $str);
	}

	/**
	 * Returns the length of the given string. This is a UTF8-aware version
	 * of [strlen](http://php.net/strlen).
	 * 
	 * @param   string   $str
	 * @return  integer
	 */
	public function strlen($str)
	{
		return mb_strlen($str, $this->config->options('charset'));
	}

	/**
	 * Pads a UTF-8 string to a certain length with another string. This is a
	 * UTF8-aware version of [str_pad](http://php.net/str_pad).
	 * 
	 *     $str = $string->str_pad($str, $length);
	 * 
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 * @param   string   Input string
	 * @param   integer  Desired string length after padding
	 * @param   string   String to use as padding
	 * @param   string   Padding type: STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH
	 * @return  string
	 */
	public function str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
	{
		if ($this->is_ascii($str) AND $this->is_ascii($pad_str))
		{
			return str_pad($str, $final_str_length, $pad_str, $pad_type);
		}

		$str_length = $this->strlen($str);

		if ($final_str_length <= 0 OR $final_str_length <= $str_length)
		{
			return $str;
		}

		$pad_str_length = $this->strlen($pad_str);
		$pad_length = $final_str_length - $str_length;

		if ($pad_type == STR_PAD_RIGHT)
		{
			$repeat = ceil($pad_length / $pad_str_length);
			return $this->substr($str.str_repeat($pad_str, $repeat), 0, $final_str_length);
		}

		if ($pad_type == STR_PAD_LEFT)
		{
			$repeat = ceil($pad_length / $pad_str_length);
			return $this->substr(str_repeat($pad_str, $repeat), 0, floor($pad_length)).$str;
		}

		if ($pad_type == STR_PAD_BOTH)
		{
			$pad_length /= 2;
			$pad_length_left = floor($pad_length);
			$pad_length_right = ceil($pad_length);
			$repeat_left = ceil($pad_length_left / $pad_str_length);
			$repeat_right = ceil($pad_length_right / $pad_str_length);

			$pad_left = $this->substr(str_repeat($pad_str, $repeat_left), 0, $pad_length_left);
			$pad_right = $this->substr(str_repeat($pad_str, $repeat_right), 0, $pad_length_right);
			return $pad_left.$str.$pad_right;
		}

		trigger_error('String.str_pad(): Unknown padding type ('.$pad_type.')', E_USER_ERROR);
	}

	/**
	 * Returns part of a UTF-8 string. This is a UTF8-aware version
	 * of [substr](http://php.net/substr).
	 * 
	 *     $sub = $string->substr($str, $offset);
	 * 
	 * @author  Chris Smith <chris@jalakai.co.uk>
	 * @param   string   Input string
	 * @param   integer  Offset
	 * @param   integer  Length limit
	 * @return  string
	 */
	public function substr($str, $offset, $length = NULL)
	{
		return ($length === NULL)
			? mb_substr($str, $offset, mb_strlen($str), $this->config->options('charset'))
			: mb_substr($str, $offset, $length, $this->config->options('charset'));
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning and
	 * end of a string. This is a UTF8-aware version of [trim](http://php.net/trim).
	 *
	 *     $str = $string->trim($str);
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 * @param   string  Input string
	 * @param   string  String of characters to remove
	 * @return  string
	 */
	public function trim($str, $charlist = NULL)
	{
		if ($charlist === NULL)
		{
			return trim($str);
		}

		return $this->ltrim($this->rtrim($str, $charlist), $charlist);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning of
	 * a string. This is a UTF8-aware version of [ltrim](http://php.net/ltrim).
	 *
	 *     $str = $string->ltrim($str);
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 * @param   string  Input string
	 * @param   string  String of characters to remove
	 * @return  string
	 */
	public function ltrim($str, $charlist = NULL)
	{
		if ($charlist === NULL)
		{
			return ltrim($str);
		}
		if ($this->is_ascii($charlist))
		{
			return ltrim($str, $charlist);
		}

		$charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

		return preg_replace('/^['.$charlist.']+/u', '', $str);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the end of a string.
	 * This is a UTF8-aware version of [rtrim](http://php.net/rtrim).
	 *
	 *     $str = $string->rtrim($str);
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 * @param   string  Input string
	 * @param   string  String of characters to remove
	 * @return  string
	 */
	public function rtrim($str, $charlist = NULL)
	{
		if ($charlist === NULL)
		{
			return rtrim($str);
		}
		if ($this->is_ascii($charlist))
		{
			return rtrim($str, $charlist);
		}

		$charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

		return preg_replace('/['.$charlist.']++$/uD', '', $str);
	}
}