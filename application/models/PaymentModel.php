<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PaymentModel extends CI_Model
{
	public function getGateways($status)
	{
		$this->db->where("status", $status);
		$query = $this->db->get('payment_gateway');
		$gateways = $query->result_array();
		$refined = array();
		foreach ($gateways as $g) {
			$refined[$g['gateway']] = $g;
		}
		return $refined;
	}

	public function getHitpaySession($reference)
	{
		$system = $this->SystemModel->getConstants();
		$HITPAY_URL = $system['HITPAY_URL'] . "/v1/payment-requests/" . $reference;
		$HITPAY_APIKEY = $system['HITPAY_APIKEY'];
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $HITPAY_URL,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => array(
				'X-BUSINESS-API-KEY: ' . $HITPAY_APIKEY,
				'X-Requested-With: XMLHttpRequest'
			),
		));
		return json_decode(curl_exec($curl), true);
	}

	public function getPaymentIntent($payment_intent)
	{
		$gateway = $this->getGateways("ACTIVE")['STRIPE'];
		$url = "curl " . $gateway['webaddress'] . 'v1/payment_intents/' . $payment_intent;
		$url .= ' -H "Authorization: Bearer ' . $gateway['secret_key'] . '"';

		$result = shell_exec($url);
		$json_result = json_decode($result, true);
		return $json_result;

	}

	public function getPurchaseSession($session_id)
	{
		$gateway = $this->getGateways("ACTIVE")['STRIPE'];
		$url = "curl ".$gateway['webaddress'].'v1/checkout/sessions/'.$session_id;
		$url .= ' -H "Authorization: Bearer '.$gateway['secret_key'].'"';

		$result = shell_exec($url);
		$json_result = json_decode($result, true);
		return $json_result;
	}
}
