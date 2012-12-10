<?php
/**
 * Property token class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class SwitchCase extends Token
{
	protected $transform = array(
		'body' => 'Block',
	);

	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'body' => array(),
			'match' => NULL,
			'default' => FALSE,
			'break' => FALSE,
		));
		$this->initialize_validation(array(
			'default' => 'boolean',
			'break' => 'boolean',
		));
	}

	protected function before_render()
	{
		$body = $this->get('body');
		$body->set('indentation', 1);
		if ($this->get('break') AND $body[count($body) - 1] !== 'break;')
		{
			$body->add('items', 'break;');
		}
	}

	public function render()
	{
		parent::render();
		if ( ! $this->get('match') AND ! $this->get('default'))
		{
			return '';
		}
		$lines = array(
			$this->render_condition(),
			$this->render_body(),
		);
		return implode($this->config->get_format('line_end'), array_filter($lines));
	}

	private function render_condition()
	{
		if ($this->get('default'))
		{
			return 'default:';
		}
		$lines = array_map(function($match) {
			return 'case '.$match.':';
		}, (array) $this->get('match'));
		return implode($this->config->get_format('line_end'), $lines);
	}

	private function render_body()
	{
		if (($body = $this->get('body')) AND count($body))
		{
			return (string) $body;
		}
	}
}