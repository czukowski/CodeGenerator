<?php
/**
 * Helpers base test class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;
use CodeGenerator\Config;

class Testcase extends \CodeGenerator\Framework\Testcase
{
	/**
	 * Setup object automatically
	 */
	protected function setUp()
	{
		$this->setup_object();
	}

	/**
	 * Use default Config as constructor arguments
	 */
	protected function _class_constructor_arguments()
	{
		return array(new Config);
	}
}