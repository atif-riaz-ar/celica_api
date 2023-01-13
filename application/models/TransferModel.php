<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TransferModel extends CI_Model
{
	public function processTransfer($post, $user)
	{
		$transfer_user = $this->MemberModel->getMemberInfoByIdUseridEmail($post['txtUserID']);
		$transfer_amount = $post['RC_amount'];
		$trans_time = getFullDate();
		$errors = $this->checkTrans($user, $transfer_amount);
		if (count($errors) > 0) {
			return failedReponse("[[LABEL_WITHDRAWAL_FAILED_MESSAGE]]", $errors);
		} else {
			$this->transfer("RC", $transfer_user['id'], $user['id'], $transfer_amount, "TRANSFER", $transfer_user['userid']. " [[TO]] ". $user['userid']);
			$ledger['to'] = $transfer_user['userid'];
			$ledger['from'] = $user['userid'];
			$ledger['amount'] = $transfer_amount;
			$ledger['insert_time'] = $trans_time;
			return successReponse("[[LABEL_TRANSFER_SUCCESS_MESSAGE]]", $ledger);
		}

	}

	public function transfer($currency, $receiver, $sender, $amount, $type, $desc) {
		$balance = 0;
		$period = getMonth();
		$trans_id = getTransId();
		$trans_time = getFullDate();
		$ledger = array(
			"currency" => $currency,
			"member_id" => $receiver,
			"period" => $period,
			"trans_source_type" => $type,
			"trans_id" => $trans_id,
			"trans_no" => $trans_id,
			"description" => $desc,
			"debit" => 0,
			"credit" => $amount,
			"balance" => $balance,
			"insert_time" => $trans_time,
			"insert_by" => "System"
		);
		$this->LedgerModel->insertLedger($ledger);
		$ledger = array(
			"currency" => $currency,
			"member_id" => $sender,
			"period" => $period,
			"trans_source_type" => $type,
			"trans_id" => $trans_id,
			"trans_no" => $trans_id,
			"description" => $desc,
			"debit" => $amount,
			"credit" => 0,
			"balance" => $balance,
			"insert_time" => $trans_time,
			"insert_by" => "Syetem"
		);
		$this->LedgerModel->insertLedger($ledger);
	}

	public function checkTrans($user, $transfer_amount)
	{
		$cc = $this->LedgerModel->getBalance("RC", $user['id']);
		$balance = $cc['cr'] - $cc['dr'];
		$errors = array();
		if (empty($transfer_amount) || !is_numeric($transfer_amount)) {
			$errors[] = "[[MSG_CONVERT_AMT_NUMERIC]]";
		}
		if ($transfer_amount > $balance) {
			$errors[] = "[[MSG_INSUFFICIENT_FUNDS]]";
		}
		return $errors;
	}

}
