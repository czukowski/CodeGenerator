<?php
/**
 * TokenBuilderTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenBuilderTest extends \CodeGenerator\Builder\Testcase
{
	/**
	 * @dataProvider  provide_build
	 */
	public function test_build($object, $expected)
	{
		$this->setup_object(array('arguments' => array($this->get_config())));
		$this->set_expected_exception_from_argument($expected);
		$token = $this->object->build($object);
		$this->assert_token_tree($token, $expected);
	}

	public function provide_build()
	{
		return array(
			array(
				$this->create_meta_object('One', array('one', 'two')),
				new \InvalidArgumentException,
			),
			array(
				$this->create_meta_object('Class', array(
					'properties' => array(
						$this->create_meta_object('Property', array(
							'access' => 'private',
							'name' => 'values',
						)),
					),
				)),
				array(
					'.' => 'Type',
					'properties[0]' => 'Property',
				),
			),
		);
	}

	private function create_meta_object($name, $attributes)
	{
		$object = $this->get_mock(array(
			'classname' => 'CodeGenerator\Builder\TokenMeta',
			'methods' => array('none'),
			'arguments' => array(),
		));
		$this->get_object_method($object, 'set_name')
			->invoke($object, $name);
		$this->get_object_method($object, 'set_attributes')
			->invoke($object, $attributes);
		return $object;
	}
}