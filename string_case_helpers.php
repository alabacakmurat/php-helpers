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
 * @method utf8_to_ascii($string) ## Türkçe Karakter > Turkce Karakter (closest ascii equivalents)
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
		// return mb_strtolower($string);
		return mb_convert_case($string, MB_CASE_LOWER);
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
		// return mb_strtoupper($string);
		return mb_convert_case($string, MB_CASE_UPPER);
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
		return mb_convert_case(str_lower($string), MB_CASE_TITLE);
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
		$string = preg_replace('~[^\pN\pL]+~u', ' ', $string);

		// Multiple uppers until -1 indexed will be downcased
		// EVERYWord > EveryWord
		// $string = preg_replace_callback('~([A-Z]+)(.?)~u', function($m) {
		$string = preg_replace_callback('~([\p{Lu}]+)(.?)~u', function($m) {
			$letters = $m[1]; 
			$next = $m[2];
			$split = str_split($letters);

			$left = substr($letters, 0, -1);
			$last = substr($letters, -1);

			// If the next char is an alpha
			// if( preg_match('~[a-z]~ui', $next) )
			if( preg_match('~[\pL]~u', $next) )
			{
				// Preserve the last uppercased letter and ucwords the letters
				return ' '. str_title($left).$last.$next;
			} else {
				return ' ' . str_title($letters).$next;
			}
		}, $string);

		// Solo last uppercased letter
		// $string = preg_replace_callback('~[A-Z](\s|$)~u', function($m){
		$string = preg_replace_callback('~[\p{Lu}](\s|$)~u', function($m){
			return trim(str_lower($m[0]));
		}, $string);

		// EveryWord > Every Word
		// $string = preg_replace('~[A-Z]~u', ' $0', $string);
		$string = preg_replace('~[\p{Lu}]~u', ' $0', $string);

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
		return preg_replace_callback('~^[\p{Lu}]~u', function($m) {
			return str_lower($m[0]);
		}, str_pascal($string));
	}
}

/**
 * String to snake_case
 * 
 * @param string $string
 * @param string $divider Word divider?
 * @param bool $split_numbers Split numbers from the words? 'some123 > some_123'
 * @return string
 */
