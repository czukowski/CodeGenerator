<?php
/**
 * Language construct token (if, foreach, etc)
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Construct extends Token
{
	protected $transform = array(
		'body' => 'Block',
	);

	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'type' => NULL,
			'condition' => NULL,
			'body' => array(),
		));
		$this->initialize_validation(array(
			'type' => 'type',
		));
	}

	public function render()
	{
		if ( ! $this->get('type'))
		{
			return '';
		}
		$body_render = $this->get('body') ? (string) $this->get('body') : '';
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
		$line = $this->get('type');
		if ($this->is_condition_in_heading($line))
		{
			$line .= $this->render_condition();
		}
		$line .= $this->config->get_format('brace_open');
		return $line;
	}

	private function is_condition_in_heading($type)
	{
		return ! in_array($type, array('else', 'do'));
	}

	private function render_footing()
	{
		$line = $this->config->get_format('brace_close');
		if ($this->get('type') === 'do')
		{
			$line .= $this->config->get_format('line_end');
			$line .= 'while'.$this->render_condition().';';
		}
		return $line;
	}

	private function render_condition()
	{
		return ' ('.$this->get('condition').')';
	}

	public function validate_type($value)
	{
		return in_array($value, array('if', 'else', 'elseif', 'for', 'foreach', 'switch', 'while', 'do'), TRUE);
	}
}