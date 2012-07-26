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
	protected $attributes = array(
		'constraint' => NULL,
		'default' => NULL,
		'name' => NULL,
		'comment' => NULL,
	);
	protected $validation = array(
		'constraint' => 'constraint',
		'name' => 'name',
	);

	public function render()
	{
		if ( ! $this->attributes['name'])
		{
			return '';
		}
		return ($this->attributes['constraint'] ? $this->attributes['constraint'].' ' : '')
			.'$'.$this->attributes['name']
			.($this->attributes['default'] ? ' = '.$this->attributes['default'] : '');
	}
}