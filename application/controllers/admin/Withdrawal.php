<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Withdrawal extends CI_Controller
{
	public function get()
	{
		unset($_POST['access_token']);
		$list = $this->WithdrawalModel->getAllWithdrawals();
		return successReponse("", $list);
	}

	public function action()
	{
		$order = $this->WithdrawalModel->action($_POST['action'], $_POST['withdrawal_id']);
		return successReponse("", $order);
	}
}

