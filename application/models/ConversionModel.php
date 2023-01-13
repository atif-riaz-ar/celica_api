<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ConversionModel extends CI_Model
{
	public function processConversion($post, $user)
	{
		$period = getMonth();
		$convert_amount = $post['base_amount'];
		$trans_id = getTransId();
		$trans_time = getFullDate();
		$errors = $this->checkTrans($user, $convert_amount, $post['secondary_password']);
		if (count($errors) > 0) {
			return failedReponse("[[LABEL_WITHDRAWAL_FAILED_MESSAGE]]", $errors);
		} else {
			$cc = $this->LedgerModel->getBalance("CC", $user['id']);
			$cc_balance = $cc['cr'] - $cc['dr'];
			$balance_cc = $cc_balance - $convert_amount;
			$rc = $this->LedgerModel->getBalance("RC", $user['id']);
			$rc_balance = $rc['cr'] - $rc['dr'];
			$balance_rc = $rc_balance + $convert_amount;

			$ledger = array(
				"currency" => "CC",
				"member_id" => $user['id'],
				"period" => $period,
				"trans_source_type" => 'POINT_CONVERTER',
				"trans_id" => $trans_id,
				"trans_no" => $trans_id,
				"description" => "POINT_CONVERT",
				"debit" => $convert_amount,
				"credit" => 0,
				"balance" => $balance_cc,
				"insert_time" => $trans_time,
				"insert_by" => $user['userid']
			);
			$this->LedgerModel->insertLedger($ledger);

			$ledger = array(
				"currency" => "RC",
				"member_id" => $user['id'],
				"period" => $period,
				"trans_source_type" => 'POINT_CONVERTER',
				"trans_id" => $trans_id,
				"trans_no" => $trans_id,
				"description" => "POINT_CONVERT",
				"debit" => 0,
				"credit" => $convert_amount,
				"balance" => $balance_rc,
				"insert_time" => $trans_time,
				"insert_by" => $user['userid']
			);
			$this->LedgerModel->insertLedger($ledger);
			$ledger['from'] = "CC";
			$ledger['to'] = "RC";
			$ledger['amount'] = $convert_amount;
			return successReponse("[[LABEL_CONVERSION_SUCCESS_MESSAGE]]", $ledger);
		}

	}

	public function checkTrans($user, $convert_amount, $password)
	{
		$cc = $this->LedgerModel->getBalance("CC", $user['id']);
		$balance = $cc['cr'] - $cc['dr'];
		$errors = array();
		if (empty($convert_amount) || !is_numeric($convert_amount)) {
			$errors[] = "[[MSG_CONVERT_AMT_NUMERIC]]";
		}
		if ($convert_amount > $balance) {
			$errors[] = "[[MSG_INSUFFICIENT_FUNDS]]";
		}
		return $errors;
	}

}
