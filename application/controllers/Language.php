<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Language extends CI_Controller
{
	public function get($language)
	{
		$data['system'] = $this->SystemModel->getConstants();
		$data['lang'] = $this->LanguageModel->get($language);
		return successReponse('', $data);
	}
}