if( !function_exists('str_snake') )
{
	function str_snake(string $string, string $divider = '_', bool $split_numbers = false)
	{
		// Just turn the first letters into '_lowercased'
		// of the pascalized version
		$pascal = str_pascal($string);

		// And split the numbers as well
		if( $split_numbers === true )
			$pascal = preg_replace('/(\pN)+/u', $divider.'$0', $pascal);

		return trim(preg_replace_callback('~[\p{Lu}]~u', function($m) use($divider) {
			return $divider.str_lower($m[0]);
		}, $pascal), $divider);
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
	function str_kebab(string $string, ...$opts)
	{
		return str_snake($string, '-', ...$opts);
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
	function str_slug(string $string, bool $utf8 = false)
	{
		// Convert symbols
		$string = preg_replace_callback('/\p{Sc}/u', function($m) {
			return strtr($m[0], [
				'€' => 'Euro',
				'$' => 'USD',
				'₺' => 'TRY',
			]);
		}, $string);

		if( $utf8 )
			return str_kebab($string, true);
		else
			return utf8_to_ascii(str_kebab($string, true));
	}
}


/**
 * UTF-8 to ASCII-only
 * 
 * @return string
 */
if( !function_exists('utf8_to_ascii') )
{
	function utf8_to_ascii(string $string)
	{
		$defaults = [
		    "°" => "0",
		    "¹" => "1",
		    "²" => "2",
		    "³" => "3",
		    "⁴" => "4",
		    "⁵" => "5",
		    "⁶" => "6",
		    "⁷" => "7",
		    "⁸" => "8",
		    "⁹" => "9",

		    "₀" => "0",
		    "₁" => "1",
		    "₂" => "2",
		    "₃" => "3",
		    "₄" => "4",
		    "₅" => "5",
		    "₆" => "6",
		    "₇" => "7",
		    "₈" => "8",
		    "₉" => "9",


		    "æ" => "ae",
		    "ǽ" => "ae",
		    "À" => "A",
		    "Á" => "A",
		    "Â" => "A",
		    "Ã" => "A",
		    "Å" => "AA",
		    "Ǻ" => "A",
		    "Ă" => "A",
		    "Ǎ" => "A",
		    "Æ" => "AE",
		    "Ǽ" => "AE",
		    "à" => "a",
		    "á" => "a",
		    "â" => "a",
		    "ã" => "a",
		    "å" => "aa",
		    "ǻ" => "a",
		    "ă" => "a",
		    "ǎ" => "a",
		    "ª" => "a",
		    "@" => "at",
		    "Ĉ" => "C",
		    "Ċ" => "C",
		    "Ç" => "C",
		    "ç" => "c",
		    "ĉ" => "c",
		    "ċ" => "c",
		    "©" => "c",
		    "Ð" => "Dj",
		    "Đ" => "D",
		    "ð" => "dj",
		    "đ" => "d",
		    "È" => "E",
		    "É" => "E",
		    "Ê" => "E",
		    "Ë" => "E",
		    "Ĕ" => "E",
		    "Ė" => "E",
		    "è" => "e",
		    "é" => "e",
		    "ê" => "e",
		    "ë" => "e",
		    "ĕ" => "e",
		    "ė" => "e",
		    "ƒ" => "f",
		    "Ĝ" => "G",
		    "Ġ" => "G",
		    "ĝ" => "g",
		    "ġ" => "g",
		    "Ĥ" => "H",
		    "Ħ" => "H",
		    "ĥ" => "h",
		    "ħ" => "h",
		    "Ì" => "I",
		    "Í" => "I",
		    "Î" => "I",
		    "Ï" => "I",
		    "Ĩ" => "I",
		    "Ĭ" => "I",
		    "Ǐ" => "I",
		    "Į" => "I",
		    "Ĳ" => "IJ",
		    "ì" => "i",
		    "í" => "i",
		    "î" => "i",
		    "ï" => "i",
		    "ĩ" => "i",
		    "ĭ" => "i",
		    "ǐ" => "i",
		    "į" => "i",
		    "ĳ" => "ij",
		    "Ĵ" => "J",
		    "ĵ" => "j",
		    "Ĺ" => "L",
		    "Ľ" => "L",
		    "Ŀ" => "L",
		    "ĺ" => "l",
		    "ľ" => "l",
		    "ŀ" => "l",
		    "Ñ" => "N",
		    "ñ" => "n",
		    "ŉ" => "n",
		    "Ò" => "O",
		    "Ó" => "O",
		    "Ô" => "O",
		    "Õ" => "O",
		    "Ō" => "O",
		    "Ŏ" => "O",
		    "Ǒ" => "O",
		    "Ő" => "O",
		    "Ơ" => "O",
		    "Ø" => "OE",
		    "Ǿ" => "O",
		    "Œ" => "OE",
		    "ò" => "o",
		    "ó" => "o",
		    "ô" => "o",
		    "õ" => "o",
		    "ō" => "o",
		    "ŏ" => "o",
		    "ǒ" => "o",
		    "ő" => "o",
		    "ơ" => "o",
		    "ø" => "oe",
		    "ǿ" => "o",
		    "º" => "o",
		    "œ" => "oe",
		    "Ŕ" => "R",
		    "Ŗ" => "R",
		    "ŕ" => "r",
		    "ŗ" => "r",
		    "Ŝ" => "S",
		    "Ș" => "S",
		    "ŝ" => "s",
		    "ș" => "s",
		    "ſ" => "s",
		    "Ţ" => "T",
		    "Ț" => "T",
		    "Ŧ" => "T",
		    "Þ" => "TH",
		    "ţ" => "t",
		    "ț" => "t",
		    "ŧ" => "t",
		    "þ" => "th",
		    "Ù" => "U",
		    "Ú" => "U",
		    "Û" => "U",
		    "Ü" => "U",
		    "Ũ" => "U",
		    "Ŭ" => "U",
		    "Ű" => "U",
		    "Ų" => "U",
		    "Ư" => "U",
		    "Ǔ" => "U",
		    "Ǖ" => "U",
		    "Ǘ" => "U",
		    "Ǚ" => "U",
		    "Ǜ" => "U",
		    "ù" => "u",
		    "ú" => "u",
		    "û" => "u",
		    "ü" => "u",
		    "ũ" => "u",
		    "ŭ" => "u",
		    "ű" => "u",
		    "ų" => "u",
		    "ư" => "u",
		    "ǔ" => "u",
		    "ǖ" => "u",
		    "ǘ" => "u",
		    "ǚ" => "u",
		    "ǜ" => "u",
		    "Ŵ" => "W",
		    "ŵ" => "w",
		    "Ý" => "Y",
		    "Ÿ" => "Y",
		    "Ŷ" => "Y",
		    "ý" => "y",
		    "ÿ" => "y",
		    "ŷ" => "y",

		    "ş" => "s",
		    "Ş" => "S",

		    "Ç"	=> "C",
		    "ç"	=> "c",

		    "Ö"	=> "O",
		    "ö"	=> "o",

		    "Ğ"	=> "G",
		    "ğ"	=> "g",

		    "İ"	=> "I",
		    "ı"	=> "i",
		];

		return strtr($string, $defaults);
	}
}
