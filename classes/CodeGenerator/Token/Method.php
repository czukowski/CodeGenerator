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

class Method extends Token
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
		));
		$this->initialize_validation(array(
			'access' => 'access',
			'name' => 'name',
		));
	}

	public function render()
	{
		$body_render = $this->render_body();
		$lines = array(
			$this->render_heading(),
			$body_render,
			$this->render_footing(),
		);
		$glue = ($body_render AND ! $this->get('abstract'))
			? $this->config->get_format('line_end')
			: '';
		return implode($glue, $lines);
	}

	private function render_heading()
	{
		return implode($this->config->get_format('line_end'), array_filter(array(
			$this->render_comment(),
			$this->render_declaration(),
		)));
	}

	private function render_comment()
	{
		if (($comment = $this->get('comment')))
		{
			return $this->config->helper('tokenFactory')
				->transform('DocComment', $comment, $this);
		}
	}

	private function render_declaration()
	{
		$line = implode(' ', array_filter(array(
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
		if ($this->get('abstract') === TRUE)
		{
			return '';
		}
		if (($body = $this->get('body')))
		{
			return (string) $this->config->helper('tokenFactory')
				->transform('Block', $body, $this);
		}
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