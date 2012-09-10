<?php
/**
 * Token parts renderer helper class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenPartsRenderer extends \CodeGenerator\Singleton
{
	/**
	 * Renders a class name in either camel case or underscore form, depending on
	 * the config options. Capital case is used for each word.
	 * 
	 * @param   string  $str  phrase to format
	 * @return  string
	 */
	public function render_class_name($str)
	{
		$namespace_parts = explode('\\', $str);
		foreach ($namespace_parts as &$part)
		{
			$part = ucfirst($this->render_name(ucwords($part)));
		}
		return implode('\\', $namespace_parts);
	}

	/**
	 * Renders a phrase in either camel case or underscore form, depending on
	 * the config options.
	 * 
	 * @param   string  $str  phrase to format
	 * @return  string
	 */
	public function render_name($str)
	{
		switch ($this->config->get_options('names'))
		{
			case 'camelcase':
				return $this->config->helper('inflector')
					->camelize($str);
				break;
			case 'underscore':
				return $this->config->helper('inflector')
					->underscore($str);
				break;
		}
		throw new \LogicException('Invalid config option `names`');
	}
}