<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GroupModel extends CI_Model
{
	function getAllGroups()
	{
		$query = $this->db->get('admin_category');
		return $query->result_array();
	}

	function get($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->get('admin_category');
		return $query->row_array();
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->delete('admin_category');
	}

	function update($post)
	{
		if (isset($post['id'])) {
			$this->db->update('admin_category', $post, array('id' => $post['id']));
		} else {
			$this->db->insert('admin_category', $post);
		}
	}
}
