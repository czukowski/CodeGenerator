<?php
/**
 * Token builder class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;
use CodeGenerator\Builder\TokenMeta;

class TokenBuilder extends \CodeGenerator\Singleton
{
	/**
	 * Recursively creates and returns Token instance from the meta object instances.
	 * 
	 * @param   \CodeGenerator\Builder\TokenMeta  $from
	 * @return  \CodeGenerator\Token\Token
	 */
	public function build(TokenMeta $from)
	{
		return $this->config->helper('tokenFactory')
			->create($from->get_name(), $this->process_attributes($from));
	}

	/**
	 * Builds Tokens from meta objects found in attributes.
	 * 
	 * @param   \CodeGenerator\Builder\TokenMeta  $from
	 * @return  array
	 */
	private function process_attributes(TokenMeta $from)
	{
		$attributes = array();
		foreach ($from->get_attributes() as $name => $attribute)
		{
			$attributes[$name] = $this->process_attribute($attribute);
		}
		return $attributes;
	}

	/**
	 * @param   mixed  $attribute
	 * @return  mixed
	 */
	private function process_attribute($attribute)
	{
		if ( ! is_array($attribute))
		{
			return $attribute instanceof TokenMeta
				? $this->build($attribute)
				: $attribute;
		}
		foreach ($attribute as $i => $item)
		{
			$attribute[$i] = $this->process_attribute($item);
		}
		return $attribute;
	}
}