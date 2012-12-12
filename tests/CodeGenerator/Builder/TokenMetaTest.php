<?php
/**
 * TokenMetaTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Builder;

class TokenMetaTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @dataProvider  provide_class
	 */
	public function test_class($name, $attributes)
	{
		$this->setup_object(array('arguments' => array($name, $attributes)));
		$this->assertSame($name, $this->object->get_name());
		$this->assertSame($attributes, $this->object->get_attributes());
	}

	public function provide_class()
	{
		return array(
			// Generic meta info
			array('One', array('one', 'two')),
			// Meta info with Token instance
			array('Class', array(
				'methods' => array(
					$this->get_config()->helper('tokenFactory')->create('Method', array(
						'access' => 'public',
						'name' => '__construct',
						'body' => '$this->values = array();'
					)),
				),
			)),
			// Meta info with Token instance
			array('Class', array(
				'properties' => array(
					new TokenMeta('Property', array(
						'access' => 'private',
						'name' => 'values',
					)),
				),
			)),
		);
	}

}