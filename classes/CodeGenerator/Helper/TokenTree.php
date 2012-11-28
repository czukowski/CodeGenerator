<?php
/**
 * Token tree walker helper class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;
use CodeGenerator\Token;

class TokenTree extends \CodeGenerator\Singleton
{
	/**
	 * Finds a token of the specified type in parent chain. If not found, NULL is returned.
	 * 
	 * @param   \CodeGenerator\Token\Token  $from
	 * @param   string  $type
	 * @return  array
	 */
	public function find_token(Token\Token $from, $type)
	{
		return array_filter(array_merge(
			array($this->find_in_parents($from, $type)),
			$this->find_in_children($from, $type)
		));
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