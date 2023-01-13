<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{
	public function all()
	{
		$list = $this->ProductModel->getAllProducts();
		return successReponse("", $list);
	}

	public function add()
	{
		unset($_POST['access_token']);
		$list = $this->ProductModel->update($_POST);
		return successReponse("", $list);
	}

	public function get($id)
	{
		unset($_POST['access_token']);
		$product = $this->ProductModel->get($id);
		return successReponse("", $product);
	}

	public function delete()
	{
		unset($_POST['access_token']);
		$product = $this->ProductModel->delete($_POST['id_to_delete']);
		return successReponse("", $product);
	}
}

