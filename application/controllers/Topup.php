<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Topup extends CI_Controller
{
	public function index()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$return['banklist'] = $this->BankModel->getCompanyBanks($user['full_name']);
		$return['currencies'] = $this->CurrencyModel->get();
		return successReponse("", $return);
	}

	public function process()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		return $this->TopupModel->processTopup($_POST, $user);
	}
}
