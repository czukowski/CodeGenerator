<?php
/**
 * Base class test
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class ObjectTest extends \CodeGenerator\Framework\Testcase
{
	public function test_constructor()
	{
		$this->setup_object();
		$actual = $this->_object_property($this->object, 'config')
			->getValue($this->object);
		$this->assertInstanceOf('\CodeGenerator\Config', $actual);
	}

	protected function _class_constructor_arguments()
	{
		return array(new Config);
	}
}