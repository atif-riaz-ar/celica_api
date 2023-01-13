<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProductPurchaseWalletModel extends CI_Model
{
	public function purchaseProduct($post)
	{
		$errors = array();
		$user = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		$cart_items = $this->CartModel->getCart($user);
		$cart_bv = 0;
		$payment = false;
		$cart_total = 0;
		foreach ($cart_items as $item) {
			$cart_total += ($item['qty'] * (float)$item['unit_price']);
			$cart_bv += ($item['qty'] * (float)$item['BV']);
		}
		if ($cart_total <= 0) {
			$errors[] = "[[LABEL_ORDER_DETAILS]]";
		}
		$cc_balance = $this->LedgerModel->getBalance("RC", $user['id']);
		$available_balance = $cc_balance['cr'] - $cc_balance['dr'];
		$trans_time = getFullDate();
		$period = getMonth();
		if ($cart_total <= $available_balance) {
			$trans_id = getTransId();
			$ledger = array(
				"currency" => "CC",
				"member_id" => $user['id'],
				"period" => $period,
				"trans_source_type" => 'ORDER',
				"trans_id" => $trans_id,
				"trans_no" => $trans_id,
				"description" => "ORDER",
				"debit" => $cart_total,
				"credit" => 0,
				"balance" => ($available_balance - $cart_total),
				"insert_time" => $trans_time,
				"insert_by" => $user['userid']
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
				'order_type' => "PRODUCT",
				'member_id' => $user['id'],
				'description' => '[[DEF_PURCHASE_ITEMS]]',
				'currency' => '[[USD]]',
				'total_amount' => $cart_total,
				'subtotal' => $cart_total,
				'discount' => 0,
				'gst' => 0,
				'shipment_cost' => 0,
				'total_pv' => $cart_bv,
				'total_extra_pv' => 0,
				'transaction_type' => "ORDER",
				'payment_type' => "E-WALLET",
				'payment_currency' => "USD",
				'payment_amount' => $cart_total,
				'status' => "PAID",
				'order_date' => $trans_time,
				'done_by' => $user['id'],
				'transaction_date' => $trans_time,
				'session_id' => "",
				'is_bonus_calculated' => 0,
				'smshare_copied' => 0,
				'smshare_response' => "",
			);
			$this->process($master, $cart_items);

			$return['order_num'] = $trans_id;
			$return['trans_time'] = $trans_time;
			$return['status'] = "[[PAID]]";
			$return['payment_mode'] = "[[E-WALLET]]";
			$return['amount'] = $cart_total;
			$return['total_bv'] = $cart_bv;
			return successReponse("[[ORDER_PLACED_SUCCESSFULLY_WITH_WALLET]]", $return);
		}
		return failedReponse("[[ORDER_PLACEMENT_FAILED]]", $errors);
	}

	public function process($master, $order)
	{
		$master_id = $this->DistributionModel->insert_master($master);
		$order_detail = array();
		foreach ($order as $key => $item) {
			$order_detail[$key]['order_master_id'] = $master_id;
			$order_detail[$key]['product_id'] = $item['id'];
			$order_detail[$key]['name'] = $item['name'];
			$order_detail[$key]['description'] = $item['description'];
			$order_detail[$key]['qty'] = $item['qty'];
			$order_detail[$key]['unit_price'] = $item['unit_price'];
			$order_detail[$key]['fee'] = 0;
			$order_detail[$key]['tax'] = 0;
			$order_detail[$key]['amount'] = ($item['qty'] * (float)$item['unit_price']);
			$order_detail[$key]['is_combo'] = ($item['is_combo'] == "N" ? 0 : 1);
			$order_detail[$key]['is_combochild'] = 0;
			$order_detail[$key]['is_purchase_once'] = 0;
		}
		$this->db->insert_batch('order_detail', $order_detail);
		$this->updateCartDetails($master['member_id'], $master['order_num']);
	}

	public function updateCartDetails($user_id, $order_num)
	{
		$this->db->update('product_cart', array("master_id" => $order_num, "is_processed" => 1, "is_paid" => 1, "payment_mode" => "E-WALLET"), array('member_id' => $user_id));
	}
}
