<?php
/**
 * Builder base test class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Builder;

class Testcase extends \CodeGenerator\Framework\Testcase
{
	/**
	 * Asserts the first argument is a Token and that it contains child tokens found using paths
	 * passed in the second argument.
	 * 
	 * @param  Token  $token
	 * @param  array  $paths
	 */
	protected function assert_token_tree($token, $paths)
	{
		$this->assertInstanceOf('\CodeGenerator\Token\Token', $token);
		foreach ($paths as $path => $expected_type)
		{
			// Token tree search is used for convenience only, it's not required for token builder to work.
			$actual = $this->get_config()
				->helper('tokenTree')
				->find_path($token, $path);
			$this->assertInstanceOf('\CodeGenerator\Token\Token', $actual);
			$this->assertEquals($expected_type, $actual->get_type());
		}
	}

	/**
	 * Builds token from `$this->object`.
	 * 
	 * @return  Token
	 */
	protected function build_token()
	{
		return $this->get_config()
			->helper('tokenBuilder')
			->build($this->object);
	}
}