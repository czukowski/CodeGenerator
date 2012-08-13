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
	protected $attributes = array(
		'access' => NULL,
		'static' => NULL,
		'abstract' => NULL,
		'name' => NULL,
		'arguments' => array(),
		'body' => array(),
	);
	protected $indent = 1;
	protected $validations = array(
		'access' => 'access',
	);

	public function render()
	{
		$lines = array(
			$this->render_method_heading(),
			$this->render_body(),
			$this->render_method_footing(),
		);
		$glue = ($this->attributes['body'] AND ! $this->attributes['abstract'])
			? $this->config->get_format('line_end')
			: '';
		return implode($glue, $lines);
	}

	private function render_method_heading()
	{
		$line = implode(' ', array_filter(array(
			$this->render_boolean_attribute('abstract'),
			$this->attributes['access'] ? : NULL,
			$this->render_boolean_attribute('static'),
			'function '.$this->attributes['name'],
		)));
		$line .= '('.implode(', ', $this->attributes['arguments']).')';
		$line .= $this->attributes['abstract'] === TRUE ? '' : $this->config->get_format('brace_open');
		return $line;
	}

	private function render_body()
	{
		return $this->attributes['abstract'] === TRUE ? '' : $this->render_block($this->attributes['body']);
	}

	private function render_method_footing()
	{
		return $this->attributes['abstract'] === TRUE ? ';' : $this->config->get_format('brace_close');
	}

	private function render_boolean_attribute($attribute)
	{
		if ($this->attributes[$attribute] === TRUE)
		{
			return $attribute;
		}
	}

	public function validate_access($value)
	{
		return $value === NULL OR in_array($value, array('public', 'private', 'protected'), TRUE);
	}
}