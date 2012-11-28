<?php
/**
 * Token measurement helper class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;
use CodeGenerator\Token;

class TokenMeasure extends \CodeGenerator\Singleton
{
	/**
	 * Returns total indentation of token and all its parents
	 * 
	 * @param   Token   $token
	 * @return  integer
	 */
	public function get_indentation(Token\Token $token)
	{
		$counter = 0;
		do
		{
			if ($token instanceof Token\Block)
			{
				$counter += $token->get('indentation');
			}
		}
		while (($token = $token->get('parent')));
		return $counter;
	}

	/**
	 * Returns total indentation of token and all its parents in character length
	 * 
	 * @param   Token   $token
	 * @reutrn  integer
	 */
	public function get_indentation_length(Token\Token $token)
	{
		$indent_char = $this->config->get_format('indent');
		$config_key = 'char_length.'.$this->encode_string($indent_char);
		$char_length = $this->config->get_options($config_key, strlen($indent_char));
		return $char_length * $this->get_indentation($token);
	}

	/**
	 * Encodes indentation character or string to safely store as a config key
	 */
	public function encode_string($string)
	{
		$encoded = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$encoded .= 'x'.str_pad(dechex(ord($string[$i])), 2, '0', STR_PAD_LEFT);
		}
		return $encoded;
	}

	/**
	 * Finds a token of the specified type in parent chain. If not found, NULL is returned.
	 * 
	 * @param   \CodeGenerator\Token\Token  $from
	 * @param   string  $type
	 * @return  mixed
	 */
	public function find_in_parents(Token\Token $from, $type)
	{
		$parent = $from;
		do
		{
			if ($parent instanceof Token\Token AND $parent->get_type() === $this->get_type($type))
			{
				return $parent;
			}
		}
		while (($parent = $parent->get('parent')));
	}

	/**
	 * Finds a token of the specified type in children. If not found, empty array is returned.
	 * 
	 * @param   \CodeGenerator\Token\Token  $from
	 * @param   string  $type
	 * @return  array
	 */
	public function find_in_children(Token\Token $from, $type)
	{
		$result = array();
		foreach ($from->get_children() as $child)
		{
			if ($child->get_type() === $this->get_type($type))
			{
				$result[] = $child;
			}
			elseif ($child instanceof Token\Token)
			{
				$result = array_merge($result, $this->find_in_children($child, $type));
			}
		}
		return $result;
	}

	/**
	 * Returns full type name.
	 * 
	 * @param   string  $alias
	 * @return  string
	 */
	private function get_type($alias)
	{
		return 'CodeGenerator\Token\\'.$this->config->helper('tokenFactory')
			->get_type_by_alias($alias);
	}

}