<?php
/**
 * Sample token generator
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class Samples extends Object
{
	/**
	 * @return  array
	 */
	public function get_sample()
	{
		$factory = $this->config->helper('tokenFactory');
		$sample['ann1'] = $factory->create('Annotation', array(
			'name' => 'author',
			'columns' => array('Korney Czukowski'),
		));
		$sample['ann2'] = $factory->create('Annotation', array(
			'name' => 'copyright',
			'columns' => array('(c) 2012 Korney Czukowski'),
		));
		$sample['ann3'] = $factory->create('Annotation', array(
			'name' => 'license',
			'columns' => array('MIT License'),
		));
		$sample['doccomment1'] = $factory->create('DocComment', array(
			'text' => 'This is a generated test class to check the render integration across most of the tokens',
			'annotations' => array($sample['ann1'], $sample['ann2'], $sample['ann3']),
		));
		$sample['ann4'] = $factory->create('Annotation', array(
			'name' => 'var',
			'columns' => array('array', 'Values array'),
		));
		$sample['doccomment2'] = $factory->create('DocComment', array(
			'annotations' => array($sample['ann4']),
		));
		$sample['property1'] = $factory->create('Property', array(
			'access' => 'private',
			'name' => 'values',
			'comment' => $sample['doccomment2'],
		));
		$sample['arg1'] = $factory->create('Argument', array(
			'constraint' => 'array',
			'name' => 'array values',
		));
		$sample['method1'] = $factory->create('Method', array(
			'access' => 'public',
			'name' => '__construct',
			'comment' => 'Class constructor',
			'arguments' => array($sample['arg1']),
			'body' => '$this->values = $array_values;'
		));
		$sample['class'] = $factory->create('Class', array(
			'comment' => $sample['doccomment1'],
			'namespace' => 'CodeGenerator',
			'use' => array('code generator\math\simple optimizer'),
			'name' => 'TestClass',
			'properties' => array($sample['property1']),
			'methods' => array($sample['method1']),
		));
		return $sample;
	}

}