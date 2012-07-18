<?php
/**
 * Base class for tabular tokens
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Token;
use CodeGenerator\String;

abstract class Columns extends Token
{
	const PAD_RIGHT = 'right';
	const PAD_LEFT = 'left';
	const PAD_CENTER = 'center';

	/**
	 * @var  array  Column widths are stored here
	 */
	protected $widths = array();
	/**
	 * @var  array  Column alignments are stored here
	 */
	protected $alignments = array();

	/**
	 * Gets or sets column widths. See below for description and usage.
	 * 
	 * @return  mixed
	 */
	public function widths()
	{
		return $this->_get_set('widths', func_get_args());
	}

	/**
	 * Gets or sets column alignments. See below for description and usage.
	 * 
	 * @return  mixed
	 */
	public function alignments()
	{
		return $this->_get_set('alignments', func_get_args());
	}

	/**
	 * Universal getter and setter method. Takes 0 to 2 arguments.
	 * 
	 *     $widths = $columns->widths();          // Gets column widths
	 *     $alignment = $columns->alignments(1);  // Gets alignment of the 2nd column
	 *     $columns->widths(array(5, 8));         // Sets column width
	 *     $columns->alignments(0, 'right');      // Sets alignment of the 1st column
	 * 
	 * @param   string  $property
	 * @param   array   $arguments
	 * @return  \CodeGenerator\Token\Columns
	 * @throws  \OutOfRangeException
	 * @throws  \InvalidArgumentException
	 */
	private function _get_set($property, $arguments)
	{
		$count = count($arguments);
		if ($count === 0)
		{
			return $this->{$property};
		}
		elseif ($count === 1 AND is_array($arguments[0]) AND $this->_validate_values($property, $arguments[0]))
		{
			$this->{$property} = $arguments[0];
			return $this;
		}
		elseif ($count === 1 AND is_int($arguments[0]))
		{
			if (isset($this->{$property}[$arguments[0]]))
			{
				return $this->{$property}[$arguments[0]];
			}
			throw new \OutOfRangeException('Column.'.$property.'['.$arguments[0].'] is not set');
		}
		elseif ($count === 2 AND is_int($arguments[0]) AND $this->_validate_value($property, $arguments[1]))
		{
			$this->{$property}[$arguments[0]] = $arguments[1];
			return $this;
		}
		throw new \InvalidArgumentException('Invalid arguments for '.$this->token().'.'.$property.'() method');
	}

	/**
	 * @param   string  $property
	 * @param   array   $values
	 * @return  boolean
	 */
	private function _validate_values($property, array $values)
	{
		foreach ($values as $value)
		{
			if ( ! $this->_validate_value($property, $value))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param   string  $property
	 * @param   mixed   $value
	 * @return  boolean
	 */
	private function _validate_value($property, $value)
	{
		switch ($property) {
			case 'widths':
				return is_int($value);
			case 'alignments':
				return in_array($value, array(
					self::PAD_RIGHT, self::PAD_LEFT, self::PAD_CENTER,
					STR_PAD_RIGHT, STR_PAD_LEFT, STR_PAD_BOTH,
				), TRUE);
		}
	}

	/**
	 * @param   array  $columns
	 * @return  string
	 */
	protected function render_columns(array $columns = array())
	{
		if ( ! $columns)
		{
			return '';
		}
		$pad_string = $this->format->format('column_delimiter');
		foreach ($columns as $i => &$column)
		{
			$column = String::str_pad($column, $this->_width($i), $pad_string, $this->_padding_mode($i));
		}
		if ($this->_padding_mode(count($columns) - 1) === STR_PAD_RIGHT)
		{
			$columns[count($columns) - 1] = String::rtrim($columns[count($columns) - 1], $pad_string);
		}
		return implode(str_repeat($pad_string, $this->format->options('column_min_space')), $columns);
	}

	/**
	 * @param   integer  $index
	 * @return  integer
	 */
	private function _padding_mode($index)
	{
		if ( ! isset($this->alignments[$index]))
		{
			return STR_PAD_RIGHT;
		}
		elseif (is_string($this->alignments[$index]))
		{
			switch ($this->alignments[$index])
			{
				case self::PAD_LEFT:
					return STR_PAD_RIGHT;
				case self::PAD_RIGHT:
					return STR_PAD_LEFT;
				case self::PAD_CENTER:
					return STR_PAD_BOTH;
			}
		}
		return $this->alignments[$index];
	}

	/**
	 * @param   integer  $index
	 * @return  integer
	 */
	private function _width($index)
	{
		return isset($this->widths[$index]) ? $this->widths[$index] : 0;
	}
}