<?php
/**
 * Template parser class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;
use CodeGenerator\Token;

class TemplateParser extends \CodeGenerator\Singleton
{
	private $start_delimiter = '{{';
	private $end_delimiter = '}}';

	/**
	 * Parse and replace template values.
	 * 
	 * @param   Token   $from  source token
	 * @param   string  $str   string to parse
	 * @return  string
	 */
	public function parse(Token\Token $from, $str)
	{
		$strings = $this->config->helper('string');
		$cursor = 0;
		while ($cursor < $strings->strlen($str))
		{
			if (
				($start = $strings->strpos($str, $this->start_delimiter, $cursor)) === FALSE
				OR ($offset = $start + strlen($this->start_delimiter)) === FALSE
				OR ($end = $strings->strpos($str, $this->end_delimiter, $offset)) === FALSE
			)
			{
				break;
			}
			$replacement = $this->get_replacement($from, $strings->substr($str, $offset, $end - $offset));
			$str = $strings->substr($str, 0, $start)
				.$replacement
				.$strings->substr($str, $end + strlen($this->end_delimiter));
			$cursor = $start + $strings->strlen($replacement);
		}
		return $str;
	}

	/**
	 * Tries to replace a specified string with a value obtained from the TokenTree helper.
	 * 
	 * @param   Token   $from
	 * @param   string  $path
	 * @return  string
	 */
	private function get_replacement($from, $path)
	{
		try
		{
			return (string) $this->config->helper('tokenTree')
				->find_path($from, $path);
		}
		catch (\Exception $e)
		{
			// Return the original string
			return $this->start_delimiter.$path.$this->end_delimiter;
		}
	}
}