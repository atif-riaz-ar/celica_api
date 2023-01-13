<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PackagePurchaseStripeModel extends CI_Model
{
	public function purchaseProduct($post)
	{
		$errors = array();
		$stripe_session = json_decode($post['stripe_session'], true);
		$signup_data = json_decode($post['signup_data'], true);
		$result = $this->PaymentModel->getPurchaseSession($stripe_session['id']);
		if (isset($result['payment_intent'])) {
			$payment = $this->PaymentModel->getPaymentIntent($result['payment_intent']);
			if (isset($payment['charges']['data'][0]['paid']) && $payment['charges']['data'][0]['paid'] == 1) {
				if(isset($post['access_token'])) {
					$user = $this->MemberModel->getMemberInfoByToken($post['access_token']);
				} else {
					$user = $this->MemberModel->getMemberInfoByUserid($signup_data['user']);
				}
				$p['id'] = $signup_data['package_id'];
				$product = $this->ProductModel->getPackages($p);
				$trans_time = getFullDate();
				$period = getMonth();
				if (count($errors) < 1) {
					$new_user = $this->SignupModel->createUser($signup_data);
					$master = array(
						'period' => $period,
						'order_num' => $post['order_num'],
						'invoice_no' => $post['order_num'],
						'customer_type' => "MEMBER",
						'order_type' => "PACKAGE",
						'member_id' => $new_user['id'],
						'description' => '[[DEF_PACKAGE_PURCHASE]]',
						'currency' => '[[USD]]',
						'total_amount' => $product['price'],
						'subtotal' => $product['price'],
						'discount' => 0,
						'gst' => 0,
						'shipment_cost' => 0,
						'total_pv' => $product['BV'],
						'total_extra_pv' => 0,
						'transaction_type' => "ORDER",
						'payment_type' => "STRIPE",
						'payment_currency' => "USD",
						'payment_amount' => $product['price'],
						'status' => "PAID",
						'order_date' => $trans_time,
						'done_by' => $user['id'],
						'transaction_date' => $trans_time,
						'session_id' => $stripe_session['id'],
						'is_bonus_calculated' => 0,
						'smshare_copied' => 0,
						'smshare_response' => "",
					);
					$this->process($master, $product);
					$return['order_num'] = $post['order_num'];
					$return['trans_time'] = $trans_time;
					$return['status'] = "[[PAID]]";
					$return['payment_mode'] = "[[STRIPE]]";
					$return['amount'] = $product['price'];
					$return['total_bv'] = $product['BV'];
					return successReponse("[[SIGNUP_SUCCESSFULLY_WITH_STRIPE]]", $return);
				}
				return failedReponse("[[SIGNUP_FAILED]]", $errors);
			}
		}
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
		$order_detail['amount'] = $item['unit_price'];
		$order_detail['is_combo'] = ($item['is_combo'] == "N" ? 0 : 1);
		$order_detail['is_combochild'] = 0;
		$order_detail['is_purchase_once'] = 0;
		$this->db->insert('order_detail', $order_detail);
	}
}
