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

class Block extends Token implements \ArrayAccess, \Iterator
{
	/**
	 * @var  integer  Current item index for Iterator access
	 */
	private $current_index = 0;

	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'indentation' => 0,
			'items' => array(),
			'glue' => NULL,
		));
		$this->initialize_validation(array(
			'indentation' => 'integer',
		));
	}

	public function render()
	{
		parent::render();
		if (($items = $this->get('items')))
		{
			$this->assert_items_array();
			return $this->render_block($this->get('items'));
		}
		return '';
	}

	public function add($attribute, $value)
	{
		if ($attribute === 'items')
		{
			$this->assert_items_array();
		}
		parent::add($attribute, $value);
	}

	private function assert_items_array()
	{
		$items = $this->get('items');
		if ( ! $this->config->helper('arrays')->is_array($items))
		{
			$this->set('items', array($items));
		}
	}

	/**
	 * Renders block of lines or items (other tokens) and indents them
	 * 
	 * @param  array  $items
	 */
	protected function render_block($items)
	{
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
		return $indentation.str_replace($line_end, $line_end.$indentation, $this->replace_parameters($item));
	}

	private function replace_parameters($item)
	{
		if ( ! is_string($item))
		{
			return $item;
		}
		return $this->config->helper('templateParser')
			->parse($this, $item);
	}

	public function current()
	{
		return $this[$this->current_index];
	}

	public function key()
	{
		return $this->current_index;
	}

	public function next()
	{
		$this->current_index++;
		return $this;
	}

	public function rewind()
	{
		$this->current_index = 0;
		return $this; 
	}

	public function valid()
	{
		return $this->offsetExists($this->current_index);
	}

	/**
	 * ArrayAccess isset implementation
	 */
	public function offsetExists($offset)
	{
		$items = $this->get_items();
		return isset($items[$offset]);
	}

	/**
	 * ArrayAccess getter implementation
	 */
	public function offsetGet($offset)
	{
		$items = $this->get_items();
		if ( ! isset($items[$offset]))
		{
			throw new \OutOfRangeException($this->get_type().'['.$offset.'] is not set');
		}
		return $items[$offset];
	}

	/**
	 * ArrayAccess setter implementation
	 */
	public function offsetSet($offset, $value)
	{
		$items = $this->get_items();
		$index = $offset !== NULL ? $offset : count($items);
		$items[$index] = $value;
		if ($value instanceof Token)
		{
			$value->set('parent', $this);
		}
		$this->set('items', $items);
	}

	/**
	 * ArrayAccess unset implementation
	 */
	public function offsetUnset($offset)
	{
		$items = $this->get_items();
		unset($items[$offset]);
		$this->set('items', $items);
	}

	private function get_items()
	{
		return $this->get('items');
	}
}