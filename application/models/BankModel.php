<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BankModel extends CI_Model
{
	public function getMemberBanks($user_id)
	{
		$this->db->where("member_id", $user_id);
		$this->db->group_by("bank_id");
		$query = $this->db->get('member_bank');
		return $query->result_array();
	}

	public function getCompBanks()
	{
		$query = $this->db->get('company_bank');
		return $query->result_array();
	}

	function delete($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->delete('company_bank');
	}

	public function getBankListByCountry($country_id)
	{
		$this->db->where("country_id", $country_id);
		$query = $this->db->get('bank_list');
		return $query->result();
	}

	public function getBankList()
	{
		$query = $this->db->get('bank_list');
		return $query->result_array();
	}

	public function get($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->get('company_bank');
		return $query->row_array();
	}

	public function getBankDetailById($bank_id)
	{
		$this->db->where("id", $bank_id);
		$query = $this->db->get('bank_list');
		return $query->row_array();
	}

	public function getCompanyBanks($country)
	{
		$this->db->select("company_bank.*, bank_list.bank_name");
		$this->db->join('bank_list', 'company_bank.bank_id = bank_list.id');
		$this->db->where("company_bank.is_active", 1);
		$this->db->where("company_bank.country", $country);
		$query = $this->db->get('company_bank');
		return $query->result();
	}

	public function getMemberBankById($bank_id)
	{
		$this->db->where("id", $bank_id);
		$query = $this->db->get('member_bank');
		return $query->row_array();
	}

	public function updateMemberBank($post)
	{
		$id = $post['id'];
		unset($post['id']);
		unset($post['access_token']);
		if ($post['bank_id'] > 0) {
			$bnk = $this->getBankDetailById($post['bank_id']);
			$post['bank_name'] = $bnk['bank_name'];
		}
		if ((int)$id > 0) {
			$this->db->update('member_bank', $post, array('id' => $id));
		} else {
			$this->db->insert('member_bank', $post);
		}
	}

	public function updateCompanyBank($post)
	{
		$id = isset($post['id'])? $post['id'] : "";
		unset($post['access_token']);
		if ($post['bank_id'] > 0) {
			$bnk = $this->getBankDetailById($post['bank_id']);
			$post['bank_name'] = $bnk['bank_name'];
		}
		if ((int)$id > 0) {
			$this->db->update('company_bank', $post, array('id' => $id));
		} else {
			$this->db->insert('company_bank', $post);
		}
	}
}
