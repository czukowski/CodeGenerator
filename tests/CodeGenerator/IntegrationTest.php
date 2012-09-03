<?php
/**
 * Intergration test
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class IntegrationTest extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @dataProvider  provide_config
	 */
	public function test_class_render($config, $expected)
	{
		$this->setup_config($config);
		$this->setup_class_render();
		$actual = (string) $this->object;
		$this->assertSame($expected, $actual);
	}

	protected function setup_class_render()
	{
		$factory = $this->config->helper('tokenFactory');
		$this->object = $factory->create('Class', array(
			'comment' => $factory->create('DocComment', array(
				'text' => 'This is a generated test class to check the render integration across most of the tokens',
				'annotations' => array(
					$factory->create('Annotation', array(
						'name' => 'author',
						'columns' => array('Korney Czukowski'),
					)),
					$factory->create('Annotation', array(
						'name' => 'copyright',
						'columns' => array('(c) 2012 Korney Czukowski'),
					)),
					$factory->create('Annotation', array(
						'name' => 'license',
						'columns' => array('MIT License'),
					)),
				),
			)),
			'namespace' => 'CodeGenerator',
			'use' => array('CodeGenerator\Math\SimpleOptimizer'),
			'name' => 'TestClass',
			'properties' => array(
				$factory->create('Property', array(
					'access' => 'private',
					'name' => 'values',
				)),
			),
			'methods' => array(
				$factory->create('Method', array(
					'access' => 'public',
					'name' => '__construct',
					'arguments' => array(
						$factory->create('Argument', array(
							'constraint' => 'array',
							'name' => 'values',
						)),
					),
					'body' => '$this->values = $values;'
				)),
			),
		));
	}

	public function provide_config()
	{
		return array(
			// Default config values
			array(
				array(),
				"/**\n".
				" * This is a generated test class to check the render integration across most of the tokens\n".
				" * \n".
				" * @author     Korney Czukowski\n".
				" * @copyright  (c) 2012 Korney Czukowski\n".
				" * @license    MIT License\n".
				" */\n".
				"namespace CodeGenerator;\n".
				"use CodeGenerator\Math\SimpleOptimizer;\n".
				"\n".
				"class TestClass\n".
				"{\n".
				"\tprivate \$values;\n".
				"\n".
				"\tpublic function __construct(array \$values)\n".
				"\t{\n".
				"\t\t\$this->values = \$values;\n".
				"\t}\n".
				"}",
			),
		);
	}

	protected function setup_config($config = array())
	{
		$this->config = new \CodeGenerator\Config($config);
	}
}