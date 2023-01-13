<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bank extends CI_Controller
{
	public function index()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$return = $this->BankModel->getMemberBanks($user['id']);
		return successReponse("", $return);
	}

	public function form()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$return['banklist'] = $this->BankModel->getBankListByCountry($user['country']);
		$return['memberBank'] = $this->BankModel->getMemberBankById($_POST['bankid']);
		return successReponse("", $return);
	}

	public function update()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$_POST['member_id'] = $user['id'];
		$this->BankModel->updateMemberBank($_POST);
		return successReponse("[[LABEL_BANK_UPDATED_SUCCESSFULLY]]", array());
	}
}
