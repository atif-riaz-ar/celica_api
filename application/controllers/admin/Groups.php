<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Groups extends CI_Controller
{
	public function all()
	{
		$list = $this->GroupModel->getAllGroups();
		return successReponse("", $list);
	}

	public function add()
	{
		unset($_POST['access_token']);
		$list = $this->GroupModel->update($_POST);
		return successReponse("", $list);
	}

	public function get($id)
	{
		unset($_POST['access_token']);
		$group = $this->GroupModel->get($id);
		return successReponse("", $group);
	}

	public function delete()
	{
		unset($_POST['access_token']);
		$group = $this->GroupModel->delete($_POST['id_to_delete']);
		return successReponse("", $group);
	}
}

