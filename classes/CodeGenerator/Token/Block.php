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
}