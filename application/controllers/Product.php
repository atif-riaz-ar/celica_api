<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends CI_Controller
{
	public function index()
	{
		$report = $this->ProductModel->getProduct($_POST);
		return successReponse("", $report);
	}
}
