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
	protected $transform = array(
		'comment' => 'DocComment',
	);

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
		$this->initialize_validation(array(
			'access' => 'access',
			'constraint' => 'constraint',
			'name' => 'name',
		));
	}

	public function render()
	{
		parent::render();
		if ( ! $this->get('name'))
		{
			return '';
		}
		return implode($this->config->get_format('line_end'), array_filter(array(
			(string) $this->get('comment'),
			$this->render_declaration(),
		)));
	}

	private function render_declaration()
	{
		return implode(' ', array_filter(array(
			$this->get('access'),
			$this->render_boolean_attribute('static'),
			$this->config->helper('tokenPartsRenderer')
				->render_variable_name($this->get('name')),
			($this->get('default') ? '= '.$this->get('default') : '')
		))).';';
	}
}