<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Binary extends CI_Controller
{
	public function index()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$ref = $this->MemberModel->getMemberInfoByUserid($_POST['userid']);
		if (isset($ref['id'])) {
			$update = array(
				"referral_placement" => $ref['id'],
				"referral_side" => $_POST['side'],
			);
			$this->MemberModel->updateMemberInfo($user['id'], $update);
			return successReponse("[[LABEL_UPDATED_SUCCESSFULLY]]", "");
		} else {
			return failedReponse("[[LABEL_INVALID_USERID]]", "");
		}
	}

	public function get()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$ref = $this->MemberModel->getMemberInfoByIdUseridEmail($user['referral_placement']);
		if (isset($ref['id'])) {
			return successReponse("", array(
				"userid" => $ref['userid'],
				"side" => $user['referral_side'],
			));
		} else {
			return successReponse("", array(
				"userid" => $user['userid'],
				"side" => $user['referral_side'],
			));
		}
	}

}
