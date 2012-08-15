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

abstract class Block extends Token
{
	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'indentation' => 0,
		));
		$this->initialize_validation(array(
			'indentation' => 'integer',
		));
	}

	/**
	 * Renders block of lines and indents them
	 * 
	 * @param  array  $lines
	 */
	protected function render_block($lines)
	{
		if ( ! $this->config->helper('arrays')->is_array($lines))
		{
			return;
		}
		foreach ($lines as &$line)
		{
			$line = $this->render_line($line);
		}
		return implode($this->config->get_format('line_end'), $lines);
	}

	/**
	 * Renders a single line or token
	 */
	private function render_line($line)
	{
		$line_end = $this->config->get_format('line_end');
		$indentation = str_repeat($this->config->get_format('indent'), $this->get('indentation'));
		return $indentation.str_replace($line_end, $line_end.$indentation, $line);
	}

	/**
	 * Render block comment, optionally generate it
	 */
	protected function render_block_comment($comment)
	{
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

	/**
	 * If attribute is set, returns its name, else NULL
	 */
	protected function render_boolean_attribute($attribute)
	{
		if ($this->get($attribute) === TRUE)
		{
			return $attribute;
		}
	}
}