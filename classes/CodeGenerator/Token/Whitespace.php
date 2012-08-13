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
		'width' => 1,
	);
	protected $validation = array(
		'width' => 'integer',
	);

	public function __construct(\CodeGenerator\Config $config)
	{
		parent::__construct($config);
		$this->attributes['char'] = $this->config->get_format('column_delimiter');
	}

	public function render()
	{
		return str_repeat($this->attributes['char'], $this->attributes['width']);
	}
}