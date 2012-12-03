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
	 * @var  array
	 */
	private $tokens = array();

	/**
	 * @return  array
	 */
	public function get_sample()
	{
		if (empty($this->tokens))
		{
			$this->setup();
		}
		return $this->tokens;
	}

	/**
	 * @return  Samples
	 */
	public function setup()
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
		$this->tokens['methodbody1'] = $factory->create('Block', array(
			'items' => '$this->values = $array_values;'
		));
		$this->tokens['method1'] = $factory->create('Method', array(
			'access' => 'public',
			'name' => '__construct',
			'comment' => 'Class constructor',
			'arguments' => array($this->tokens['arg1']),
			'body' => $this->tokens['methodbody1']
		));
		$this->tokens['methodbody2'] = $factory->create('Block', array(
			'items' => 'return $this->values;'
		));
		$this->tokens['method2'] = $factory->create('Method', array(
			'access' => 'public',
			'name' => 'get values',
			'comment' => 'Get object values',
			'body' => $this->tokens['methodbody2']
		));
		$this->tokens['class'] = $factory->create('Class', array(
			'comment' => $this->tokens['doccomment1'],
			'namespace' => 'CodeGenerator',
			'use' => array('code generator\math\simple optimizer'),
			'name' => 'TestClass',
			'properties' => array($this->tokens['property1']),
			'methods' => array($this->tokens['method1'], $this->tokens['method2']),
		));
		return $this;
	}

	/**
	 * @return  Samples
	 */
	public function call_before_render()
	{
		foreach ($this->tokens as $token)
		{
			$before_render = new \ReflectionMethod($token, 'before_render');
			$before_render->setAccessible(TRUE);
			$before_render->invoke($token);
		}
		return $this;
	}
}