<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CurrencyModel extends CI_Model
{
	public function get()
	{
		$this->db->where("is_shown", 1);
		$query = $this->db->get('currency_list');
		return $query->result();
	}
}
