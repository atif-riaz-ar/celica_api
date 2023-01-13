<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends CI_Controller
{
	public function ewallet(){
		return $this->ProductPurchaseWalletModel->purchaseProduct($_POST);
	}

	public function cod()
	{
		return $this->ProductPurchaseCodModel->purchaseProduct($_POST);
	}

	public function stripe()
	{
		return $this->ProductPurchaseStripeModel->purchaseProduct($_POST);
	}

	public function hitpay()
	{
		return $this->ProductPurchaseHitpayModel->purchaseProduct($_POST);
	}
}
