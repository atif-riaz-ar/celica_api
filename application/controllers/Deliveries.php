<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Deliveries extends CI_Controller
{
	public function index()
	{
		$report = $this->DeliveriesModel->getOrders($_POST);
		return successReponse("", $report);
	}
}
