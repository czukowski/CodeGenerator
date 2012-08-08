<?php
/**
 * Annotation class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Annotation extends Columns
{
	protected $attributes = array(
		'name' => NULL,
		'columns' => array(),
	);

	public function get_columns()
	{
		return array_merge(array('@'.$this->attributes['name']), $this->attributes['columns']);
	}

	public function render()
	{
		if ( ! $this->attributes['name'])
		{
			return '';
		}
		return $this->render_columns();
	}
}