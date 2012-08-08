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
	 * @var  string  default delimiter for path()
	 */
	private $delimiter = '.';

	/**
	 * Test if a value is an array with an additional check for array-like objects.
	 * 
	 *     // Returns TRUE
	 *     $arrays->is_array(array());
	 *     $arrays->is_array(new ArrayObject);
	 * 
	 *     // Returns FALSE
	 *     $arrays->is_array(FALSE);
	 *     $arrays->is_array('not an array!');
	 *     $arrays->is_array(Database::instance());
	 * 
	 * @param   mixed   $value  value to check
	 * @return  boolean
	 */
	public function is_array($value)
	{
		if (is_array($value))
		{
			// Definitely an array
			return TRUE;
		}
		else
		{
			// Possibly a Traversable object, functionally the same as an array
			return (is_object($value) AND $value instanceof \Traversable);
		}
	}

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

	/**
	 * Gets a value from an array using a dot separated path.
	 * 
	 *     // Get the value of $array['foo']['bar']
	 *     $value = $arrays->path($array, 'foo.bar');
	 * 
	 * Using a wildcard "*" will search intermediate arrays and return an array.
	 * 
	 *     // Get the values of "color" in theme
	 *     $colors = $arrays->path($array, 'theme.*.color');
	 * 
	 *     // Using an array of keys
	 *     $colors = $arrays->path($array, array('theme', '*', 'color'));
	 * 
	 * @param   array   $array      array to search
	 * @param   mixed   $path       key path string (delimiter separated) or array of keys
	 * @param   mixed   $default    default value if the path is not set
	 * @param   string  $delimiter  key path delimiter
	 * @return  mixed
	 */
	public function path($array, $path, $default = NULL, $delimiter = NULL)
	{
		if ( ! $this->is_array($array))
		{
			// This is not an array!
			return $default;
		}

		if (is_array($path))
		{
			// The path has already been separated into keys
			$keys = $path;
		}
		else
		{
			if (array_key_exists($path, $array))
			{
				// No need to do extra processing
				return $array[$path];
			}

			if ($delimiter === NULL)
			{
				// Use the default delimiter
				$delimiter = $this->delimiter;
			}

			// Remove starting delimiters and spaces
			$path = ltrim($path, "{$delimiter} ");

			// Remove ending delimiters, spaces, and wildcards
			$path = rtrim($path, "{$delimiter} *");

			// Split the keys by delimiter
			$keys = explode($delimiter, $path);
		}

		do
		{
			$key = array_shift($keys);

			if (ctype_digit($key))
			{
				// Make the key an integer
				$key = (int) $key;
			}

			if (isset($array[$key]))
			{
				if ($keys)
				{
					if ($this->is_array($array[$key]))
					{
						// Dig down into the next part of the path
						$array = $array[$key];
					}
					else
					{
						// Unable to dig deeper
						break;
					}
				}
				else
				{
					// Found the path requested
					return $array[$key];
				}
			}
			elseif ($key === '*')
			{
				// Handle wildcards
				$values = array();
				foreach ($array as $arr)
				{
					if (($value = $this->path($arr, implode('.', $keys))))
					{
						$values[] = $value;
					}
				}

				if ($values)
				{
					// Found the values requested
					return $values;
				}
				else
				{
					// Unable to dig deeper
					break;
				}
			}
			else
			{
				// Unable to dig deeper
				break;
			}
		}
		while ($keys);

		// Unable to find the value requested
		return $default;
	}
}