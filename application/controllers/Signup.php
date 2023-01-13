<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Signup extends CI_Controller
{
	public function getUser($userid)
	{
		$user = $this->MemberModel->getMemberInfoByUserid($userid);
		$suggestion['matrix'] = $user['referral_placement'] == 0 ? $user['id'] : $user['referral_placement'];
		$suggestion['side'] = $user['referral_side'];
		$return = $this->DownlineModel->getPlacement($suggestion);
		$return['sponsor'] = $user;
		$return['countries'] = $this->CountryModel->getAllCountries();
		if (isset($user['id'])) {
			return successReponse("", $return);
		} else {
			return failedReponse("[[LABEL_INVALID_SPONSOR_LINK]]", array());
		}
	}

	public function form()
	{
		$return = $this->SignupModel->getMemberFormData();
		return successReponse("", $return);
	}

	public function stripe()
	{
		$signup_data = json_decode($_POST['signup_data'], true);
		$p['id'] = $signup_data['package_id'];
		$product = $this->ProductModel->getPackages($p);
		$return = $this->stripeSession($product['price']);
		return successReponse("", $return);
	}

	public function stripeSession($product_price)
	{
		$order_num = getTransId();
		$gateway = $this->PaymentModel->getGateways("ACTIVE")['STRIPE'];
		$url = "curl " . $gateway['webaddress'] . 'v1/checkout/sessions';
		$url .= ' -H "Authorization: Bearer ' . $gateway['secret_key'] . '"';
		$url .= ' -d payment_method_types[]=card';
		$url .= ' -d line_items[][name]="' . $order_num . '"';
		$url .= ' -d line_items[][description]="' . $order_num . '"';
		$url .= ' -d line_items[][quantity]=1';
		$url .= ' -d line_items[][amount]=' . round($product_price * 100, 0);
		$url .= ' -d line_items[][currency]=sgd';
		$url .= ' -d success_url="' . MEMBER_URL . 'signup/stripe_success/' . $order_num . '"';
		$url .= ' -d cancel_url="' . MEMBER_URL . 'signup/stripe_cancel/' . $order_num . '"';
		$result = shell_exec($url);
		return $result;
	}

	public function stripe_success()
	{
		return $this->PackagePurchaseStripeModel->purchaseProduct($_POST);
	}

}
