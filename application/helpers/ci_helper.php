<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('ci')) {
	function ci()
	{
		$CI =& get_instance();
		return $CI;
	}
}

if (!function_exists('dd')) {
	function dd($var)
	{
		echo "<pre>";
		print_r($var);
		exit;
	}
}
