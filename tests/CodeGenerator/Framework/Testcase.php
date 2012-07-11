<?php
/**
 * Base testcase class for PHPUnit
 * 
 * @package    CodeGenerator
 * @category   Framework
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Framework;

abstract class Testcase extends \PHPUnit_Framework_TestCase {

	protected $object;

	protected function setup_object($arguments)
	{
        $class = new \ReflectionClass(preg_replace('#Test$#', '', get_class($this)));
		$arguments = func_num_args() > 0 ? $arguments : $this->_object_constructor_arguments();
        $this->object = $class->newInstanceArgs($arguments);
	}

	protected function _object_constructor_arguments()
	{
		// No arguments by default
		return array();
	}
}