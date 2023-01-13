<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Currencies extends CI_Controller
{

	public function get()
	{
		$report = $this->CurrencyModel->get();
		return successReponse("", $report);
	}
}
