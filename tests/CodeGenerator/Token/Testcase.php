<?php
/**
 * TokenTest
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;

class Testcase extends \CodeGenerator\Framework\Testcase
{
	/**
	 * @var  \CodeGenerator\Config
	 */
	protected $config;
	/**
	 * @var  array  Backup of the replaced helpers
	 */
	private $_backup_helpers = array();

	/**
	 * @param  array  $attributes
	 * @param  array  $options
	 */
	protected function setup_with_attributes($attributes, $options = array())
	{
		$this->setup_object($options);
		$this->set_object_attributes($this->object, $attributes);
	}

	/**
	 * @param  object  $object
	 * @param  array   $attributes
	 */
	protected function set_object_attributes($object, $attributes)
	{
		foreach ($attributes as $name => $value)
		{
			$object->set($name, $value);
		}
	}

	/**
	 * Setup config
	 */
	protected function setup_config()
	{
		$this->config = new \CodeGenerator\Config;
	}

	/**
	 * @return  \CodeGenerator\Config
	 */
	protected function get_config()
	{
		if ($this->config === NULL)
		{
			$this->setup_config();
		}
		return $this->config;
	}

	/**
	 * @return  array
	 */
	protected function get_class_constructor_arguments()
	{
		$this->setup_config();
		return array($this->config);
	}

	/**
	 * @param  string  $helper
	 * @param  object  $object
	 */
	protected function replace_helper($helper, $object)
	{
		if ( ! array_key_exists($helper, $this->_backup_helpers))
		{
			$current_helpers = $this->get_object_property($this->config, 'helpers')
				->getValue($this->config);
			$this->_backup_helpers[$helper] = isset($current_helpers[$helper]) ? $current_helpers[$helper] : NULL;
			$current_helpers[$helper] = $object;
			$this->get_object_property($this->config, 'helpers')
				->setValue($this->config, $current_helpers);
		}
	}

	protected function restore_helpers()
	{
		$current_helpers = $this->get_object_property($this->config, 'helpers')
			->getValue($this->config);
		foreach ($this->_backup_helpers as $helper => $object)
		{
			$current_helpers[$helper] = $object;
		}
		$this->get_object_property($this->config, 'helpers')
			->setValue($this->config, $current_helpers);
	}

	protected function tearDown()
	{
		$this->restore_helpers();
		parent::tearDown();
	}
}