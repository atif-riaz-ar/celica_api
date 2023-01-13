<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PdfModel extends CI_Model
{
	public function getOrderInfo($id)
	{
		$this->db->select("order_master.*, member.userid, member.f_name, member.mobile, member.userid, member.sponsorid, member.l_name, country_list.full_name");
		$this->db->join('member', 'member.id = order_master.member_id');
		$this->db->join('country_list', 'country_list.id = member.country');
		$this->db->where("order_master.id", $id);
		$query = $this->db->get('order_master');
		return $query->row_array();
	}

	public function getSalesDetail($id)
	{
		$this->db->select("order_detail.*, product.code, product.description, product.name, product.price");
		$this->db->join('product', 'product.id = order_detail.product_id');
		$this->db->where("order_detail.order_master_id", $id);
		$query = $this->db->get('order_detail');
		return $query->result_array();
	}

}
