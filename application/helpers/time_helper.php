<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('getMonth')) {
	function getMonth()
	{
		return date("Y-m");
	}
}

if (!function_exists('getDate')) {
	function getDate()
	{
		return date("Y-m-d");
	}
}

if (!function_exists('getFullDate')) {
	function getFullDate()
	{
		return date("Y-m-d H:i:s");
	}
}

if (!function_exists('calcMonth')) {
	function calcMonth($format, $interval, $month)
	{
		return date($format, strtotime($interval." month", strtotime($month)));
	}
}

if (!function_exists('withdrawalDate')) {
	function withdrawalDate()
	{
		if (date('d') <= 15)
			return date('Y-m-20');
		else
			return date('Y-m-05', strtotime("+1 month", strtotime(date("Y-m-d"))));
	}
}

if (!function_exists('getTransId')) {
	function getTransId()
	{
		return date("mdHis");
	}
}


