<?php
/**
 * DocComment class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class DocComment extends Token
{
	protected function initialize()
	{
		$this->initialize_attributes(array(
			'annotations' => array(),
			'text' => NULL,
		));
	}

	public function render()
	{
		if ( ! $this->get('annotations') AND ! $this->get('text'))
		{
			return '';
		}
		$this->config->helper('columnsOptimizer')
			->auto_width($this->get('annotations'));
		$lines = array('/**');
		if ($this->get('text'))
		{
			$lines[] = ' * '.$this->get('text');
		}
		foreach ($this->get('annotations') as $annotation)
		{
			$lines[] = ' * '.$annotation;
		}
		$lines[] = ' */';
		return implode($this->config->get_format('line_end'), $lines);
	}
}