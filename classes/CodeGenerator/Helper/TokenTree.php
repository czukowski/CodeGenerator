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
	 * @var  array  Available path tokens. Do not confuse with other Tokens.
	 */
	private $path_tokens = array(
		'\.(?:/|$)' => 'get_self',
		'\.\.(?:/|$)' => 'get_parent',
		'[A-Z][A-Za-z]+' => 'get_by_type',
		'[a-z][A-Za-z]+' => 'get_by_attribute',
		'\[\d+\]' => 'get_ord',
		'\.[a-z]+' => 'get_attribute',
		'\|[a-z-]+' => 'format_attribute',
	);

	/**
	 * Finds a token or it's attributes by a specified path. If not found, NULL is returned.
	 * 
	 * @param   \CodeGenerator\Token\Token  $from
	 * @param   string  $path
	 * @return  mixed
	 */
	public function find_path(Token\Token $from, $path)
	{
		$current_path = $path;
		$current_object = $from;
		while (($parameters = $this->parse_next_path_token($current_path))) {
			list ($method, $argument) = $parameters;
			$current_object = call_user_func(array($this, $method), $current_object, $argument);
		}
		return $current_object;
	}

	/**
	 * Parses the next path part from the left and returns method nema to process it.
	 * Note: it also trims the current path part.
	 * 
	 * @param   string  $path  Passed by reference
	 * @return  string
	 */
	private function parse_next_path_token(&$path)
	{
		if ( ! $path)
		{
			return;
		}
		foreach ($this->path_tokens as $token => $processing_method)
		{
			$regex = '#^(?<argument>'.$token.')#';
			if (preg_match($regex, $path, $matches)) {
				$path = preg_replace('#^'.$token.'(.*)#', '\1', $path);
				return array($this->path_tokens[$token], $matches['argument']);
			}
		}
		throw new \InvalidArgumentException('Invalid path: "'.$path.'"');
	}

	/**
	 * Returns the method argument.
	 * 
	 * @param   Token\Token  $from
	 * @return  Token\Token
	 */
	public function get_self($from)
	{
		if ($from instanceof Token\Token)
		{
			return $from;
		}
		throw new \InvalidArgumentException('Path element "." cannot be used on non-token items.');
	}

	/**
	 * Returns the parent token of the argument, but skips Block tokens.
	 * Therefore, this method can't be used to find Block tokens.
	 * 
	 * @param   Token\Token  $from
	 * @return  Token\Token
	 */
	public function get_parent($from)
	{
		if ($from instanceof Token\Token)
		{
			$parent = $from->get('parent');
			if ($parent instanceof Token\Block)
			{
				return $this->get_parent($parent);
			}
			return $parent;
		}
		throw new \InvalidArgumentException('Path element ".." cannot be used on non-token items.');
	}

	/**
	 * Returns the token or tokens by the specified type.
	 * 
	 * @param   Token\Token  $from
	 * @param   string       $type
	 * @return  array
	 */
	public function get_by_type($from, $type)
	{
		if ($from instanceof Token\Token)
		{
			$result = array();
			foreach ($from->get_children() as $child)
			{
				if ($child->get_type() === $this->get_type($type))
				{
					$result[] = $child;
				}
			}
			return $result;
		}
		throw new \InvalidArgumentException('Path element "'.$type.'" cannot be used on non-token items.');
	}

	/**
	 * Returns the token or tokens by the specified attribute name.
	 * This method can't be used to find Block tokens.
	 * 
	 * @param   Token\Token  $from
	 * @param   string       $attribute
	 * @return  Token\Token
	 */
	public function get_by_attribute($from, $attribute)
	{
		if ($from instanceof Token\Token)
		{
			$object = $from->get($attribute);
			if ($object instanceof Token\Block)
			{
				return $object->get('items');
			}
			return $object;
		}
		throw new \InvalidArgumentException('Path element "'.$attribute.'" cannot be used on non-token items.');
	}

	/**
	 * Returns the n-th token from a colelction by a `$number` argument.
	 * 
	 * @param   string   $from
	 * @param   integer  $number
	 * @return  Token\Token
	 */
	public function get_ord($from, $number)
	{
		$number = intval(trim($number, '[]'));
		if ( ! is_array($from))
		{
			throw new \InvalidArgumentException('Path element "['.$number.']" cannot be used on token items.');
		}
		if ($number >= count($from))
		{
			throw new \OutOfRangeException('Elements count is less than "'.$number.'".');
		}
		return $from[$number];
	}

	/**
	 * Returns the specified attribute from a token.
	 * 
	 * @param   Token\Token  $from
	 * @param   string       $attribute
	 * @return  mixed
	 */
	public function get_attribute($from, $attribute)
	{
		if ($from instanceof Token\Token)
		{
			return $from->get(ltrim($attribute, '.'));
		}
		throw new \InvalidArgumentException('Path element "'.$attribute.'" cannot be used on non-token items.');
	}

	/**
	 * Returns the formatted value of the specified attribute from a token.
	 * 
	 * @param   string  $from
	 * @param   string  $format
	 * @return  mixed
	 */
	public function format_attribute($from, $format)
	{
		if ($from instanceof Token\Token OR (is_array($from) AND reset($from) instanceof Token\Token))
		{
			throw new \InvalidArgumentException('Path element "'.$format.'" cannot be used on token or array items.');
		}
		$renderer = $this->config->helper('tokenPartsRenderer');
		$method_name = 'render_'.str_replace('-', '_', ltrim($format, '|'));
		if (method_exists($renderer, $method_name))
		{
			return $renderer->$method_name($from);
		}
		throw new \InvalidArgumentException('Unknown format item "'.ltrim($format, '|').'".');
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