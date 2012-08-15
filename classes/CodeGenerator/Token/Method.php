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
			$this->render_method_heading(),
			$this->render_body(),
			$this->render_method_footing(),
		);
		$glue = ($this->get('body') AND ! $this->get('abstract'))
			? $this->config->get_format('line_end')
			: '';
		return implode($glue, $lines);
	}

	private function render_method_heading()
	{
		$line = $this->render_comment();
		$line .= implode(' ', array_filter(array(
			$this->render_boolean_attribute('abstract'),
			$this->get('access') ? : NULL,
			$this->render_boolean_attribute('static'),
			'function '.$this->get('name'),
		)));
		$line .= '('.implode(', ', $this->get('arguments')).')';
		$line .= $this->get('abstract') === TRUE ? '' : $this->config->get_format('brace_open');
		return $line;
	}

	private function render_comment()
	{
		$comment = $this->get('comment');
		if ($comment AND is_string($comment))
		{
			$comment = $this->config->helper('tokenFactory')
				->create('DocComment', array(
					'text' => $comment,
				));
		}
		if ($comment AND $comment instanceof DocComment)
		{
			return $comment.$this->config->get_format('line_end');
		}
		return NULL;
	}

	private function render_body()
	{
		return $this->get('abstract') === TRUE ? '' : $this->render_block($this->get('body'));
	}

	private function render_method_footing()
	{
		return $this->get('abstract') === TRUE ? ';' : $this->config->get_format('brace_close');
	}

	private function render_boolean_attribute($attribute)
	{
		if ($this->get($attribute) === TRUE)
		{
			return $attribute;
		}
	}

	public function validate_access($value)
	{
		return $value === NULL OR in_array($value, array('public', 'private', 'protected'), TRUE);
	}
}