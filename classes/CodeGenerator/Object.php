<?php
/**
 * Base helper class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class Object
{
	/**
	 * @var  Config
	 */
	protected $config;

	/**
	 * Class constructor
	 * 
	 * @param  Config  $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
}