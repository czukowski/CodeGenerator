<?php
/**
 * TokenMeasureTest
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class TokenMeasureTest extends Testcase
{
	/**
	 * @var  array
	 */
	private $tokens;

	/**
	 * @dataProvider  provide_get_indentation
	 */
	public function test_get_indentation($token, $expected)
	{
		$this->setup_object();
		$actual = $this->object->get_indentation($token);
		$this->assertEquals($expected, $actual);
	}

	public function provide_get_indentation()
	{
		$factory = $this->get_config()
			->helper('tokenFactory');
		$method_body = $this->create_token('Block');
		$method_comment = $this->create_token('DocComment', array('text' => 'Some text'));
		$method = $this->create_token('Method', array('body' => $method_body));
		$method->set('comment', $method_comment);
		$class = $this->create_token('Class', array(
			'methods' => $this->create_token('Block'),
		));
		$class->add('methods', $method);
		return array(
			array($method_body, 2),
			array($method_comment, 1),
			array($method, 1),
			array($class, 0),
		);
	}

	private function create_token($type, $arguments = array())
	{
		$token = $this->get_config()
			->helper('tokenFactory')
			->create($type, $arguments);
		$this->get_object_method($token, 'before_render')
			->invoke($token);
		return $token;
	}

	/**
	 * @dataProvider  provide_get_indentation_length
	 */
	public function test_get_indentation_length($token, $indent_char, $indent_length, $expected)
	{
		$this->setup_object(array(
			'arguments' => array($this->create_config($indent_char, $indent_length)),
		));
		$actual = $this->object->get_indentation_length($token);
		$this->assertEquals($expected, $actual);
	}

	public function provide_get_indentation_length()
	{
		$widths = array(
			array('    ', 4),
			array('  ', 2),
			array("\t", 4),
			array('-', 1),
		);
		$count = count($widths);
		$provide = array();
		foreach ($this->provide_get_indentation() as $i => $indentation_provider)
		{
			list($char, $width) = $widths[$i % $count];
			list($token, $indentation) = $indentation_provider;
			$provide[] = array($token, $char, $width, $indentation * $width);
		}
		return $provide;
	}

	private function create_config($indent_char, $indent_length)
	{
		$key = $this->get_mock(array('methods' => array('none')))
			->encode_string($indent_char);
		return new \CodeGenerator\Config(array(
			'format' => array(
				'indent' => $indent_char,
			),
			'options' => array(
				'char_length' => array(
					$key => $indent_length,
				),
			),
		));
	}

	/**
	 * @dataProvider  provide_find_attribute
	 */
	public function test_find_attribute($token, $find, $expected)
	{
		$this->setup_config();
		$this->setup_token_structure();
		$actual = $this->object->find_attribute($this->tokens[$token], $find);
		$this->assert_found_attribute($expected, $find, $actual);
	}

	public function provide_find_attribute()
	{
		// [source_token, find_attribute, found_in_token]
		return array_merge(
			$this->provide_find_attribute_in_parents(),
			$this->provide_find_attribute_in_children()
		);
	}

	/**
	 * @dataProvider  provide_find_attribute_in_parents
	 */
	public function test_find_attribute_in_parents($token, $find, $expected)
	{
		$this->setup_config();
		$this->setup_token_structure();
		$actual = $this->object->find_attribute_in_parents($this->tokens[$token], $find);
		$this->assert_found_attribute($expected, $find, $actual);
	}

	public function provide_find_attribute_in_parents()
	{
		// [source_token, find_attribute, found_in_token]
		return array(
			// Not found
			array('method1', 'property-that-not-exists', NULL),
			// Found in self
			array('ann1', 'name', 'ann1'),
			array('class', 'properties', 'class'),
			// Found in parents
			array('ann2', 'text', 'doccomment1'),
			array('arg1', 'namespace', 'class'),
		);
	}

	/**
	 * @dataProvider  provide_find_attribute_in_children
	 * @todo          FIXME
	 */
	public function _test_find_attribute_in_children($token, $find, $expected)
	{
		$this->setup_config();
		$this->setup_token_structure();
		$actual = $this->object->find_attribute_in_children($this->tokens[$token], $find);
		$this->assert_found_attribute($expected, $find, $actual);
	}

	public function provide_find_attribute_in_children()
	{
		// [source_token, find_attribute, found_in_token]
		return array(
			// Not found
			array('property1', 'attribute-that-not-exists', NULL),
			// Found in self
			array('ann2', 'name', 'ann2'),
			array('class', 'methods', 'class'),
			// Found in children
			// FIXME
		);
	}

	protected function assert_found_attribute($expected, $find, $actual)
	{
		if ($expected !== NULL)
		{
			$this->assertSame($this->tokens[$expected]->get($find), $actual);
		}
		else
		{
			$this->assertNull($actual);
		}
	}

	protected function setup_token_structure()
	{
		$factory = $this->config->helper('tokenFactory');
		$this->tokens['ann1'] = $factory->create('Annotation', array(
			'name' => 'author',
			'columns' => array('Korney Czukowski'),
		));
		$this->tokens['ann2'] = $factory->create('Annotation', array(
			'name' => 'copyright',
			'columns' => array('(c) 2012 Korney Czukowski'),
		));
		$this->tokens['ann3'] = $factory->create('Annotation', array(
			'name' => 'license',
			'columns' => array('MIT License'),
		));
		$this->tokens['doccomment1'] = $factory->create('DocComment', array(
			'text' => 'This is a generated test class to check the render integration across most of the tokens',
			'annotations' => array($this->tokens['ann1'], $this->tokens['ann2'], $this->tokens['ann3']),
		));
		$this->tokens['ann4'] = $factory->create('Annotation', array(
			'name' => 'var',
			'columns' => array('array', 'Values array'),
		));
		$this->tokens['doccomment2'] = $factory->create('DocComment', array(
			'annotations' => array($this->tokens['ann4']),
		));
		$this->tokens['property1'] = $factory->create('Property', array(
			'access' => 'private',
			'name' => 'values',
			'comment' => $this->tokens['doccomment2'],
		));
		$this->tokens['arg1'] = $factory->create('Argument', array(
			'constraint' => 'array',
			'name' => 'array values',
		));
		$this->tokens['method1'] = $factory->create('Method', array(
			'access' => 'public',
			'name' => '__construct',
			'comment' => 'Class constructor',
			'arguments' => array($this->tokens['arg1']),
			'body' => '$this->values = $array_values;'
		));
		$this->tokens['class'] = $factory->create('Class', array(
			'comment' => $this->tokens['doccomment1'],
			'namespace' => 'CodeGenerator',
			'use' => array('code generator\math\simple optimizer'),
			'name' => 'TestClass',
			'properties' => array($this->tokens['property1']),
			'methods' => array($this->tokens['method1']),
		));
	}

	protected function setup_config($config = array())
	{
		$this->config = new \CodeGenerator\Config($config);
	}
}