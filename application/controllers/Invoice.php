<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends CI_Controller
{
	public function index()
	{
		$report = $this->InvoiceModel->getInvoices($_POST);
		return successReponse("", $report);
	}
}
