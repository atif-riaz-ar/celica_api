<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transfer extends CI_Controller
{
	public function index()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$rc_balance = $this->LedgerModel->getBalance("RC", $user['id']);
		if(isset($_POST['txtUserID'])){
			$return['member'] = $this->MemberModel->getMemberInfoByIdUseridEmail($_POST['txtUserID']);
		}
		$return['currencies'] = $this->CurrencyModel->get();
		$return['CCs']['credit'] = $rc_balance['cr'];
		$return['CCs']['debit'] = $rc_balance['dr'];
		$return['CCs']['balance'] = $rc_balance['cr'] - $rc_balance['dr'];
		return successReponse("", $return);
	}

	public function history()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$return = $this->LedgerModel->getTransferHistory($user['id']);
		return successReponse("", $return);
	}

	public function process()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		return $this->TransferModel->processTransfer($_POST, $user);
	}
}
