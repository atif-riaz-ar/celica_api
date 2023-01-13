<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TopupModel extends CI_Model
{
	public function processTopup($post, $user)
	{
		$submit['deposit_amount'] = isset($post['deposit_amount']) ? $post['deposit_amount'] : 0;
		$submit['transfer_amount'] = isset($post['transfer_amount']) ? $post['transfer_amount'] : 0;
		$submit['counter_currency'] = isset($post['counter_currency']) ? $post['counter_currency'] : 0;
		$submit['currency_rate'] = isset($post['currency_rate']) ? $post['currency_rate'] : 0;
		$submit['bank_id'] = isset($post['bank_id']) ? $post['bank_id'] : 0;
		$submit['member_id'] = $user['id'];
		if (isset($post['extension'])) {
			$submit['payment_slip'] = uploadFile(array(
				'extension' => $post['extension'],
				'filedata' => $post['filedata']
			), "payment_slip");
		}
		$submit['insert_by'] = $user['userid'];
		if ($this->db->insert('member_purchase_credit', $submit)) {
			return successReponse("[[LABEL_TOP_SUCCESS_MESSAGE]]", $submit);
		} else {
			return failedReponse("[[LABEL_TOP_SUCCESS_MESSAGE]]", array());
		}
	}

	function getAllTopups()
	{
		$this->db->select('
				member_purchase_credit.*, 
				member.f_name, member.l_name, member.userid, member.email, member.mobile,
				company_bank.bank_name
			');
		$this->db->join('company_bank', 'company_bank.id = member_purchase_credit.bank_id', 'left');
		$this->db->join('member', 'member.id = member_purchase_credit.member_id', 'left');
		$this->db->order_by('member_purchase_credit.id', 'desc');
		$query = $this->db->get('member_purchase_credit');
		return $query->result_array();
	}

	function getTopup($id)
	{
		$this->db->select('
				member_purchase_credit.*, 
				member.f_name, member.l_name, member.userid, member.email, member.mobile,
				company_bank.bank_name
			');
		$this->db->join('company_bank', 'company_bank.id = member_purchase_credit.bank_id', 'left');
		$this->db->join('member', 'member.id = member_purchase_credit.member_id', 'left');
		$this->db->where('member_purchase_credit.id', $id);
		$query = $this->db->get('member_purchase_credit');
		return $query->row_array();
	}

	function action($action, $topup_id)
	{
		$post['status'] = $action;
		if($action == 'APPROVED'){
			$topup = $this->getTopup($topup_id);
			$cc = $this->LedgerModel->getBalance("CC", $topup['member_id']);
			$cc_balance = $cc['cr'] - $cc['dr'];
			$balance_cc = $cc_balance + $topup['balance'];
			$trans_id = getTransId();
			$ledger = array(
				"currency" => "CC",
				"member_id" => $topup['member_id'],
				"period" => getMonth(),
				"trans_source_type" => 'PURCHASE_CREDIT',
				"trans_id" => $trans_id,
				"trans_no" => $trans_id,
				"description" => "[[LABEL_PURCHASE_CREDIT]]",
				"debit" => 0,
				"credit" => $topup['transfer_amount'],
				"balance" => $balance_cc,
				"insert_time" => getFullDate(),
				"insert_by" => $topup['userid']
			);
			$this->LedgerModel->insertLedger($ledger);
		}
		$this->db->update('member_purchase_credit', $post, array('id' => $topup_id));
	}

}
