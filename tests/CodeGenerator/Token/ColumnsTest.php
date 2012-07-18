<?php
/**
 * AnnotationTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class ColumnsTest extends Testcase
{
	/**
	 * @dataProvider  provide_alignments
	 */
	public function test_alignments($alignments, $arguments, $expected_return, $expected_alignments)
	{
		$this->setup_column_object(array(), $alignments);
		$this->setExpectedExceptionFromArgument($expected_return);
		$this->assert_object_values('alignments', $arguments, $expected_return, $expected_alignments);
	}

	public function provide_alignments()
	{
		// [setup_alignments, method_arguments, expected_return, expected_alignments]
		return array(
			// Returns empty alignments list
			array(
				array(), array(), array(), array(),
			),
			// Returns alignments list
			array(
				array('left', 'right'), array(), array('left', 'right'), array('left', 'right'),
			),
			// Gets first column alignment (not set)
			array(
				array(), array(0), new \OutOfRangeException, array(),
			),
			// Gets first column alignment
			array(
				array('left', 'right'), array(0), 'left', array('left', 'right'),
			),
			// Sets first column alignment
			array(
				array(), array(0, 'center'), NULL, array('center'),
			),
			// Sets first column alignment
			array(
				array('left', 'right'), array(0, 'center'), NULL, array('center', 'right'),
			),
			// Sets column alignments
			array(
				array(), array(array('center')), NULL, array('center'),
			),
			// Sets column alignments
			array(
				array('left', 'right'), array(array('center')), NULL, array('center'),
			),
			// Invalid usage
			array(
				array(), array(array(1), 'center'), new \InvalidArgumentException, array(),
			),
			// Invalid usage
			array(
				array(), array(array('bar')), new \InvalidArgumentException, array(),
			),
			// Invalid usage
			array(
				array('left', 'right'), array(1, array('center')), new \InvalidArgumentException, array(),
			),
		);
	}

	/**
	 * @dataProvider  provide_widths
	 */
	public function test_widths($widths, $arguments, $expected_return, $expected_widths)
	{
		$this->setup_column_object($widths);
		$this->setExpectedExceptionFromArgument($expected_return);
		$this->assert_object_values('widths', $arguments, $expected_return, $expected_widths);
	}

	public function provide_widths()
	{
		// [setup_widths, method_arguments, expected_return, expected_widths]
		return array(
			// Returns empty widths list
			array(
				array(), array(), array(), array(),
			),
			// Returns widths list
			array(
				array(1, 2), array(), array(1, 2), array(1, 2),
			),
			// Gets first column width (not set)
			array(
				array(), array(0), new \OutOfRangeException, array(),
			),
			// Gets first column width
			array(
				array(1, 2), array(0), 1, array(1, 2),
			),
			// Sets first column width
			array(
				array(), array(0, 2), NULL, array(2),
			),
			// Sets first column width
			array(
				array(1, 2), array(0, 2), NULL, array(2, 2),
			),
			// Sets column widths
			array(
				array(), array(array(1)), NULL, array(1),
			),
			// Sets column widths
			array(
				array(1, 2), array(array(1)), NULL, array(1),
			),
			// Invalid usage
			array(
				array(), array(array(1), 1), new \InvalidArgumentException, array(),
			),
			// Invalid usage
			array(
				array(), array(array(1, 'foo')), new \InvalidArgumentException, array(),
			),
			// Invalid usage
			array(
				array(1, 2), array(1, array(1)), new \InvalidArgumentException, array(),
			),
		);
	}

	protected function assert_object_values($method, $arguments, $expected_return, $expected_values)
	{
		$actual = $this->_object_method($this->object, $method)
			->invokeArgs($this->object, $arguments);
		if ($expected_return === NULL)
		{
			$this->assertSame($this->object, $actual);
		}
		else
		{
			$this->assertEquals($expected_return, $actual);
		}
		$this->assertEquals($expected_values, $this->object->$method());
	}

	/**
	 * @dataProvider  provide_render_columns
	 */
	public function test_render_columns($widths, $alignments, $columns, $expected)
	{
		$this->setup_column_object($widths, $alignments);
		$this->config->format('column_delimiter', '-');
		$actual = $this->_object_method($this->object, 'render_columns')
			->invokeArgs($this->object, array($columns));
		$this->assertEquals($expected, $actual);
	}

	public function provide_render_columns()
	{
		// [widths, alignments, columns, expected]
		return array(
			array(
				array(), array(), array(), '',
			),
			array(
				array(), array(), array('@var', 'array'), '@var--array',	
			),
			array(
				array(1, 2), array(), array('@var', 'array'), '@var--array',	
			),
			array(
				array(5, 2), array(), array('@var', 'array'), '@var---array',	
			),
			array(
				array(10, 10), array(), array('@var', 'array'), '@var--------array',	
			),
			array(
				array(10, 10), array(NULL, 'right'), array('@var', 'array'), '@var-------------array',	
			),
			array(
				array(10, 10), array(NULL, STR_PAD_LEFT), array('@var', 'array'), '@var-------------array',	
			),
			array(
				array(10, 10), array('right', 'right'), array('@var', 'array'), '------@var-------array',	
			),
			array(
				array(10, 10), array('right', 'left'), array('@var', 'array'), '------@var--array',	
			),
			array(
				array(10, 10), array('right', 'center'), array('@var', 'array'), '------@var----array---',	
			),
			array(
				array(10, 10), array(STR_PAD_LEFT, STR_PAD_BOTH), array('@var', 'array'), '------@var----array---',	
			),
		);
	}

	protected function setup_column_object($widths = array(), $alignments = array())
	{
		$this->setup_mock();
		$this->_object_property($this->object, 'widths')
			->setValue($this->object, $widths);	
		$this->_object_property($this->object, 'alignments')
			->setValue($this->object, $alignments);	
	}
}