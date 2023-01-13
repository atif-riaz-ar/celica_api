<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CommissionModel extends CI_Model
{
	public function getCommissions($user_id, $period)
	{
		$return['period'] = $period;
		$return['SPONSOR_BONUS'] = $this->getCommissionByType($user_id, $period, "SPONSOR_BONUS");
		$return['BINARY_BONUS'] = $this->getCommissionByType($user_id, $period, "BINARY_BONUS");
		$return['MATCHING_BONUS'] = $this->getCommissionByType($user_id, $period, "MATCHING_BONUS");
		return $return;
	}

	public function getCommissionByType($user_id, $period, $type)
	{
		$this->db->select("sum(amount) as amount");
		$this->db->where("member_id", $user_id);
		$this->db->where("period", $period);
		$this->db->where("comm_type", $type);
		$query = $this->db->get('member_commission_ledger');
		return (float)$query->row_array()['amount'];
	}

	public function getCommissionByTypePeriod($user_id, $type, $period)
	{
		$this->db->select("member_commission_ledger.*, member.userid, member.f_name, member.l_name");
		$this->db->join('member', 'member.id = member_commission_ledger.member_id');
		$this->db->where("member_id", $user_id);
		$this->db->where("period", $period);
		$this->db->where("comm_type", strtoupper($type) . "_BONUS");
		$query = $this->db->get('member_commission_ledger');
		return $query->result_array();
	}

	public function getCommissionByYear($year, $type){
		$this->db->select("sum(amount) as amount");
		$this->db->like("date_created", $year);
		$this->db->where("comm_type", $type);
		$query = $this->db->get('member_commission_ledger');
		return (float)$query->row_array()['amount'];
	}

	public function getCommissionByPeriod($period, $type){
		$this->db->select("member_commission_ledger.*, member.userid, member.f_name, member.l_name");
		$this->db->join('member', 'member.id = member_commission_ledger.member_id');
		$this->db->like("date_created", $period);
		if($type == "all"){
			$this->db->where_in("comm_type", array("SPONSOR_BONUS", "BINARY_BONUS", "MATCHING_BONUS"));
		} else {
			$this->db->where("comm_type", strtoupper($type) . "_BONUS");
		}
		$query = $this->db->get('member_commission_ledger');
		return $query->result_array();
	}

}
