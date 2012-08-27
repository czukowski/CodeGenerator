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
		$this->initialize_validation(array(
			'type' => 'type',
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
		$heading = implode($this->get_methods_glue(), array_filter(array(
			$this->render_comment(),
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
			return $this->config->helper('tokenFactory')
				->transform('DocComment', $comment)
				->set('parent', $this);
		}
	}

	private function render_namespace()
	{
		return implode($this->config->get_format('line_end'), array_filter(array(
			($namespace = $this->get('namespace')) ? 'namespace '.$namespace : '',
			($use = $this->get('use')) ? 'use '.implode($this->config->get_format('line_end'), $use) : '',
		)));
	}

	private function render_declaration()
	{
		return implode(' ', array_filter(array(
			$this->render_boolean_attribute('abstract'),
			$this->render_boolean_attribute('final'),
			$this->get('type'),
			$this->get('name'),
			$this->render_extends(),
			$this->render_implements(),
		)));
	}

	private function render_extends()
	{
		if (($classname = $this->get('extends')))
		{
			return 'extends '.$classname;
		}
	}

	private function render_implements()
	{
		if (($interfaces = $this->get('implements')))
		{
			return 'implements '.implode(', ', $interfaces);
		}
	}

	private function render_body()
	{
		return implode($this->get_methods_glue(), array_filter(array(
			(string) $this->config->helper('tokenFactory')
				->transform('Block', $this->get('properties'))
				->set('parent', $this),
			(string) $this->config->helper('tokenFactory')
				->transform('Block', $this->get('methods'))
				->set('glue', $this->get_methods_glue())
				->set('parent', $this),
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

	public function validate_type($value)
	{
		return in_array($value, array('class', 'interface'), TRUE);
	}
}