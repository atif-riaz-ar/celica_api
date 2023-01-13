<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
{
	public function all()
	{
		$list = $this->SettingModel->getAllSettings();
		return successReponse("", $list);
	}

	public function add()
	{
		$list = $this->SettingModel->update($_POST);
		return successReponse("", $list);
	}
}

