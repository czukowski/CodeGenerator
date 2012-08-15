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
	 * Renders block of lines or items (other tokens) and indents them
	 * 
	 * @param  array  $items
	 */
	protected function render_block($items, $glue = NULL)
	{
		if ( ! $this->config->helper('arrays')->is_array($items))
		{
			return;
		}
		foreach ($items as &$item)
		{
			$item = $this->render_item($item);
		}
		if ($glue === NULL)
		{
			$glue = $this->config->get_format('line_end');
		}
		return implode($glue, $items);
	}

	/**
	 * Renders a single line or token
	 */
	private function render_item($item)
	{
		$line_end = $this->config->get_format('line_end');
		$indentation = str_repeat($this->config->get_format('indent'), $this->get('indentation'));
		return $indentation.str_replace($line_end, $line_end.$indentation, $item);
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
			return (string) $comment;
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