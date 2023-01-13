<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CashBankModel extends CI_Model
{
	public function getWithdrawals($user_id, $period = null)
	{
		$this->db->where("member_id", $user_id);
		if ($period != null) {
			$this->db->where("period", $period);
		}
		$query = $this->db->get('cashbank_transaction');
		return $query->result_array();
	}

}
