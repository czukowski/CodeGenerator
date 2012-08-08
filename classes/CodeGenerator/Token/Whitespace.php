<?php
/**
 * Whitespace class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Whitespace extends Token
{
	protected $attributes = array(
		'char' => NULL,
	);
	private $_width = 1;

	public function __construct(\CodeGenerator\Config $config)
	{
		parent::__construct($config);
		$this->attributes['char'] = $this->config->get_format('column_delimiter');
	}

	public function render()
	{
		return str_repeat($this->attributes['char'], $this->width());
	}

	public function width($argument = NULL)
	{
		if ($argument === NULL)
		{
			return $this->_width;
		}
		elseif (is_int($argument))
		{
			$this->_width = $argument;
			return $this;
		}
		throw new \InvalidArgumentException($this->token().'.width() takes integer argument');
	}
}