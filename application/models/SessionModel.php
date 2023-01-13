<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SessionModel extends CI_Model
{
	function validateSession($access_token)
	{
		$this->db->where("access_token", $access_token);
		$this->db->where("expiry_time",">",date("Y-m-d H:i:s"));
		$query = $this->db->get('session');
		return $query->row_array();
	}

	function setSession($session)
	{
		$arr['session_id'] = md5(date("YmdHis") . rand(1000, 9999));;
		$arr['access_token'] = $session['access_token'];
		$arr['parameter'] = serialize($arr);
		$arr['expiry_time'] = date('Y-m-d H:i:s', strtotime("+8 hours", strtotime(date("Y-m-d H:i:s"))));
		$arr['id'] = $this->db->insert('session', $arr);
		return $arr;
	}

	public function updateSession($data, $id)
	{
		$new_data['access_token'] = $data['access_token'];
		$new_data['parameter'] = serialize($data);
		$new_data['expiry_time'] = date('Y-m-d H:i:s', strtotime("+8 hours", strtotime(date("Y-m-d H:i:s"))));
		$this->db->update('session', $new_data, array('id' => $id));
	}

}
