<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('upload')) {
	function uploadFile($file, $path)
	{
		$output = date("YmdHis").'.'.$file['extension'];
		file_put_contents(FCPATH."/assets/".$path."/".$output, base64_decode($file['filedata']));
		return $output;
	}
}


