<?php
/**
 * Code Format config class
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator;

class Format
{
	private $config = array(
		'format' => array(
			'column_delimiter' => ' ',
			'indent' => "\t",
		),
		'options' => array(
			'column_min_space' => 2,
			'line_width' => 100,
			'wrap_comment_text' => TRUE,
		),
	);

	public function __construct($config = array())
	{
		$this->config = array_merge_recursive($this->config, $config);
	}

	public function format()
	{
		return $this->_get_set('format', func_get_args());
	}

	public function options()
	{
		return $this->_get_set('options', func_get_args());
	}

	private function _get_set($item, array $arguments)
	{
		$count = count($arguments);
		if ($count === 0)
		{
			return $this->config[$item];
		}
		elseif ($count === 1 AND array_key_exists($arguments[0], $this->config[$item]))
		{
			return $this->config[$item][$arguments[0]];
		}
		elseif ($count === 2 AND array_key_exists($arguments[0], $this->config[$item]))
		{
			$this->config[$item][$arguments[0]] = $arguments[1];
			return $this;
		}
		elseif ($count > 2)
		{
			throw new \InvalidArgumentException('Format.'.ucfirst($item).'() takes one or two arguments.');
		}
		throw new \InvalidArgumentException('Format.'.$item.'.'.$arguments[0].' does not exist');
	}

}