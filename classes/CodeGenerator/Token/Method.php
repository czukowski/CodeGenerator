<?php
/**
 * Method class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Method extends Block
{
	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'comment' => NULL,
			'access' => NULL,
			'static' => NULL,
			'abstract' => NULL,
			'name' => NULL,
			'arguments' => array(),
			'body' => array(),
			'indentation' => 1,
		));
		$this->initialize_validation(array(
			'access' => 'access',
		));
	}

	public function render()
	{
		$lines = array(
			$this->render_heading(),
			$this->render_body(),
			$this->render_footing(),
		);
		$glue = ($this->get('body') AND ! $this->get('abstract'))
			? $this->config->get_format('line_end')
			: '';
		return implode($glue, $lines);
	}

	private function render_heading()
	{
		$line = $this->render_block_comment($this->get('comment'));
		$line .= implode(' ', array_filter(array(
			$this->render_boolean_attribute('abstract'),
			$this->get('access') ? : NULL,
			$this->render_boolean_attribute('static'),
			'function',
			$this->get('name'),
		)));
		$line .= '('.implode(', ', $this->get('arguments')).')';
		$line .= $this->get('abstract') === TRUE ? '' : $this->config->get_format('brace_open');
		return $line;
	}

	private function render_body()
	{
		return $this->get('abstract') === TRUE ? '' : $this->render_block($this->get('body'));
	}

	private function render_footing()
	{
		return $this->get('abstract') === TRUE ? ';' : $this->config->get_format('brace_close');
	}

	public function validate_access($value)
	{
		return $value === NULL OR in_array($value, array('public', 'private', 'protected'), TRUE);
	}
}