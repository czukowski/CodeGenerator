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
	public function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'name' => NULL,
			'columns' => array(),
		));
	}

	public function get_columns()
	{
		return array_merge(array('@'.$this->get('name')), $this->get('columns'));
	}

	public function render()
	{
		parent::render();
		if ( ! $this->get('name'))
		{
			return '';
		}
		return $this->render_columns();
	}
}