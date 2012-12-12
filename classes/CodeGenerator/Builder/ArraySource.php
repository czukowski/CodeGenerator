<?php
/**
 * Array source class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Builder;

class ArraySource extends TokenMeta
{
	/**
	 * @param  array  $source
	 */
	public function __construct($source)
	{
		if ( ! $this->is_source_array($source))
		{
			throw new \InvalidArgumentException('Argument cannot be used to build tokens.');
		}
		$this->set_name($source[0]);
		$this->set_attributes($source[1]);
	}

	/**
	 * Extracts child array sources and builds a tree.
	 * 
	 * @param  array  $attributes
	 */
	public function set_attributes($attributes)
	{
		foreach ($attributes as $name => $attribute)
		{
			$attributes[$name] = $this->process_attribute($attribute);
		}
		parent::set_attributes($attributes);
	}

	/**
	 * @param   mixed  $attribute
	 * @return  mixed
	 */
	private function process_attribute($attribute)
	{
		if ($this->is_source_array($attribute))
		{
			return new self($attribute);
		}
		elseif (is_array($attribute))
		{
			foreach ($attribute as $i => $item)
			{
				$attribute[$i] = $this->process_attribute($item);
			}
		}
		return $attribute;
	}

	/**
	 * Returns TRUE if the array can be converted to a Token meta object.
	 * 
	 * @param   array    $array
	 * @return  boolean
	 */
	private function is_source_array($array)
	{
		return is_array($array)
			AND count($array) === 2
			AND is_string($array[0])
			AND is_array($array[1])
			AND preg_match('#^[A-Z][A-Za-z]++$#', $array[0]);
	}
}