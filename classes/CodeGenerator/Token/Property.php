<?php
/**
 * Property token class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Property extends Token
{
	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'comment' => NULL,
			'constraint' => NULL,
			'access' => 'public',
			'static' => NULL,
			'name' => NULL,
			'default' => NULL,
		));
		$this->initialize_transformations(array(
			'comment' => 'DocComment',
		));
		$this->initialize_validation(array(
			'access' => 'access',
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
		return implode($this->config->get_format('line_end'), array_filter(array(
			$this->render_comment(),
			$this->render_declaration(),
		)));
	}

	private function render_comment()
	{
		if (($comment = $this->get('comment')))
		{
			return $comment;
		}
	}

	private function render_declaration()
	{
		return implode(' ', array_filter(array(
			$this->get('access'),
			$this->render_boolean_attribute('static'),
			'$'.$this->config->helper('tokenPartsRenderer')
				->render_name($this->get('name')),
			($this->get('default') ? '= '.$this->get('default') : '')
		))).';';
	}
}