<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends CI_Controller
{
	public function get()
	{
		$list = $this->OrderModel->getAllOrders();
		return successReponse("", $list);
	}

	public function details()
	{
		$order = $this->OrderModel->details($_POST['order_id']);
		return successReponse("", $order);
	}

	public function action()
	{
		$order = $this->OrderModel->action($_POST['action'], $_POST['order_id']);
		return successReponse("", $order);
	}
}

