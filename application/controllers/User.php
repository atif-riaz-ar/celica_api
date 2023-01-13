<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
	public function info()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		return successReponse("", $user);
	}

	public function get($userid)
	{
		$logged_in = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$to_switch = $this->MemberModel->getMemberInfoByIdUseridEmail($userid);
		$mainAccountId = $logged_in['main_acct_id'] > 0 ? $logged_in['main_acct_id'] : $logged_in['id'];
		$subAccounts = $this->MemberModel->getSubAccounts($mainAccountId);
		$found = false;

		if ($to_switch['id'] == $mainAccountId) {
			$found = true;
		} else {
			foreach ($subAccounts as $account) {
				if ($account['id'] == $to_switch['id']) {
					$found = true;
				}
			}
		}
		if ($found == true) {
			return successReponse("[[LABEL_USER_SWITCHED_SUCCESSFULLY]]", $to_switch);
		} else {
			return failedRepnonse("[[LABEL_USER_NOT_IN_NETWOKR]]", array());
		}
	}
}
