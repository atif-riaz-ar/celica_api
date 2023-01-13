<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeliveriesModel extends CI_Model
{
	public function getOrders($post)
	{
		$keyword = $post['txtSearch'];
		$status = $post['do_status'];
		$member = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		if ($status != "ALL") {
			if ($status == "DELIVERED") {
				$this->db->where('received_date is NOT NULL', NULL, FALSE);
			} else {
				$this->db->where(array('received_date' => NULL));
			}
		}
		if ($keyword != "") {
			$this->db->where("order_num", $keyword);
		}
		$this->db->where("member_id", $member['id']);
		$this->db->order_by("order_date", "asc");
		$query = $this->db->get("order_master");
		return $query->result_array();
	}

}
