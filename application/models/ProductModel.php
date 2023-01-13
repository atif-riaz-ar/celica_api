<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProductModel extends CI_Model
{
	public function getProduct($post)
	{
		if (isset($post['id'])) {
			$this->db->where("id", $post['id']);
		}
		$this->db->where("type_id", 2);
		$this->db->where("is_active", 1);
		$this->db->where("for_admin", 0);
		$query = $this->db->get('product');
		if (isset($post['id'])) {
			return $query->row_array();
		} else {
			return $query->result_array();
		}
	}

	public function get($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->get('product');
		return $query->row_array();
	}

	public function getPackages($post)
	{
		if (isset($post['id'])) {
			$this->db->where("id", $post['id']);
		}
		$this->db->where("type_id", 1);
		$this->db->where("is_active", 1);
		$query = $this->db->get('product');
		if (isset($post['id'])) {
			return $query->row_array();
		} else {
			return $query->result_array();
		}
	}

	function getAllProducts()
	{
		$this->db->where("type_id", 2);
		$query = $this->db->get('product');
		return $query->result_array();
	}

	function getAllPackages()
	{
		$this->db->where("type_id", 1);
		$query = $this->db->get('product');
		return $query->result_array();
	}

	function update($post)
	{
		$submit['is_active'] = $post["is_active"];
		$submit['need_delivery'] = $post["need_delivery"];
		$submit['code'] = $post["code"];
		$submit['name'] = $post["name"];
		$submit['description'] = $post["description"];
		$submit['price'] = $post["price"];
		$submit['sponsor_bonus'] = $post["sponsor_bonus"];
		$submit['BV'] = $post["BV"];
		$submit['category_id'] = $post["category_id"];
		$submit['type_id'] = $post["type_id"];
		if (isset($post['extension'])) {
			$submit['img_file'] = uploadFile(array(
				'extension' => $post['extension'],
				'filedata' => $post['filedata']
			), "img_product");
		}
		if (isset($post['id'])) {
			$this->db->update('product', $submit, array('id' => $post['id']));
		} else {
			$this->db->insert('product', $submit);
		}
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->delete('product');
	}
}
