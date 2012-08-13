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
	protected function initialize()
	{
		$this->initialize_attributes(array(
			'char' => $this->config->get_format('column_delimiter'),
			'width' => 1,
		));
		$this->initialize_validation(array(
			'width' => 'integer',
		));
	}

	public function render()
	{
		return str_repeat($this->get('char'), $this->get('width'));
	}
}