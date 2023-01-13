<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SettingModel extends CI_Model
{
	function getAllSettings()
	{
		$this->db->where('is_editable', 1);
		$query = $this->db->get('constants');
		return $query->result_array();
	}

	function update($post)
	{
		foreach ($post as $key => $val){
			$k = explode("_", $key);
			$id = $k[1];
			$col = $k[0];
			$p = array($col => $val);
			$this->db->update('constants', $p, array('id' => $id));
		}
	}
}
