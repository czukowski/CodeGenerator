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
	 * @param   string  $str     phrase to format
	 * @param   string  $option  camelcalse or underscore
	 * @return  string
	 */
	public function render_class_name($str, $option = NULL)
	{
		if ($option === NULL)
		{
			$option = $this->config->get_options('names.classname');
		}
		$namespace_parts = explode('\\', $str);
		foreach ($namespace_parts as &$part)
		{
			$part = ucfirst($this->render_name(ucwords($part), $option));
		}
		return implode('\\', $namespace_parts);
	}

	/**
	 * Renders a phrase in either camel case or underscore form, depending on
	 * the config options.
	 * 
	 * @param   string  $str     phrase to format
	 * @param   string  $option  camelcalse or underscore
	 * @return  string
	 */
	public function render_name($str, $option = NULL)
	{
		// If the string is not a phrase, return the original string
		if (strpos($str, ' ') === FALSE)
		{
			return $str;
		}
		if ($option === NULL)
		{
			$option = $this->config->get_options('names.default');
		}
		// Otherwise return formatted string appropriately to the config option
		switch ($option)
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
		// Break on invalid config option
		throw new \LogicException('Invalid format option: "'.$option.'"');
	}
}