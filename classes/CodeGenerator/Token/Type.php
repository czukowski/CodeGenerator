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
			return $this->config->helper('tokenFactory')
				->transform('DocComment', $comment, $this);
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
		return ($namespace = $this->get('namespace')) ? 'namespace '.$namespace.';' : '';
	}

	private function render_use_namespace()
	{
		if (($use = $this->get('use')))
		{
			$glue = ','.$this->config->get_format('line_end').$this->config->get_format('indent');
			return 'use '.implode($glue, $use).';';
		}
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

	public function validate_type($value)
	{
		return in_array($value, array('class', 'interface'), TRUE);
	}
}