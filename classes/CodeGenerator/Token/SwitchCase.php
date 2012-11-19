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
	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'body' => NULL,
			'match' => NULL,
			'default' => FALSE,
			'break' => FALSE,
		));
		$this->initialize_validation(array(
			'default' => 'boolean',
			'break' => 'boolean',
		));
	}

	public function render()
	{
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
		if (($body = $this->get('body')))
		{
			$body_token = $this->config->helper('tokenFactory')
				->transform('Block', $body, $this);
			if ($this->get('break'))
			{
				$body_token->add('items', 'break;');
			}
			return (string) $body_token;
		}
	}
}