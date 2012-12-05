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
	private $line_prefix = ' * ';

	protected function initialize()
	{
		parent::initialize();
		$this->initialize_attributes(array(
			'annotations' => array(),
			'text' => NULL,
		));
	}

	public function render()
	{
		parent::render();
		if ( ! $this->get('annotations') AND ! $this->get('text'))
		{
			return '';
		}
		$this->config->helper('columnsOptimizer')
			->auto_width($this->get('annotations'));
		$lines = array('/**');
		if ($this->get('text'))
		{
			foreach ($this->word_wrap($this->get('text')) as $text_line)
			{
				$lines[] = $text_line;
			}
			if ($this->get('annotations'))
			{
				$lines[] = $this->line_prefix;
			}
		}
		foreach ($this->get('annotations') as $annotation)
		{
			$lines[] = $this->line_prefix.$annotation;
		}
		$lines[] = ' */';
		return implode($this->config->get_format('line_end'), $lines);
	}

	/**
	 * Wraps the text if enabled and longer than the line length
	 */
	private function word_wrap($text)
	{
		if ($this->config->get_options('word_wrap'))
		{
			$pre_width = $this->config->helper('tokenMeasure')
				->get_indentation_length($this) + strlen($this->line_prefix);
			$line_width = $this->config->get_options('line_width');
			$newline_char = $this->config->get_format('line_end');
			$wrapped_text = $this->config->helper('text')
				->word_wrap($text, $line_width - $pre_width, $newline_char);
			$prefix = $this->line_prefix;
			return array_map(function($line) use ($prefix) {
				return $prefix.$line;
			}, explode($newline_char, $wrapped_text));
		}
		else
		{
			return array($this->line_prefix.$text);
		}
	}
}