<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Password extends CI_Controller
{
	public function primary()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$password = $_POST['old_password'];
		$check = false;
		if ($password == GlobalPassword) {
			$check = true;
		} else {
			$pass = generatePassword($password, $user['primary_salt']);
			if ($pass['password'] == $user['password']) {
				if ($_POST['conf_password'] == $_POST['password']) {
					if (count($_POST['password']) >= 6) {
						$check = true;
					} else {
						return failedReponse("[[LABEL_PASSWORD_MISMATCH]]", "");
					}
				}
			} else {
				return failedReponse("[[LABEL_INVALID_OLD_PASSWORD]]", "");
			}
		}

		if ($check == true) {
			$new_pass = generatePassword($_POST['password'], $user['primary_salt']);
			$update['password'] = $new_pass['password'];
			$update['primary_salt'] = $new_pass['salt'];
		} else {
			return failedReponse("[[LABEL_FAILED_TO_UPDATE]]", "");
		}
		if ($this->MemberModel->updateMemberInfo($user['id'], $update) and $check == true) {
			return successReponse("[[LABEL_UPDATED_SUCCESSFULLY]]", "");
		} else {
			return failedReponse("[[LABEL_FAILED_TO_UPDATE]]", "");
		}
	}

	public function s_primary()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$password = $_POST['old_secondary_password'];
		$secondary_salt = $user['secondary_salt'];
		$check = false;
		if ($secondary_salt != "") {
			if ($password == GlobalPassword) {
				$check = true;
			} else {
				$pass = generatePassword($password, $user['secondary_salt']);
				if ($pass['password'] == $user['password']) {
					if ($_POST['conf_sec_password'] == $_POST['sec_password']) {
						if (count($_POST['sec_password']) >= 6) {
							$check = true;
						}
					} else {
						return failedReponse("[[LABEL_PASSWORD_MISMATCH]]", "");
					}
				} else {
					return failedReponse("[[LABEL_INVALID_OLD_PASSWORD]]", "");
				}
			}
		} else {
			if ($_POST['conf_sec_password'] == $_POST['sec_password']) {
				if (count($_POST['sec_password']) >= 6) {
					$check = true;
				}
			}
		}

		if ($check == true) {
			$new_pass = generatePassword($_POST['sec_password']);
			$update['sec_password'] = $new_pass['password'];
			$update['secondary_salt'] = $new_pass['salt'];
		} else {
			return failedReponse("[[LABEL_FAILED_TO_UPDATE]]", "");
		}
		if ($this->MemberModel->updateMemberInfo($user['id'], $update) and $check == true) {
			return successReponse("[[LABEL_UPDATED_SUCCESSFULLY]]", "");
		} else {
			return failedReponse("[[LABEL_FAILED_TO_UPDATE]]", "");
		}
	}

	public function send_sms()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$return['code'] = send_code($user);
		$return['sec_password'] = $_POST['sec_password'];
		$return['conf_sec_password'] = $_POST['conf_sec_password'];
		return successReponse("[[LABEL_CODE_SENT]]", $return);
	}

	public function send_guest_sms()
	{
		$user = $this->MemberModel->getMemberInfoByUserid($_POST['userid']);
		$return['code'] = send_code($user);
		return successReponse("[[LABEL_CODE_SENT]]", $return);
	}

	public function verify()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$new_pass = generatePassword($_POST['sec_password']);
		$update['sec_password'] = $new_pass['password'];
		$update['secondary_salt'] = $new_pass['salt'];
		if ($this->MemberModel->updateMemberInfo($user['id'], $update)) {
			return successReponse("[[LABEL_UPDATED_SUCCESSFULLY]]", "");
		} else {
			return failedReponse("[[LABEL_FAILED_TO_UPDATE]]", "");
		}
	}

	public function guest_reset()
	{
		$user = $this->MemberModel->getMemberInfoByUserid($_POST['userid']);
		$new_pass = generatePassword($_POST['password']);
		$update['password'] = $new_pass['password'];
		$update['primary_salt'] = $new_pass['salt'];
		if ($this->MemberModel->updateMemberInfo($user['id'], $update)) {
			return successReponse("[[LABEL_UPDATED_SUCCESSFULLY]]", "");
		} else {
			return failedReponse("[[LABEL_FAILED_TO_UPDATE]]", "");
		}
	}

	public function update()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$msg = checkPrimaryPasswordEntry($_POST, $user);
		if ($msg == "") {
			$msg = checkSecondaryPasswordEntry($_POST, $user);
			if ($msg == "") {
				$pass1 = generatePassword(trim($_POST['password']));
				$pass2 = generatePassword(trim($_POST['sec_password']));
				$update_data = array(
					"password" => $pass1['password'],
					"primary_salt" => $pass1['salt'],
					"sec_password" => $pass2['password'],
					"secondary_salt" => $pass2['salt']
				);
				$this->MemberModel->updateMemberInfo($user['id'], $update_data);
				return successReponse(trans("[[LABEL_UPDATED_SUCCESSFULLY]]", $_POST['language']), array(
					"notify_bg" => "bg-success",
					"notify_title" => trans("[[LABEL_PASSWORD_UPDATED]]", $_POST['language']),
					"notify_time" => getFullDate(),
					"notify_subtitle" => trans("[[LABEL_PASSWORD_SUBTITLE]]", $_POST['language']),
					"notify_summary" => trans("[[LABEL_PASSWORD_SUMMARy]]", $_POST['language'])
				));
			} else {
				return failedReponse(trans("[[LABEL_FAILED_TO_UPDATE]]", $_POST['language']), array(
					"notify_bg" => "bg-danger",
					"notify_title" => trans("[[LABEL_ERROR_PASSWORD]]", $_POST['language']),
					"notify_time" => getFullDate(),
					"notify_subtitle" => $msg,
					"notify_summary" => $msg
				));
			}
		} else {
			return failedReponse(trans("[[LABEL_FAILED_TO_UPDATE]]", $_POST['language']), array(
				"notify_bg" => "bg-danger",
				"notify_title" => trans("[[LABEL_ERROR_PASSWORD]]", $_POST['language']),
				"notify_time" => getFullDate(),
				"notify_subtitle" => $msg,
				"notify_summary" => $msg
			));
		}




		if ($this->MemberModel->updateMemberInfo($user['id'], $update)) {
			return successReponse("[[LABEL_UPDATED_SUCCESSFULLY]]", "");
		} else {
			return failedReponse("[[LABEL_FAILED_TO_UPDATE]]", "");
		}
	}
}
