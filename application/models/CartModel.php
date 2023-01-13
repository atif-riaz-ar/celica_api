<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CartModel extends CI_Model
{

	public function getCart($user)
	{
		$this->db->select("product.*, product_cart.member_id, product_cart.product_id, sum(product_cart.quantity) as qty");
		$this->db->join('product', 'product.id = product_cart.product_id');
		$this->db->where("product_cart.member_id", $user['id']);
		$this->db->where("product_cart.master_id", null);
		$this->db->where("product_cart.is_processed", 0);
		$this->db->group_by("product_id");
		$query = $this->db->get('product_cart');
		return $query->result_array();
	}

	public function getCartByMasterId($master_id)
	{
		$this->db->select("product.*, product_cart.member_id, product_cart.product_id, sum(product_cart.quantity) as qty");
		$this->db->join('product', 'product.id = product_cart.product_id');
		$this->db->where("product_cart.master_id", $master_id);
		$this->db->where("product_cart.is_paid", 0);
		$this->db->group_by("product_id");
		$query = $this->db->get('product_cart');
		return $query->result_array();
	}

	public function addToCart($details)
	{
		$cart['member_id'] = $details['user']['id'];
		$cart['quantity'] = $details['quantity'];
		$cart['product_id'] = $details['product_id'];
		return $this->db->insert('product_cart', $cart);
	}

	public function updateCart($details)
	{
		$this->db->where("is_processed", 0);
		$this->db->where("master_id", null);
		$this->db->where("member_id", $details['user']['id']);
		$this->db->where("product_id", $details['product_id']);
		$this->db->delete('product_cart');
		if($details['quantity'] > 0) {
			return $this->addToCart($details);
		}
		return 1;
	}
}
