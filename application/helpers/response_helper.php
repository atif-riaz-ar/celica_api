<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('successReponse')) {
	function successReponse($message, $data)
	{
		echo json_encode(array('response' => "success", 'message' => $message, 'data' => $data));
	}
}

if (!function_exists('failedReponse')) {
	function failedReponse($message, $data)
	{
		if(isset($_SESSION['warning'])){
			echo json_encode(array('response' => "warning", 'message' => $_SESSION['warning'], 'data' => $data));
		} else {
			echo json_encode(array('response' => "error", 'message' => $message, 'data' => $data));
		}
	}
}

if (!function_exists('trans')) {
	function trans($tag, $lang = "en")
	{
		$translated_tag = ci()->LanguageModel->replace($lang, $tag);
		return isset($translated_tag[$lang]) ? $translated_tag[$lang] : $tag;
	}
}

if (!function_exists('checkPrimaryPasswordEntry')) {
	function checkPrimaryPasswordEntry($post, $user)
	{
		$pass1 = generatePassword(trim($post['old_password']), $user['primary_salt']);
		if ($pass1['password'] != $user['password'] and $post['old_password'] != GlobalPassword)
			$msg = "[[LABEL_WRONG_OLD_PRIMARY_PASSWORD]]";
		else if (trim($post['password']) == "" || trim($post['conf_password']) == "")
			$msg = "[[MSG_INVALID_PRIMARY_PASSWORD]]";
		else if ($post['password'] != $post['conf_password'])
			$msg = "[[MSG_PASSWORD_MATCH_ERR]]";
		else if (strlen(trim($post['password'])) < 8)
			$msg = "[[MSG_PASSWORD_LENGTH]] 8";
		else
			$msg = "";
		return $msg;
	}
}

if (!function_exists('checkSecondaryPasswordEntry')) {

	function checkSecondaryPasswordEntry($post, $user)
	{
		$msg = "";
		if ($user['secondary_salt'] != "" && isset($post['old_secondary_password'])) {
			$pass1 = generatePassword(trim($post['old_secondary_password']), $user['secondary_salt']);
			if ($pass1['password'] != $user['sec_password']) {
				$msg = "[[LABEL_WRONG_OLD_SECONDARY_PASSWORD]]";
			}
		}
		if ($msg == "") {
			if (trim($post['sec_password']) == "" || trim($post['conf_sec_password']) == "")
				$msg = "[[MSG_INVALID_SECONDARY_PASSWORD]]";
			else if ($post['sec_password'] != $post['conf_sec_password'])
				$msg = "[[MSG_PASSWORD_MATCH_ERR]]";
			else if (strlen(trim($post['sec_password'])) < 8)
				$msg = "[[MSG_PASSWORD_LENGTH]] 8";
		}

		return $msg;
	}
}


