<?php
/**
 * PHP Helpers by Murat Alabacak <alabacakm@gmail.com>
 * 
 * @author Murat Alabacak <alabacakm@gmail.com>
 * @link muratalabacak.com
 * @version 1.0.0
 * 
 * @method str_lower($string)
 * @method str_upper($string)
 * @method str_title($string) ## Title Case 
 * @method str_pascal($string) ## PascalCase 
 * @method str_camel($string) ## camelCase 
 * @method str_snake($string, $divider='_') ## snake_case 
 * @method str_kebab($string) ## snake-case-with-dash
 * @method str_slug($string, $utf8=true) 
 */

/**
 * String to Lowercase
 * 
 * @param string $string
 * @return string
 */
if( !function_exists('str_lower') )
{
	function str_lower(string $string)
	{
		return mb_strtolower($string);
	}
}

/**
 * String to Uppercase
 * 
 * @param string $string
 * @return string
 */
if( !function_exists('str_upper') )
{
	function str_upper(string $string)
	{
		return mb_strtoupper($string);
	}
}

/**
 * String to Ucwords
 * 
 * @param string $string
 * @return string
 */
if( !function_exists('str_title') )
{
	function str_title(string $string)
	{
		return ucwords(str_lower($string));
	}
}

/**
 * String to PascalCase
 * 
 * @param string $string
 * @return string
 */
if( !function_exists('str_pascal') )
{
	function str_pascal(string $string)
	{
		// Every non-alpha will be spaced
		$string = preg_replace('~[^a-z0-9]~i', ' ', $string);

		// Multiple uppers until -1 indexed will be downcased
		// EVERYWord > EveryWord
		$string = preg_replace_callback('~([A-Z]+)(.?)~', function($m) {
			$letters = $m[1]; 
			$next = $m[2];
			$split = str_split($letters);

			$left = substr($letters, 0, -1);
			$last = substr($letters, -1);

			// If the next char is an alpha
			if( preg_match('~[a-z]~i', $next) )
			{
				// Preserve the last uppercased letter and ucwords the letters
				return ' '. str_title($left).$last.$next;
			} else {
				return ' ' . str_title($letters).$next;
			}
		}, $string);

		// Solo last uppercased letter
		$string = preg_replace_callback('~[A-Z](\s|$)~ ', function($m){
			return trim(str_lower($m[0]));
		}, $string);

		// EveryWord > Every Word
		$string = preg_replace('~[A-Z]~', ' $0', $string);

		// Trim the spaces at each side & double to 1 space
		$string = preg_replace('~\s+~', ' ', trim($string));

		// Ucwords
		$string = preg_replace('~\s+~', '', str_title(str_lower($string)));

		return $string;
	}
}

/**
 * String to camelCase
 * 
 * @param string $string
 * @return string
 */
if( !function_exists('str_camel') )
{
	function str_camel(string $string)
	{
		// Just turn the first letter into lowercased
		// of the pascalized version
		return preg_replace_callback('~^[A-Z]~', function($m) {
			return str_lower($m[0]);
		}, str_pascal($string));
	}
}

/**
 * String to snake_case
 * 
 * @param string $string
 * @param string $divider Word divider?
 * @return string
 */
if( !function_exists('str_snake') )
{
	function str_snake(string $string, string $divider = '_')
	{
		// Just turn the first letters into '_lowercased'
		// of the pascalized version
		return trim(preg_replace_callback('~[A-Z]~', function($m) use($divider) {
			return $divider.str_lower($m[0]);
		}, str_pascal($string)), $divider);
	}
}

/**
 * String to kebab-case
 * 
 * @see str_snake
 * @return string
 */
if( !function_exists('str_kebab') )
{
	function str_kebab(string $string)
	{
		return str_snake($string, '-');
	}
}

/**
 * Slug generator
 * 
 * @see str_snake
 * @see str_kebab
 * 
 * @param string $string
 * @param bool $utf8 
 * @return string
 */
if( !function_exists('str_slug') )
{
	function str_slug(string $string, bool $utf8 = true)
	{
		return str_kebab($string);
	}
}
