<?php
/**
 * Matrix class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Math;

class Matrix
{
	private $_matrix = array();
	private $_dimensions = array(0, 0);

	/**
	 * @param  array  $matrix  Array of arrays
	 */
	public function __construct(array $matrix)
	{
		$this->_setup_matrix($matrix);
	}

	private function _setup_matrix(array $matrix)
	{
		$this->_matrix = array();
		$this->_setup_dimensions($matrix);
		for ($j = 0; $j < $this->_dimensions[1]; $j++)
		{
			for ($i = 0; $i < $this->_dimensions[0]; $i++)
			{
				$this->_matrix[$i][$j] = isset($matrix[$i][$j]) ? $matrix[$i][$j] : NULL;
			}
		}
	}

	private function _setup_dimensions(array $matrix)
	{
		$this->_dimensions[1] = count($matrix);
		for ($i = 0; $i < $this->_dimensions[1]; $i++)
		{
			if ( ! is_array($matrix[$i]))
			{
				throw new \InvalidArgumentException('Argument must be array of arrays');
			}
			$this->_dimensions[0] = max($this->_dimensions[0], count($matrix[$i]));
		}
	}

	/**
	 * Returns matrix dimension
	 * 
	 * @param   integer  $dim  0 for columns count, 1 for rows count
	 * @return  mixed
	 * @throws  \InvalidArgumentException
	 */
	public function get_dimension()
	{
		if (func_num_args() === 0)
		{
			return $this->_dimensions;
		}
		$dim = func_get_arg(0);
		if ( ! in_array($dim, array(0, 1), TRUE))
		{
			throw new \InvalidArgumentException('Use 0 for columns, 1 for rows');
		}
		return $this->_dimensions[$dim];
	}

	/**
	 * Return cell value or the whole row or column or matrix, depending on which arguments are NULL
	 * 
	 * @param   integer|NULL  $i  Row index
	 * @param   integer|NULL  $j  Column index
	 * @return  array
	 */
	public function get($i = NULL, $j = NULL)
	{
		if ($i === NULL AND $j === NULL)
		{
			return $this->_matrix;
		}
		elseif ($i === NULL)
		{
			return $this->get_column($j);
		}
		elseif ($j === NULL)
		{
			return $this->get_row($i);
		}
		return $this->get_cell($i, $j);
	}

	/**
	 * Returns a single cell value
	 * 
	 * @param   integer  $i  Row index
	 * @param   integer  $j  Column index
	 * @reutrn  mixed
	 */
	public function get_cell($i, $j)
	{
		$this->_assert_dimension_value(0, $j);
		$this->_assert_dimension_value(1, $i);
		return $this->_matrix[$i][$j];
	}

	/**
	 * Return a column vector
	 * 
	 * @param   integer  $j  Column index
	 * @return  array
	 */
	public function get_column($j)
	{
		$this->_assert_dimension_value(0, $j);
		$col = array();
		foreach ($this->_matrix as $row)
		{
			$col[] = $row[$j];
		}
		return $col;
	}

	/**
	 * Return a row vector
	 * 
	 * @param   integer  $i  Row index
	 * @return  array
	 */
	public function get_row($i)
	{
		$this->_assert_dimension_value(1, $i);
		return $this->_matrix[$i];
	}

	/**
	 * Sets cell value or the whole row or column or matrix, depending on which arguments are NULL
	 * 
	 * @param   integer|NULL  $i      Row coordinate
	 * @param   integer|NULL  $j      Column coordinate
	 * @param   mixed         $value  Content for a desired part
	 * @return  array
	 */
	public function set($i, $j, $value)
	{
		if ($i === NULL AND $j === NULL)
		{
			return $this->_setup_matrix($value);
		}
		elseif ($i === NULL)
		{
			return $this->set_column($j, $value);
		}
		elseif ($j === NULL)
		{
			return $this->set_row($i, $value);
		}
		return $this->set_cell($i, $j, $value);
	}

	/**
	 * Sets a single cell value
	 * 
	 * @param   integer  $i
	 * @param   integer  $j
	 * @param   mixed    $value
	 */
	public function set_cell($i, $j, $value)
	{
		$this->_assert_dimension_value(0, $j);
		$this->_assert_dimension_value(1, $i);
		$this->_matrix[$i][$j] = $value;
	}

	/**
	 * Sets a column vector
	 * 
	 * @param   integer  $j  Column index
	 * @param   mixed    $value
	 */
	public function set_column($j, $value)
	{
		$this->_assert_dimension_value(0, $j, $value);
		foreach ($this->_matrix as &$row)
		{
			$row[$j] = $value[$j];
		}
	}

	/**
	 * Sets a row vector
	 * 
	 * @param   integer  $i  Row index
	 * @param   mixed    $value
	 */
	public function set_row($i, $value)
	{
		$this->_assert_dimension_value(1, $i, $value);
		$this->_matrix[$i] = $value;
	}

	/**
	 * @param   integer  $dim    Dimension: 0 or 1
	 * @param   integer  $value  Dimension value (index)
	 * @param   array    $aux    Optionally, verify that this array length does not exceed the dimension size
	 * @throws  \InvalidArgumentException
	 */
	private function _assert_dimension_value($dim, $value, $aux = NULL)
	{
		if ($value < 0 OR $value >= $this->_dimensions[$dim] OR ($aux !== NULL AND count($aux) > $this->_dimensions[$dim]))
		{
			throw new \InvalidArgumentException('Dimension '.$dim.' is '.$this->_dimensions[$dim]);
		}
	}

	/**
	 * Returns transponed matrix
	 * 
	 * @reutrn  Matrix
	 */
	public function transpone()
	{
		$transponed = array();
		for ($i = 0; $i < $this->_dimensions[1]; $i++)
		{
			$transponed[] = $this->get_column($i);
		}
		return new Matrix($transponed);
	}
}