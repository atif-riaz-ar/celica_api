<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PackagePurchaseWalletModel extends CI_Model
{
	public function purchaseProduct($post)
	{
		$signup_data = json_decode($post['signup_data'], true);
		$p['id'] = $signup_data['package_id'];
		$product = $this->ProductModel->getPackages($p);
		$errors = array();
		$cart_total = $product['price'];
		$cc_balance = $this->LedgerModel->getBalance("RC", $signup_data['id']);
		$available_balance = $cc_balance['cr'] - $cc_balance['dr'];
		$trans_time = getFullDate();
		$period = getMonth();
		if ($cart_total <= $available_balance) {
			$new_user = $this->SignupModel->createUser($signup_data);
			$trans_id = getTransId();
			$ledger = array(
				"currency" => "CC",
				"member_id" => $signup_data['id'],
				"period" => $period,
				"trans_source_type" => 'ORDER',
				"trans_id" => $trans_id,
				"trans_no" => $trans_id,
				"description" => "[[PACKAGE_BUY_FOR]] " . $new_user['userid'],
				"debit" => $cart_total,
				"credit" => 0,
				"balance" => ($available_balance - $cart_total),
				"insert_time" => $trans_time,
				"insert_by" => $signup_data['user']
			);
			$payment = $this->LedgerModel->insertLedger($ledger);
		} else {
			$errors[] = "[[LABEL_INSUFFICIENT_BALANCE]]";
		}
		if ($payment and count($errors) < 1) {
			$master = array(
				'period' => $period,
				'order_num' => $trans_id,
				'invoice_no' => $trans_id,
				'customer_type' => "MEMBER",
				'order_type' => "PACKAGE",
				'member_id' => $new_user['id'],
				'description' => '[[DEF_PURCHASE_ITEMS]]',
				'currency' => '[[USD]]',
				'total_amount' => $cart_total,
				'subtotal' => $cart_total,
				'discount' => 0,
				'gst' => 0,
				'shipment_cost' => 0,
				'total_pv' => $product['BV'],
				'total_extra_pv' => 0,
				'transaction_type' => "ORDER",
				'payment_type' => "E-WALLET",
				'payment_currency' => "USD",
				'payment_amount' => $cart_total,
				'status' => "PAID",
				'order_date' => $trans_time,
				'done_by' => $signup_data['id'],
				'transaction_date' => $trans_time,
				'session_id' => "",
				'is_bonus_calculated' => 0,
				'smshare_copied' => 0,
				'smshare_response' => "",
			);
			$this->process($master, $product);

			$return['order_num'] = $trans_id;
			$return['trans_time'] = $trans_time;
			$return['status'] = "[[PAID]]";
			$return['payment_mode'] = "[[E-WALLET]]";
			$return['amount'] = $cart_total;
			$return['total_bv'] = $product['BV'];
			return successReponse("[[SIGNUP_SUCCESSFULLY_WITH_WALLET]]", $return);
		}
		return failedReponse("[[SIGNUP_FAILED]]", $errors);
	}

	public function process($master, $item)
	{
		$master_id = $this->DistributionModel->insert_master($master);
		$order_detail = array();
		$order_detail['order_master_id'] = $master_id;
		$order_detail['product_id'] = $item['id'];
		$order_detail['name'] = $item['name'];
		$order_detail['description'] = $item['description'];
		$order_detail['qty'] = 1;
		$order_detail['unit_price'] = $item['unit_price'];
		$order_detail['fee'] = 0;
		$order_detail['tax'] = 0;
		$order_detail['amount'] = (float)$item['price'];
		$order_detail['is_combo'] = ($item['is_combo'] == "N" ? 0 : 1);
		$order_detail['is_combochild'] = 0;
		$order_detail['is_purchase_once'] = 0;
		$this->db->insert('order_detail', $order_detail);
	}
}
