<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart extends CI_Controller
{
	public function get()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$data['products'] = $this->CartModel->getCart($user);
		$data['gateways'] = $this->PaymentModel->getGateways("ACTIVE");
		return successReponse("", $data);
	}

	public function update_cart()
	{
		$cart['user'] = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$cart['id'] = $cart['product_id'] = $_POST['product_id'];
		$product = $this->ProductModel->getProduct($cart);
		$cart['quantity'] = $_POST['quantity'];
		$return = $this->CartModel->updateCart($cart);
		if ($return) {
			if ($cart['quantity'] == 0) {
				$summary = $product['name'] . "[[LABEL_REMOVED FROM CART]]";
			} else {
				$summary = $product['name'] . " x " . $cart['quantity'] . " " . trans("[[LABEL_CART_UPDATED]]", $_POST['language']);
			}
			return successReponse(trans("[[LABEL_CART_UPDATED_SUCCSSFULLY]]", $_POST['language']), array(
				"notify_bg" => "bg-success",
				"notify_title" => trans("[[LABEL_CART_UPDATED_SUCCSSFULLY]]", $_POST['language']),
				"notify_time" => getFullDate(),
				"notify_subtitle" => $product['name'],
				"notify_summary" => $summary
			));
		} else {
			$summary = $product['name'] . trans("[[LABEL_FAILED_TRY_AGAIN]]", $_POST['language']);
			return successReponse(trans("[[LABEL_FAILED_TO_UPDATE_CART]]", $_POST['language']), array(
				"notify_bg" => "bg-warning",
				"notify_title" => trans("[[LABEL_FAILED_TO_UPDATE_CART]]", $_POST['language']),
				"notify_time" => getFullDate(),
				"notify_subtitle" => "",
				"notify_summary" => $summary
			));
		}
	}

	public function add_to_cart()
	{
		$cart['user'] = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$p['id'] = $_POST['product_id'];
		$product = $this->ProductModel->getProduct($p);
		$cart['product_id'] = $_POST['product_id'];
		$cart['quantity'] = $_POST['quantity'];
		$return = $this->CartModel->addToCart($cart);
		if ($return) {
			$summary = $product['name'] . " x " . $cart['quantity'] . " " . trans("[[LABEL_ADDED_TO_CARD]]", $_POST['language']) .
				" <a href='" . MEMBER_URL . "cart' class='text-white text-decoration-underline'>" . trans("[[LABEL_GOTO_CART]]", $_POST['language']) . "</a>";
			return successReponse(trans("[[LABEL_ADDED_SUCCSSFULLY]]", $_POST['language']), array(
				"notify_bg" => "bg-success",
				"notify_title" => trans("[[LABEL_ADDED_SUCCSSFULLY]]", $_POST['language']),
				"notify_time" => getFullDate(),
				"notify_subtitle" => $product['name'],
				"notify_summary" => $summary
			));
		} else {
			$summary = $product['name'] . trans("[[LABEL_FAILED_TRY_AGAIN]]", $_POST['language']);
			return successReponse(trans("[[LABEL_FAILED_TO_ADD]]", $_POST['language']), array(
				"notify_bg" => "bg-warning",
				"notify_title" => trans("[[LABEL_FAILED_TO_ADD]]", $_POST['language']),
				"notify_time" => getFullDate(),
				"notify_subtitle" => "",
				"notify_summary" => $summary
			));
		}
	}
}
