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
	const NAME_PATTERN = '[A-Za-z_][A-Za-z0-9_]+?';

	protected $attributes = array(
		'constraint' => NULL,
		'default' => NULL,
		'name' => NULL,
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

	public function validate_name($value)
	{
		return is_string($value) AND preg_match('#^'.self::NAME_PATTERN.'$#', $value);
	}

	public function validate_constraint($value)
	{
		return is_string($value) AND preg_match('#^(?:\\\\?'.self::NAME_PATTERN.')+$#', $value);
	}
}