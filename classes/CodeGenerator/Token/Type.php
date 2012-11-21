<?php
/**
 * Type class that represents Class tokens
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Type extends Token
{
	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'comment' => NULL,
			'type' => 'class',
			'abstract' => NULL,
			'final' => NULL,
			'namespace' => NULL,
			'use' => array(),
			'name' => NULL,
			'extends' => NULL,
			'implements' => array(),
			'traits' => array(),
			'properties' => array(),
			'methods' => array(),
		));
		$this->initialize_transformations(array(
			'comment' => 'DocComment',
		));
		$this->initialize_validation(array(
			'type' => 'type',
			'use' => 'use',
		));
	}

	public function render()
	{
		if ( ! $this->get('name') OR ! $this->get('type'))
		{
			return '';
		}
		$body_render = $this->render_body();
		$lines = array(
			$this->render_heading(),
			$body_render,
			$this->render_footing(),
		);
		$glue = $body_render ? $this->config->get_format('line_end') : '';
		return implode($glue, $lines);
	}

	private function render_heading()
	{
		$heading = $this->render_comment();
		$heading .= $heading ? $this->config->get_format('line_end') : '';
		$heading .= implode($this->get_methods_glue(), array_filter(array(
			$this->render_namespace(),
			$this->render_declaration(),
		)));
		$heading .= $this->config->get_format('brace_open');
		return $heading;
	}

	private function render_comment()
	{
		if (($comment = $this->get('comment')))
		{
			return $comment;
		}
	}

	private function render_namespace()
	{
		return implode($this->config->get_format('line_end'), array_filter(array(
			$this->render_class_namespace(),
			$this->render_use_namespace(),
		)));
	}

	private function render_class_namespace()
	{
		if (($namespace = $this->get('namespace')))
		{
			return 'namespace '.call_user_func($this->get_name_renderer(), $namespace).';';
		}
	}

	private function render_use_namespace()
	{
		if (($use = $this->get('use')))
		{
			$glue = ','.$this->config->get_format('line_end').$this->config->get_format('indent');
			return 'use '.implode($glue, array_map(array($this, 'render_use_namespace_item'), $use)).';';
		}
	}

	protected function render_use_namespace_item($item)
	{
		$renderer = $this->get_name_renderer();
		$item = (array) $item;
		$line = call_user_func($renderer, $item[0]);
		if (isset($item[1]))
		{
			$line .= ' as '.$item[1];
		}
		return $line;
	}

	private function render_declaration()
	{
		return implode(' ', array_filter(array(
			$this->render_boolean_attribute('abstract'),
			$this->render_boolean_attribute('final'),
			$this->get('type'),
			$this->config->helper('tokenPartsRenderer')
				->render_class_name($this->get('name')),
			$this->render_extends(),
			$this->render_implements(),
		)));
	}

	private function render_extends()
	{
		if (($classname = $this->get('extends')))
		{
			return 'extends '.$this->config->helper('tokenPartsRenderer')
				->render_class_name($classname);
		}
	}

	private function render_implements()
	{
		if (($interfaces = $this->get('implements')))
		{
			return 'implements '.implode(', ', array_map($this->get_name_renderer(), $interfaces));
		}
	}

	private function render_body()
	{
		return implode($this->get_methods_glue(), array_filter(array(
			(string) $this->config->helper('tokenFactory')
				->transform('Block', $this->get('properties'), $this),
			(string) $this->config->helper('tokenFactory')
				->transform('Block', $this->get('methods'), $this)
				->set('glue', $this->get_methods_glue()),
		)));
	}

	private function render_footing()
	{
		return $this->config->get_format('brace_close');
	}

	private function get_methods_glue()
	{
		return str_repeat($this->config->get_format('line_end'), 2);
	}

	private function get_name_renderer()
	{
		return array($this->config->helper('tokenPartsRenderer'), 'render_class_name');
	}

	public function validate_type($value)
	{
		return in_array($value, array('class', 'interface'), TRUE);
	}

	public function validate_use($values)
	{
		if ( ! is_array($values) OR empty($values))
		{
			return FALSE;
		}
		$validator = $this->config->helper('validator');
		foreach ($values as $value)
		{
			$passed = ((is_string($value) AND $validator->validate_constraint($value))
				OR (is_array($value) AND count($value) === 2 AND $validator->validate_constraint($value[0]) AND $validator->validate_name($value[1])));
			if ( ! $passed)
			{
				return FALSE;
			}
		}
		return TRUE;
	}
}