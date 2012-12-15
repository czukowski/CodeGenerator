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
use \CodeGenerator\Builder\ArraySource;

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
		$this->object = $this->config->helper('tokenBuilder')
			->build(new ArraySource(array(
				'Class', array(
					'comment' => array('DocComment', array(
						'text' => 'This is a generated test class to check the render integration across most of the tokens',
						'annotations' => array(
							array('Annotation', array(
								'name' => 'author',
								'columns' => array('Korney Czukowski'),
							)),
							array('Annotation', array(
								'name' => 'copyright',
								'columns' => array('(c) 2012 Korney Czukowski'),
							)),
							array('Annotation', array(
								'name' => 'license',
								'columns' => array('MIT License'),
							)),
						),
					)),
					'namespace' => 'code generator',
					'use' => array('code generator\math\simple optimizer'),
					'name' => 'test class',
					'properties' => array(
						array('Property', array(
							'access' => 'private',
							'name' => 'values',
							'comment' => array('DocComment', array(
								'annotations' => array(
									array('Annotation', array(
										'name' => 'var',
										'columns' => array('array', 'Values array'),
									)),
								),
							)),
						)),
					),
					'methods' => array(
						array('Method', array(
							'access' => 'public',
							'name' => '__construct',
							'comment' => 'Class constructor - somewhat longer description',
							'arguments' => array(
								array('Argument', array(
									'constraint' => 'array',
									'name' => 'array values',
								)),
							),
							'body' => '$this->values = {{../arguments[0].name|variable-name}};'
						)),
					),
				),
			)));
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
				"\t * Class constructor - somewhat longer description\n".
				"\t */\n".
				"\tpublic function __construct(array \$array_values)\n".
				"\t{\n".
				"\t\t\$this->values = \$array_values;\n".
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
						'names' => array(
							'variable' => 'camelcase',
						),
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
				"     * Class constructor - somewhat\n".
				"     * longer description\n".
				"     */\n".
				"    public function __construct(array \$arrayValues) {\n".
				"        \$this->values = \$arrayValues;\n".
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