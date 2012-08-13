<?php
/**
 * Helper class to validate various attributes
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class Validator extends \CodeGenerator\Singleton
{
	const NAME_PATTERN = '[A-Za-z_][A-Za-z0-9_]+?';

	public function validate_constraint($value)
	{
		return is_string($value) AND preg_match('#^(?:\\\\?'.self::NAME_PATTERN.')+$#', $value);
	}

	public function validate_integer($value)
	{
		return is_int($value);
	}

	public function validate_name($value)
	{
		return is_string($value) AND preg_match('#^'.self::NAME_PATTERN.'$#', $value);
	}
}