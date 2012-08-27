<?php
/**
 * BlockTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class BlockTest extends Testcase
{
	/**
	 * @dataProvider  provide_render
	 */
	public function test_render($attributes, $expected)
	{
		$this->setup_with_attributes($attributes);
		$actual = $this->object->render();
		$this->assertEquals($expected, $actual);
	}

	public function provide_render()
	{
		return array(
			array(
				array(
					'items' => array(),
					'indentation' => 2,
				),
				'',
			),
			array(
				array(
					'items' => array(
						'$a = 1;',
						'$b = $a;',
					),
					'indentation' => 2,
				),
				"\t\t\$a = 1;\n".
				"\t\t\$b = \$a;",
			),
			array(
				array(
					'items' => array(
						"\$a = 1;\n\$b = \$a;",
					),
					'indentation' => 1,
				),
				"\t\$a = 1;\n".
				"\t\$b = \$a;",
			),
			array(
				array(
					'items' => array(
						'$a = 1;',
						'$b = $a;',
					),
					'indentation' => 1,
					'glue' => "\n\n",
				),
				"\t\$a = 1;\n".
				"\n".
				"\t\$b = \$a;",
			),
			array(
				array(
					'items' => 'Weird argument',
				),
				NULL,
			),
		);
	}

	/**
	 * @dataProvider  provide_offset_exists
	 */
	public function test_offset_exists($items, $offset, $expected)
	{
		$this->setup_with_items($items);
		$this->assertSame($expected, isset($this->object[$offset]));
	}

	public function provide_offset_exists()
	{
		return array(
			array($this->get_items(), 0, TRUE),
			array($this->get_items(), 1, TRUE),
			array($this->get_items(), 2, TRUE),
			array($this->get_items(), 3, FALSE),
		);
	}

	/**
	 * @dataProvider  provide_offset_get
	 */
	public function test_offset_get($items, $offset, $expected)
	{
		$this->setup_with_items($items);
		$this->set_expected_exception_from_argument($expected);
		$this->assertSame($expected, $this->object[$offset]);
	}

	public function provide_offset_get()
	{
		return array(
			array($this->get_items(), 0, 'a'),
			array($this->get_items(), 1, 'b'),
			array($this->get_items(), 2, 123),
			array($this->get_items(), 3, new \OutOfRangeException),
		);
	}

	/**
	 * @dataProvider  provide_offset_set
	 */
	public function test_offset_set($items, $offset, $set_value, $expected)
	{
		$this->setup_with_items($items);
		$this->object[$offset] = $set_value;
		$this->set_expected_exception_from_argument($expected);
		$this->assertSame($set_value, $this->object[$offset]);
	}

	public function provide_offset_set()
	{
		return array(
			array($this->get_items(), 0, '123', '123'),
			array($this->get_items(), 1, FALSE, FALSE),
			array($this->get_items(), 2, NULL, new \OutOfRangeException),  // Setting NULL is the same as unset
			array($this->get_items(), 3, TRUE, TRUE),
			array($this->get_items(), 10, 3.14, 3.14),
		);
	}

	/**
	 * @dataProvider  provide_offset_unset
	 */
	public function test_offset_unset($items, $offset)
	{
		$this->setup_with_items($items);
		unset($this->object[$offset]);
		$this->assertFalse(isset($this->object[$offset]));
	}

	public function provide_offset_unset()
	{
		return array(
			array($this->get_items(), 0),
			array($this->get_items(), 1),
			array($this->get_items(), 2),
			array($this->get_items(), 3),
			array($this->get_items(), 10),
		);
	}

	private function get_items()
	{
		return array(
			'a', 'b', 123,
		);
	}

	private function setup_with_items($items)
	{
		$this->setup_with_attributes(array('items' => $items));
	}
}