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
		foreach ($attributes as $name => $value)
		{
			$this->object->set($name, $value);
		}
	}

	protected function _class_constructor_arguments()
	{
		return array(new \CodeGenerator\Format);
	}
}