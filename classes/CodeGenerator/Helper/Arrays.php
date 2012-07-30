<?php
/**
 * Array functions helper class. Based on Kohana Framework Arr class.
 * 
 * @author     Kohana Team
 * @copyright  (c) 2007-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 * 
 * @package    CodeGenerator
 * @author     Korney Czukowski
 * @copyright  (c) 2012 Korney Czukowski
 * @license    MIT License
 */
namespace CodeGenerator\Helper;

class Arrays extends \CodeGenerator\Singleton
{
	/**
	 * Tests if an array is associative or not.
	 *
	 *     // Returns TRUE
	 *     $arrays->is_assoc(array('username' => 'john.doe'));
	 *
	 *     // Returns FALSE
	 *     $arrays->is_assoc('foo', 'bar');
	 *
	 * @param   array   array to check
	 * @return  boolean
	 */
	public function is_assoc(array $array)
	{
		// Keys of the array
		$keys = array_keys($array);

		// If the array keys of the keys match the keys, then the array must
		// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
		return array_keys($keys) !== $keys;
	}

	/**
	 * Merges one or more arrays recursively and preserves all keys.
	 * Note that this does not work the same as [array_merge_recursive](http://php.net/array_merge_recursive)!
	 *
	 *     $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
	 *     $mary = array('name' => 'mary', 'children' => array('jane'));
	 *
	 *     // John and Mary are married, merge them together
	 *     $john = $arrays->merge($john, $mary);
	 *
	 *     // The output of $john will now be:
	 *     array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))
	 *
	 * @param   array  initial array
	 * @param   array  array to merge
	 * @param   array  ...
	 * @return  array
	 */
	public function merge(array $a1, array $a2)
	{
		$result = array();
		for ($i = 0, $total = func_num_args(); $i < $total; $i++)
		{
			// Get the next array
			$arr = func_get_arg($i);

			// Is the array associative?
			$assoc = $this->is_assoc($arr);

			foreach ($arr as $key => $val)
			{
				if (isset($result[$key]))
				{
					if (is_array($val) AND is_array($result[$key]))
					{
						if ($this->is_assoc($val))
						{
							// Associative arrays are merged recursively
							$result[$key] = $this->merge($result[$key], $val);
						}
						else
						{
							// Find the values that are not already present
							$diff = array_diff($val, $result[$key]);

							// Indexed arrays are merged to prevent duplicates
							$result[$key] = array_merge($result[$key], $diff);
						}
					}
					else
					{
						if ($assoc)
						{
							// Associative values are replaced
							$result[$key] = $val;
						}
						elseif ( ! in_array($val, $result, TRUE))
						{
							// Indexed values are added only if they do not yet exist
							$result[] = $val;
						}
					}
				}
				else
				{
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;
	}
}