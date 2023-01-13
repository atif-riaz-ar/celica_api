<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
	public function get()
	{
		$admin_id = decode($_POST['access_token']);
		$menus = $this->MenuModel->getMenus($admin_id);
		return successReponse("", $menus);
	}

	public function permission($id)
	{
		$vals = implode(",",array_values($_POST));
		$menus = $this->MenuModel->update($vals, $id);
		return successReponse("", $menus);
	}
}
