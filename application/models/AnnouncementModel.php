<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AnnouncementModel extends CI_Model
{
	public function getAnnouncement($post)
	{
		if (isset($post['id'])) {
			$this->db->where("id", $post['id']);
		}
		$this->db->where("type", "Members");
		$this->db->or_where("type", "All");
		$query = $this->db->get('news');
		if (isset($post['id'])) {
			return $query->row_array();
		} else {
			return $query->result_array();
		}
	}

	function getAllAnnouncements()
	{
		$query = $this->db->get('news');
		return $query->result_array();
	}

	function get($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->get('news');
		return $query->row_array();
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->delete('news');
	}

	function update($post)
	{
		if (isset($post['id'])) {
			$this->db->update('news', $post, array('id' => $post['id']));
		} else {
			$this->db->insert('news', $post);
		}
	}
}
