<?php
/**
 * TokenTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Testcase extends \CodeGenerator\Framework\Testcase
{
	protected function setup_with_attributes($attributes, $options = array())
	{
		$this->setup_object($options);
		$this->get_attributes()
			->setValue($this->object, $attributes);
	}

	protected function get_attributes()
	{
		$attributes = new \ReflectionProperty($this->object, 'attributes');
		$attributes->setAccessible(TRUE);
		return $attributes;
	}

	protected function _class_constructor_arguments()
	{
		return array(new \CodeGenerator\Format);
	}
}