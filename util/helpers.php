<?php
if (! function_exists('value')) {
	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
  function value($value) 
  {
		return $value instanceof Closure ? $value() : $value;
	}
}

if (! function_exists('env')) {
	/**
	 * Gets the value of an environment variable. Supports boolean, empty and null.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
  function env($key, $default = null)	
  {
		if (defined($key)) {
      return constant($key);
    }
    
    $value = getenv($key);

		if ($value === false) return value($default);

		switch (strtolower($value))
		{
			case 'true':
			case '(true)':
				return true;

			case 'false':
			case '(false)':
				return false;

			case 'null':
			case '(null)':
				return null;

			case 'empty':
			case '(empty)':
				return '';
		}

		return $value;
	}
}

if (! function_exists('array_get')) {
  /**
	 * Get an item from an array using "dot" notation.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
  function array_get($array, $key, $default = null) 
  {  
    if (is_null($key)) return $array;

		if (isset($array[$key])) return $array[$key];

		foreach (explode('.', $key) as $segment)
		{
			if (! is_array($array) || ! array_key_exists($segment, $array))
			{
				return value($default);
			}

			$array = $array[$segment];
		}

		return $array;
  }
}