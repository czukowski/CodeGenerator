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

class Argument extends Token
{
	protected function initialize()
	{
		$this->initialize_attributes(array(
			'constraint' => NULL,
			'default' => NULL,
			'name' => NULL,
			'comment' => NULL,
		));
		$this->initialize_validation(array(
			'constraint' => 'constraint',
			'name' => 'name',
		));
	}

	public function render()
	{
		if ( ! $this->get('name'))
		{
			return '';
		}
		return ($this->get('constraint') ? $this->get('constraint').' ' : '')
			.'$'.$this->get('name')
			.($this->get('default') ? ' = '.$this->get('default') : '');
	}
}