Code Generator library (experimental)
=====================================

Sorry, no documentation yet... patches welcome! :)

Still so much room for improvement and so little time to do it...

Just an example
---------------

Something like this may work:

	$my_class = array(
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
	));

	$generator = \CodeGenerator\Helper\TokenBuilder(new \CodeGenerator\Config);
	echo $generator->generate($my_class);

Will print the following:

	/**
	 * This is a generated test class to check the render integration across most of the tokens
	 * 
	 * @author     Korney Czukowski
	 * @copyright  (c) 2012 Korney Czukowski
	 * @license    MIT License
	 */
	namespace CodeGenerator;
	use CodeGenerator\Math\SimpleOptimizer;
	
	class TestClass
	{
		/**
		 * @var  array  Values array
		 */
		private $values;
	
		/**
		 * Class constructor - somewhat longer description
		 */
		public function __construct(array $array_values)
		{
			$this->values = $array_values;
		}
	}
