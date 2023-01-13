<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Binary extends CI_Controller
{
	public function index()
	{
		$period = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
		$insert_time = date('Y-m-d H:i:s');

		$members = $this->members($period);

		foreach ($members as $member) {
			if (isset($member['comm_id']) && $member['bleft_node'] > 0 && $member['bright_node'] > 0 && $member['package_id'] > 0) {
				$binary_pct = $member['binary_bonus'];
				$binary_cap = $member['binary_bonus_daily_cap'];

				$left_node = $member['bleft_node'];
				$right_node = $member['bright_node'];

				$left_deduct_node = $right_deduct_node = $deduct_node = min($left_node, $right_node);
				$left_node = $left_node - $left_deduct_node;
				$right_node = $right_node - $right_deduct_node;

				$commission_val = round(($binary_pct / 100) * $deduct_node, 2);

				if ($binary_cap > 0) {
					if ($commission_val > $binary_cap) {
						$commission_val = $binary_cap;
						$left_deduct_node = $member['bleft_node'];
						$right_deduct_node = $member['bright_node'];
						$left_node = 0;
						$right_node = 0;
					}
				}

				if ($commission_val > 0) {
					$q1['bright_node'] = $right_node;
					$q1['bleft_node'] = $left_node;
					$q1['deduction_left_node'] = $left_deduct_node;
					$q1['deduction_right_node'] = $right_deduct_node;
					$this->db->where('id', $member['comm_id']);
					$this->db->update("member_commissions", $q1);

					$this->db->set('bright_node', "(bright_node-" . $right_deduct_node . ")", FALSE);
					$this->db->set('bleft_node', "(bleft_node - " . $left_deduct_node . ")", FALSE);
					$this->db->set('left_node', "(left_node - " . $left_deduct_node . ")", FALSE);
					$this->db->set('right_node', "(right_node - " . $right_deduct_node . ")", FALSE);
					$this->db->where("period >", $members['period']);
					$this->db->where('id', $member['comm_id']);
					$this->db->update("member_commissions");

					$is_push = $member['account_status'] == "ACTIVE" ? 1 : 0;

					$description = "[[BINARY_BONUS]] " . date("d M Y", strtotime($period));

					$data = array(
						"period" => substr($period, 0, 7),
						"member_id" => $member['member_id'],
						"from_member_id" => 0,
						"comm_type" => "BINARY_BONUS",
						"amount" => $commission_val,
						"date_created" => $insert_time,
						"based_on" => $deduct_node,
						"percent" => $binary_pct,
						"is_push" => $is_push,
						"description" => $description
					);
					$this->db->insert("member_commission_ledger", $data);
					$comid = $this->db->insert_id();

					if ($is_push) {
						$trans_no = getTransId();
						$cc = $this->LedgerModel->getBalance("CC", $member['member_id']);
						$cc_balance = $cc['cr'] - $cc['dr'];
						$balance_cc = $cc_balance + $commission_val;
						$ledger = array(
							"currency" => "CC",
							"member_id" => $member['member_id'],
							"period" => substr($period, 0, 7),
							"trans_source_type" => 'BINARY_BONUS',
							"trans_id" => $comid,
							"trans_no" => $trans_no,
							"description" => $description,
							"debit" => 0,
							"credit" => $commission_val,
							"balance" => $balance_cc,
							"insert_time" => $insert_time,
							"insert_by" => $member['userid']
						);
						$this->LedgerModel->insertLedger($ledger);

						if ($member['main_acct_id'] > 0) {
							$main_account = $this->MemberModel->getMemberInfoByIdUseridEmail($member['main_acct_id']);
							$desc = "[[AUTO_TRANSFER]] [[TO]] " . $main_account['userid'] .
								"  [[FROM]] " . $member['userid'] . " => " . $description . " __ " . $comid . " __ " . $trans_no;
							$this->TransferModel->transfer("CC", $member['main_acct_id'], $member['member_id'], $commission_val, "AUTO_TRANSFER", $desc);
						}
					}

					$this->calculateMatchingBonus($member['member_id'], $member['userid'], $member['sponsorid'], $commission_val, $period, $insert_time, 0);
				}
			}
		}
	}

	public function calculateMatchingBonus($from_id, $from_userid, $member_id, $amount, $period, $insert_time, $level)
	{
		$member = $this->member_info($member_id);
		$matching = explode(",", $member['matching_bonus']);
		if (!is_array($matching)) {
			$matching[0] = $matching;
		}
		if (isset($matching[$level])) {
			$bonus_pct = $matching[$level];
			$bonus = $bonus_pct / 100 * $amount;
			$is_push = $member['account_status'] == "ACTIVE" ? 1 : 0;

			$description = "[[MATCHING_BONUS]] [[FROM]] " . $from_userid . " " . date("d M Y", strtotime($period));
			$data = array(
				"period" => substr($period, 0, 7),
				"member_id" => $member['id'],
				"from_member_id" => $from_id,
				"comm_type" => "MATCHING_BONUS",
				"amount" => $bonus,
				"date_created" => $insert_time,
				"based_on" =>  $amount,
				"percent" => $bonus_pct,
				"is_push" => $is_push,
				"description" => $description
			);
			$this->db->insert("member_commission_ledger", $data);
			$comid = $this->db->insert_id();

			if ($is_push) {
				$trans_no = getTransId();
				$cc = $this->LedgerModel->getBalance("CC", $member['id']);
				$cc_balance = $cc['cr'] - $cc['dr'];
				$balance_cc = $cc_balance + $bonus;
				$ledger = array(
					"currency" => "CC",
					"member_id" => $member['id'],
					"period" => substr($period, 0, 7),
					"trans_source_type" => 'MATCHING_BONUS',
					"trans_id" => $comid,
					"trans_no" => $trans_no,
					"description" => $description,
					"debit" => 0,
					"credit" => $bonus,
					"balance" => $balance_cc,
					"insert_time" => $insert_time,
					"insert_by" => $member['userid']
				);
				$this->LedgerModel->insertLedger($ledger);


				if ($member['main_acct_id'] > 0) {
					$main_account = $this->MemberModel->getMemberInfoByIdUseridEmail($member['main_acct_id']);
					$desc = "[[AUTO_TRANSFER]] [[TO]] " . $main_account['userid'] .
						"  [[FROM]] " . $member['userid'] . " => " . $description . " __ " . $comid . " __ " . $trans_no;
					$this->TransferModel->transfer("CC", $member['main_acct_id'], $member['member_id'], $bonus, "AUTO_TRANSFER", $desc);
				}
			}

			if ($level < 2) {
				$level++;
				$this->calculateMatchingBonus($from_id, $from_userid, $member['sponsorid'], $amount, $period, $insert_time, $level);
			}
		}
	}

	public function member_info($member_id)
	{
		$this->db->select("m.id, m.userid, m.main_acct_id, m.sponsorid, m.package_id, m.account_status, 
		p.binary_bonus, p.binary_bonus_daily_cap, p.matching_bonus");
		$this->db->join('product p', 'm.package_id = p.id');
		$this->db->where("m.id", $member_id);
		$query = $this->db->get('member m');
		return $query->row_array();
	}

	public function members($period)
	{
		$this->db->select("m.id as member_id, m.userid, m.main_acct_id, m.sponsorid, m.package_id, m.account_status,
			mc.bleft_node, mc.bright_node, mc.id as comm_id, mc.period, p.binary_bonus, p.binary_bonus_daily_cap");
		$this->db->join('member m', 'm.id = mc.member_id');
		$this->db->join('product p', 'm.package_id = p.id');
		$this->db->where("mc.period", $period);
		$this->db->where("mc.bleft_node >", 0);
		$this->db->where("mc.bright_node >", 0);
		$this->db->where("m.package_id >", 0);
		$this->db->order_by("mc.period", "DESC");
		$this->db->group_by("mc.period, m.id");
		$query = $this->db->get('member_commissions mc');
		return $query->result_array();
	}


}
