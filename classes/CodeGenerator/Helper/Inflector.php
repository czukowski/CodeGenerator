<?php
/**
 * Inflector helper class. Based on Kohana Framework Inflector class.
 * 
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class Inflector extends \CodeGenerator\Singleton
{
	/**
	 * Makes a phrase camel case. Spaces and underscores will be removed.
	 * 
	 *     $str = $inflector->camelize('mother cat');     // "motherCat"
	 *     $str = $inflector->camelize('kittens in bed'); // "kittensInBed"
	 * 
	 * @param   string  $str  phrase to camelize
	 * @return  string
	 */
	public function camelize($str)
	{
		$str = 'x'.strtolower(trim($str));
		$str = ucwords(preg_replace('/[\s_]+/', ' ', $str));

		return substr(str_replace(' ', '', $str), 1);
	}

	/**
	 * Converts a camel case phrase into a spaced phrase.
	 * 
	 *     $str = $inflector->decamelize('houseCat');    // "house cat"
	 *     $str = $inflector->decamelize('kingAllyCat'); // "king ally cat"
	 * 
	 * @param   string  $str  phrase to camelize
	 * @param   string  $sep  word separator
	 * @return  string
	 */
	public function decamelize($str, $sep = ' ')
	{
		return strtolower(preg_replace('/([a-z])([A-Z])/', '$1'.$sep.'$2', trim($str)));
	}

	/**
	 * Makes a phrase underscored instead of spaced.
	 * 
	 *     $str = $inflector->underscore('five cats'); // "five_cats";
	 * 
	 * @param   string  $str  phrase to underscore
	 * @return  string
	 */
	public function underscore($str)
	{
		return preg_replace('/\s+/', '_', trim($str));
	}
}