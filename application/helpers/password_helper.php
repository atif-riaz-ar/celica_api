<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('encode')) {
	function encode($string)
	{
		return base64_encode(base64_encode(base64_encode(base64_encode($string . "__" . getFullDate() . "__" . time()))));
	}
}

if (!function_exists('decode')) {
	function decode($string)
	{
		$str = base64_decode(base64_decode(base64_decode(base64_decode($string))));
		return explode("__", $str)[0];
	}
}

if (!function_exists('generatePassword')) {
	function generatePassword($_pass, $_salt = '')
	{
		$data = array();
		$salt = (!$_salt) ? generateSalt() : $_salt;
		$data['password'] = hash('sha512', $salt . $_pass);
		$data['salt'] = $salt;
		return $data;
	}
}

if (!function_exists('generateSalt')) {
	function generateSalt()
	{
		$salt_array = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '+', '=', ']', '[', '{', '}', '|', ';', ':', ',', '.', '<', '>', '/', '?', '`', ' ');
		$val = '';
		$x = 0;
		$max = count($salt_array);
		$rand = mt_rand(35, floor($max -1));
		while ($x < $rand) {
			$val .= $salt_array[mt_rand(0, $max - 1)];
			$x++;
		}
		return hash('sha512', $val);
	}
}

if (!function_exists('makeSeed')) {
	function makeSeed()
	{
		list($usec, $sec) = explode(' ', microtime());
		return (float)$sec + ((float)$usec * 100000);
	}
}

if (!function_exists('randomString')) {
	function randomString($length)
	{
		$salt = generateSalt();
		return substr($salt, 0, $length);
	}
}

if (!function_exists('generateAccessToken')) {
	function generateAccessToken()
	{
		$salt = generateSalt();
		return substr($salt, 0, 25);
	}
}

if (!function_exists('getIPAddr')) {
	function getIPAddr()
	{
		$ip = "";
		if (isset($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			if ($ip != "") $ip .= ", ";
			$ip .= $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if (isset($_SERVER['REMOTE_ADDR'])) {
			if ($ip != "") $ip .= ", ";
			$ip .= $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}


