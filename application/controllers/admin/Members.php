<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Members extends CI_Controller
{
	public function all()
	{
		$list = $this->MemberModel->getAllMembers();
		return successReponse("", $list);
	}
	public function get()
	{
		$user = $this->MemberModel->getAdminInfoByIdLogin(decode($_POST['access_token']));
		$member = $this->MemberModel->getMemberDetailsByUserid($_POST['userid']);
		return successReponse("", $member);
	}
	public function update($userid)
	{
		$user = $this->MemberModel->getAdminInfoByIdLogin(decode($_POST['access_token']));
		$member = $this->MemberModel->getMemberDetailsByUserid($userid);
		$posts['f_name'] = $_POST['f_name'];
		$posts['l_name'] = $_POST['l_name'];
		$posts['email'] = $_POST['email'];
		$posts['mobile'] = $_POST['mobile'];
		$member = $this->MemberModel->updateMemberInfo($member['member_detail']['id'], $posts);
		return successReponse("", $member);
	}
}

