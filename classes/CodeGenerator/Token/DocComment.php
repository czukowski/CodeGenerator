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
	protected $attributes = array(
		'annotations' => array(),
		'text' => NULL,
	);

	public function render()
	{
		if ( ! $this->attributes['annotations'] AND ! $this->attributes['text'])
		{
			return '';
		}
		$this->config->helper('columnsOptimizer')
			->align($this->attributes['annotations']);
		$lines = array('/**');
		if ($this->attributes['text'])
		{
			$lines[] = ' * '.$this->attributes['text'];
		}
		foreach ($this->attributes['annotations'] as $annotation)
		{
			$lines[] = ' * '.$annotation;
		}
		$lines[] = ' */';
		return implode("\n", $lines);
	}

}