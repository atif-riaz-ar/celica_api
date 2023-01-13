<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Banks extends CI_Controller
{
	public function all()
	{
		$list = $this->BankModel->getCompBanks();
		return successReponse("", $list);
	}

	public function update()
	{
		$list = $this->BankModel->updateCompanyBank($_POST);
		return successReponse("", $list);
	}

	public function get($id)
	{
		unset($_POST['access_token']);
		$product = $this->BankModel->get($id);
		return successReponse("", $product);
	}

	public function getBankList()
	{
		$bank = $this->BankModel->getBankList();
		return successReponse("", $bank);
	}

	public function delete()
	{
		unset($_POST['access_token']);
		$product = $this->BankModel->delete($_POST['id_to_delete']);
		return successReponse("", $product);
	}
}

