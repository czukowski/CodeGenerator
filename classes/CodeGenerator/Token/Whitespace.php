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
	private $width = 1;

	public function __construct(\CodeGenerator\Config $config, $width = 1)
	{
		parent::__construct($config);
		$this->set_width($width);
		$this->attributes['char'] = $this->config->get_format('column_delimiter');
	}

	public function render()
	{
		return str_repeat($this->attributes['char'], $this->get_width());
	}

	/**
	 * @return  integer
	 */
	public function get_width()
	{
		return $this->width;
	}

	/**
	 * @param   integer  $value
	 * @return  \CodeGenerator\Token\Whitespace
	 * @throws  \InvalidArgumentException
	 */
	public function set_width($value)
	{
		if (is_int($value))
		{
			$this->width = $value;
			return $this;
		}
		throw new \InvalidArgumentException($this->token().'.set_width() takes integer argument');
	}
}