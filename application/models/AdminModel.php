<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminModel extends CI_Model
{
	function getAllAdmins()
	{
		$this->db->select('admin.*, admin_category.name as group, admin_category.access_list');
		$this->db->join('admin_category', 'admin_category.id = admin.group_id');
		$query = $this->db->get('admin');
		return $query->result_array();
	}

	function get($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->get('admin');
		return $query->row_array();
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->delete('admin');
	}

	function update($post)
	{
		if (!empty($post['password'])) {
			$pass = generatePassword($post['password']);
			$post['password'] = $pass['password'];
			$post['primary_salt'] = $pass['salt'];
		} else {
			unset($post['password']);
		}
		if (isset($post['id'])) {
			$this->db->update('admin', $post, array('id' => $post['id']));
		} else {
			$this->db->insert('admin', $post);
		}
	}
}
