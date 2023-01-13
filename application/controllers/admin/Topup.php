<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Topup extends CI_Controller
{
	public function get()
	{
		unset($_POST['access_token']);
		$list = $this->TopupModel->getAllTopups();
		return successReponse("", $list);
	}

	public function action()
	{
		$order = $this->TopupModel->action($_POST['action'], $_POST['topup_id']);
		return successReponse("", $order);
	}
}

