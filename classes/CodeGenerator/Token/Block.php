<?php
/**
 * Block token argument class. Serves as a container for multiple items and intended for use as a class
 * or method body.
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Block extends Token
{
	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'indentation' => 1,
			'items' => array(),
			'glue' => NULL,
		));
		$this->initialize_validation(array(
			'indentation' => 'integer',
		));
	}

	public function render()
	{
		return $this->render_block($this->get('items'));
	}

	/**
	 * Renders block of lines or items (other tokens) and indents them
	 * 
	 * @param  array  $items
	 */
	protected function render_block($items)
	{
		if ( ! $this->config->helper('arrays')->is_array($items))
		{
			return;
		}
		foreach ($items as &$item)
		{
			$item = $this->render_item($item);
		}
		if (($glue = $this->get('glue')) === NULL)
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
}