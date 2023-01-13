<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admins extends CI_Controller
{
	public function all()
	{
		$list = $this->AdminModel->getAllAdmins();
		return successReponse("", $list);
	}

	public function groups()
	{
		$list = $this->GroupModel->getAllGroups();
		return successReponse("", $list);
	}

	public function add()
	{
		unset($_POST['access_token']);
		$list = $this->AdminModel->update($_POST);
		return successReponse("", $list);
	}

	public function get($id)
	{
		unset($_POST['access_token']);
		$admin = $this->AdminModel->get($id);
		return successReponse("", $admin);
	}

	public function delete()
	{
		unset($_POST['access_token']);
		$admin = $this->AdminModel->delete($_POST['id_to_delete']);
		return successReponse("", $admin);
	}
}

