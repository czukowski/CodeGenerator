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
		parent::initialize();
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
		return $this->render_constraint()
			.$this->render_name()
			.$this->render_default();
	}

	/**
	 * Render default part
	 */
	private function render_constraint()
	{
		if (($constraint = $this->get('constraint')))
		{
			if ($constraint !== 'array')
			{
				$constraint = $this->config->helper('tokenPartsRenderer')
					->render_class_name($constraint);
			}
			return $constraint.' ';
		}
		return '';
	}

	/**
	 * Render variable name part
	 */
	private function render_name()
	{
		return '$'.$this->config->helper('tokenPartsRenderer')
			->render_name($this->get('name'));
	}

	/**
	 * Render default variable value part
	 */
	private function render_default()
	{
		if (($default = $this->get('default')))
		{
			return ' = '.$default;
		}
		return '';
	}
}