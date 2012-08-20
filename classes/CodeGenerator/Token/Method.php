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
			'body' => NULL,
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
				->transform('DocComment', $comment);
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
		elseif (($body = $this->get('body')))
		{
			return $this->config->helper('tokenFactory')
				->transform('Block', $body);
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