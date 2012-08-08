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

class ObjectTest extends \CodeGenerator\Helper\Testcase
{
	public function test_constructor()
	{
		$actual = $this->get_object_property($this->object, 'config')
			->getValue($this->object);
		$this->assertInstanceOf('\CodeGenerator\Config', $actual);
	}
}