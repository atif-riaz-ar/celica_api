<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tags extends CI_Controller
{
	public function all()
	{
		$list = $this->LanguageModel->getAll();
		return successReponse("", $list);
	}

	public function update()
	{
		$list = $this->LanguageModel->update($_POST);
		return successReponse("", $list);
	}
}

