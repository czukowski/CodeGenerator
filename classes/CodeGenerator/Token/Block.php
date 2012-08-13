<?php
/**
 * Function argument class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

abstract class Block extends Token
{
	/**
	 * @var  integer  Token base indentation
	 */
	protected $indent = 0;

	/**
	 * Indentation getter
	 * 
	 * @return  integer
	 */
	public function get_indentation()
	{
		return $this->indent;	
	}

	/**
	 * Indentation setter
	 * 
	 * @param   integer  $level
	 * @return  Token
	 * @throws  \InvalidArgumentException
	 */
	public function set_indentation($level = NULL)
	{
		if (is_int($level))
		{
			$this->indent = $level;
			return $this;
		}
		throw new \InvalidArgumentException('Indentation must be integer');
	}

	/**
	 * Renders block of lines and indents them
	 * 
	 * @param  array  $lines
	 */
	protected function render_block($lines)
	{
		if ( ! $this->config->helper('arrays')->is_array($lines))
		{
			throw new \InvalidArgumentException($this->get_type().'.render_block() takes an array as argument');
		}
		foreach ($lines as &$line)
		{
			$line = $this->render_line($line);
		}
		return implode($this->config->get_format('line_end'), $lines);
	}

	/**
	 * Renders a single line or token
	 */
	private function render_line($line)
	{
		$line_end = $this->config->get_format('line_end');
		$indentation = str_repeat($this->config->get_format('indent'), $this->get_indentation());
		return $indentation.str_replace($line_end, $line_end.$indentation, $line);
	}
}