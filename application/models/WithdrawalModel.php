<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WithdrawalModel extends CI_Model
{
	public function processWithdrawal($post, $user)
	{
		$system = $this->SystemModel->getConstants();
		$WITHDRAWAL_FEE = $system['WITHDRAWAL_FEE'];
		$period = getMonth();
		$withdraw_amount = $post['txtAmountDeducted'];
		$trans_id = getTransId();
		$trans_time = getFullDate();
		$errors = $this->checkTrans($user['id'], $withdraw_amount, $period);
		if (count($errors) > 0) {
			return failedReponse("[[LABEL_WITHDRAWAL_FAILED_MESSAGE]]", $errors);
		} else {
			switch ($_POST['selPaymentMode']) {
				case 'PAYNOW' :
					$cashbank_name = trim($_POST['paynow']);
					break;
				case 'PAYPAL' :
					$cashbank_name = trim($_POST['txtPaypalAddress']);
					break;
				case 'CHEQUE' :
					$cashbank_name = trim($_POST['txtChequeName']);
					break;
				case 'BANK' :
					$bank_info = $this->BankModel->getMemberBankById($_POST['bank'], "id");
					$cashbank_name = $bank_info['bank_name'] . '(' . $bank_info['account_number'] . ')';
					break;
			}
			$cc = $this->LedgerModel->getBalance("CC", $user['id']);
			$balance = $cc['cr'] - $cc['dr'];
			$remaining_balance = $balance - $withdraw_amount;
			$cashbank = array(
				"period" => $period,
				"customer_type" => 'MEMBER',
				"member_id" => $user['id'],
				"cashbank_type" => $_POST['selPaymentMode'],
				"description" => "",
				"cashbank_name" => $cashbank_name,
				"trans_type" => 'COMMISSION WITHDRAWAL',
				"currency" => "CC",
				"debit" => $withdraw_amount,
				"balance" => $remaining_balance,
				"admin_fee" => $WITHDRAWAL_FEE,
				"member_rcv" => ($withdraw_amount - $WITHDRAWAL_FEE),
				"due_date" => withdrawalDate(),
				"status" => 'PENDING',
				"trans_date" => $trans_time,
				"trans_by" => $user['userid'],
				"trans_id" => $trans_id,
			);
			$cashbank_id = $this->db->insert('cashbank_transaction', $cashbank);

			$ledger = array(
				"currency" => "CC",
				"member_id" => $user['id'],
				"period" => getMonth(),
				"trans_source_type" => 'COMMISSION_WITHDRAWAL',
				"trans_id" => $cashbank_id,
				"trans_no" => $trans_id,
				"description" => "PENDING_WITHDRAWAL",
				"debit" => $withdraw_amount,
				"credit" => 0,
				"balance" => $remaining_balance,
				"insert_time" => $trans_time,
				"insert_by" => $user['userid']
			);
			$this->LedgerModel->insertLedger($ledger);
			$ledger["admin_fee"] = $WITHDRAWAL_FEE;
			$ledger["member_rcv"] = ($withdraw_amount - $WITHDRAWAL_FEE);
			$ledger["due_date"] = withdrawalDate();
			return successReponse("[[LABEL_WITHDRAWAL_SUCCESS_MESSAGE]]", $ledger);
		}

	}

	public function checkTrans($user_id, $withdraw_amount, $period)
	{
		$cc = $this->LedgerModel->getBalance("CC", $user_id);
		$balance = $cc['cr'] - $cc['dr'];
		$errors = array();
		if (empty($withdraw_amount) || !is_numeric($withdraw_amount)) {
			$errors[] = "[[MSG_WITHDRAWAL_AMT_NUMERIC]]";
		}
		if ($withdraw_amount % 100 != 0) {
			$errors[] = "[[MSG_WITHDRAWAL_IN_HUNDRED]]";
		}
		if ($withdraw_amount > $balance) {
			$errors[] = "[[MSG_INSUFFICIENT_FUNDS]]";
		}
		$monthly_withdrawal = $this->CashBankModel->getWithdrawals($user_id, $period);
		if (count($monthly_withdrawal) >= 1) {
			$errors[] = "[[ERROR_MAX_WITHDRAW_LIMIT]]";
		}
		return $errors;
	}

	function getAllWithdrawals()
	{
		return $this->DistributionModel->raw_query("
			SELECT ct.*, m.f_name, m.l_name, m.userid, m.email, m.mobile FROM cashbank_transaction ct 
			    JOIN member m on m.id = ct.member_id 
				ORDER BY case 
				    when ct.status = 'PENDING' then 1 
				    when ct.status = 'HOLD' then 2 
				    else 3 end, ct.id desc;
		", false);
	}

	function getWithdrawal($id)
	{
		$this->db->select('
				cashbank_transaction.*, 
				member.f_name, member.l_name, member.userid, member.email, member.mobile
			');
		$this->db->join('member', 'member.id = cashbank_transaction.member_id', 'left');
		$this->db->where('cashbank_transaction.id', $id);
		$query = $this->db->get('cashbank_transaction');
		return $query->row_array();
	}

	function action($action, $withrawal_id)
	{
		$col = $action . "_date";
		$post[$col] = getFullDate();
		$post['status'] = strtoupper($action);
		if ($action == 'reject') {
			$withrawal = $this->getWithdrawal($withrawal_id);
			$cc = $this->LedgerModel->getBalance("CC", $withrawal['member_id']);
			$cc_balance = $cc['cr'] - $cc['dr'];
			$balance_cc = $cc_balance + $withrawal['debit'];
			$trans_id = getTransId();
			$ledger = array(
				"currency" => "CC",
				"member_id" => $withrawal['member_id'],
				"period" => getMonth(),
				"trans_source_type" => 'COMMISSION_WITHDRAWAL_VOID',
				"trans_id" => $trans_id,
				"trans_no" => $withrawal['id'],
				"description" => "ADMIN REJECT WITHDRAWAL APPLICATION",
				"debit" => 0,
				"credit" => $withrawal['debit'],
				"balance" => $balance_cc,
				"insert_time" => getFullDate(),
				"insert_by" => $withrawal['userid']
			);
			$this->LedgerModel->insertLedger($ledger);
		}
		$this->db->update('cashbank_transaction', $post, array('id' => $withrawal_id));
	}

}
