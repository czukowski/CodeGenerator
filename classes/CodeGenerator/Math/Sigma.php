<?php
/**
 * Sigma _/¯ function class
 * 
 * @package    CodeGenerator
 * @category   Tokens
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Math;

class Sigma
{
	private $_x1;
	private $_x2;
	private $_y1;
	private $_y2;

	public function __construct($x1, $x2, $y1, $y2)
	{
		$this->_x1 = $x1;
		$this->_x2 = $x2;
		$this->_y1 = $y1;
		$this->_y2 = $y2;
	}

	public function value($argument)
	{
		$value = ($this->_y2 - $this->_y1) / ($this->_x2 - $this->_x1) * ($argument - $this->_x1) + $this->_y1;
		if ($this->_y2 > $this->_y1)
		{
			return max($this->_y1, min($this->_y2, $value));
		}
		else
		{
			return min($this->_y1, max($this->_y2, $value));
		}
	}
}