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
					'comment' => $factory->create('DocComment', array(
						'annotations' => array(
							$factory->create('Annotation', array(
								'name' => 'var',
								'columns' => array('array', 'Values array'),
							)),
						),
					)),
				)),
			),
			'methods' => array(
				$factory->create('Method', array(
					'access' => 'public',
					'name' => '__construct',
					'comment' => 'Class constructor',
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
				"\t/**\n".
				"\t * @var  array  Values array\n".
				"\t */\n".
				"\tprivate \$values;\n".
				"\n".
				"\t/**\n".
				"\t * Class constructor\n".
				"\t */\n".
				"\tpublic function __construct(array \$values)\n".
				"\t{\n".
				"\t\t\$this->values = \$values;\n".
				"\t}\n".
				"}",
			),
			// Non-default config values
			array(
				array(
					'format' => array(
						'brace_open' => " {",
						'indent' => "    ",
					),
					'options' => array(
						'line_width' => 40,
					),
				),
				"/**\n".
				" * This is a generated test class to\n".
				" * check the render integration across\n".
				" * most of the tokens\n".
				" * \n".
				" * @author     Korney Czukowski\n".
				" * @copyright  (c) 2012 Korney Czukowski\n".
				" * @license    MIT License\n".
				" */\n".
				"namespace CodeGenerator;\n".
				"use CodeGenerator\Math\SimpleOptimizer;\n".
				"\n".
				"class TestClass {\n".
				"    /**\n".
				"     * @var  array  Values array\n".
				"     */\n".
				"    private \$values;\n".
				"\n".
				"    /**\n".
				"     * Class constructor\n".
				"     */\n".
				"    public function __construct(array \$values) {\n".
				"        \$this->values = \$values;\n".
				"    }\n".
				"}",
			),
		);
	}

	protected function setup_config($config = array())
	{
		$this->config = new \CodeGenerator\Config($config);
	}
}