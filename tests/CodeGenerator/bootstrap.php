<?php
/**
 * PHPUnit bootstrap for CodeGenerator
 * 
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
require_once __DIR__.'/Framework/Autoloader.php';
spl_autoload_register(array(
	new CodeGenerator\Framework\Autoloader(array(__DIR__.'/..', __DIR__.'/../../classes')),
	'load',
));