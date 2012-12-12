<?php
/**
 * ArraySourceTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Builder;

class ArraySourceTest extends Testcase
{
	/**
	 * @dataProvider  provide_construct
	 */
	public function test_construct($arguments, $expected)
	{
		$this->set_expected_exception_from_argument($expected);
		$this->setup_object(array('arguments' => array($arguments)));
		list ($name, $attribute_keys, $object_keys) = $expected;
		$actual_attributes = $this->object->get_attributes();
		$this->assertEquals($name, $this->object->get_name());
		$this->assertEquals($attribute_keys, array_keys($actual_attributes));
		foreach ($object_keys as $key)
		{
			$this->assertInstanceOf('CodeGenerator\Builder\ArraySource', $actual_attributes[$key]);
		}
	}

	public function provide_construct()
	{
		return array(
			// Invalid sources
			array('not-array', new \InvalidArgumentException),
			array(array(), new \InvalidArgumentException),
			array(array('one-item'), new \InvalidArgumentException),
			array(array('item1', 'non-array-item1'), new \InvalidArgumentException),
			array(array('non-preg-matching', array()), new \InvalidArgumentException),
			// Valid sources
			array(
				array('TestItem', array()),
				array('TestItem', array(), array()),
			),
			array(
				array('TestItem', array(
					'item1' => 'value1',
					'item2' => array(),
					'item3' => array('subitem1', 'subitem2'),
				)),
				array('TestItem', array('item1', 'item2', 'item3'), array()),
			),
			array(
				array('TestItem', array(
					'item1' => array('Subitem', 'subitem2'),
					'item2' => array('Subitem'),
					'item3' => array('Subitem', array()),
				)),
				array('TestItem', array('item1', 'item2', 'item3'), array('item3')),
			),
		);
	}

	/**
	 * @dataProvider  provide_build
	 */
	public function test_build($source, $expected)
	{
		$this->setup_object(array('arguments' => array($source)));
		$this->assert_token_tree($this->build_token(), $expected);
	}

	public function provide_build()
	{
		return array(
			array(
				array('Class', array(
					'properties' => array(
						'private $item;',
						array('Property', array(
							'access' => 'private',
							'name' => 'values',
						)),
					),
				)),
				array(
					'.' => 'Type',
					'properties[1]' => 'Property',
				),
			),
		);
	}

}