<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DistributionModel extends CI_Model
{
	public function insert_master($master)
	{
		$this->db->insert('order_master', $master);
		$master_id = $this->db->insert_id();
		$sb = array(
			"period" => $master['period'],
			"from_id" => $master['member_id'],
			"member_id" => $master['done_by'],
			"amount" => $master['total_amount'],
			"type" => "SPONSOR_BONUS",
			"insert_time" => $master['transaction_date'],
			"trans_id" => $master['order_num'],
		);
		$this->sponsorBonus($sb);
		$this->calculateBV($master['member_id'], $master['total_pv'], $master['period']);
		return $master_id;
	}

	public function calculateBV($member_id, $pv, $period)
	{
		$member = $this->MemberModel->getMemberInfoByIdUseridEmail($member_id);
		if (isset($member['matrixid'])) {
			if ($member['matrixid'] > 0) {
				$this->updateNode(trim($member['matrix_side']), $pv, $member['matrixid'], $period);
			}
		}
	}

	function updateNode($matrix_side, $volume, $member_id = "", $period = "")
	{
		$member = $this->MemberModel->getMemberInfoByIdUseridEmail($member_id);
		$status = isset($member['account_status']) ? $member['account_status'] : "";
		$p['id'] = $member['package_id'];
		$package = $this->ProductModel->getPackages($p);

		if ($status != 'ACTIVE' || $package['binary_bonus'] == 0) {
			$give_pts = 0;
		} else {
			$give_pts = 1;
		}

		if ($give_pts == 1) {
			$count_comm = $this->raw_query("select * from member_commissions WHERE member_id = '$member_id' AND period = '$period'", false);
			if (count($count_comm) > 0) {
				if ($matrix_side == 'R') {
					$updateQuery = "UPDATE member_commissions SET right_node = (right_node + {$volume}), accu_right_node = (accu_right_node + {$volume}),
					bright_node = (bright_node + {$volume}), addition_right_node = (addition_right_node + {$volume}) WHERE member_id = '$member_id' AND period = '$period' ";
					$this->raw_query($updateQuery, true, false);
				} else if ($matrix_side == 'L') {
					$updateQuery = "UPDATE member_commissions SET left_node = (left_node + {$volume}), accu_left_node = (accu_left_node + {$volume}),
					bleft_node = (bleft_node + {$volume}), addition_left_node = (addition_left_node + {$volume}) WHERE member_id = '$member_id' AND period = '$period' ";
					$this->raw_query($updateQuery, true, false);
				}
			} else {
				$row = $this->raw_query("select * from member_commissions WHERE member_id = " . $member_id . " ORDER BY period DESC");

				$acc_left = $acc_right = $left = $right = $bleft = $bright = $add_right = $add_left = 0;

				if (isset($row['accu_left_node'])) {
					$acc_left = $row['accu_left_node'];
					$acc_right = $row['accu_right_node'];
					$left = $row['bleft_node'];
					$right = $row['bright_node'];
					$bleft = $row['bleft_node'];
					$bright = $row['bright_node'];
				}
				if ($matrix_side == 'R') {
					$right = $right + $volume;
					$acc_right = $acc_right + $volume;
					$bright = $bright + $volume;
					$add_right = $volume;
				} else if ($matrix_side == 'L') {
					$left = $left + $volume;
					$acc_left = $acc_left + $volume;
					$bleft = $bleft + $volume;
					$add_left = $volume;
				}
				$data = array(
					"period" => $period,
					"member_id" => $member_id,
					"date_created" => 'NOW()',
					"accu_left_node" => $acc_left,
					"accu_right_node" => $acc_right,
					"addition_left_node" => $add_left,
					"addition_right_node" => $add_right,
					"left_node" => $left,
					"right_node" => $right,
					"bleft_node" => $bleft,
					"bright_node" => $bright
				);
				$this->db->insert("member_commissions", $data);
			}
		}
		$member = $this->MemberModel->getMemberInfoById($member_id);
		if (isset($member['matrixid']) && $member['matrixid'] > 0) {
			$this->updateNode($member['matrix_side'], $volume, $member['matrixid'], $period);
		}
	}

	public function sponsorBonus($arr)
	{
		$period = $arr['period'];
		$from_id = $arr['from_id'];
		$member_id = $arr['member_id'];
		$amount = $arr['amount'];
		$insert_time = $arr['insert_time'];
		$type = $arr['type'];
		$trans_id = $arr['trans_id'];

		$from_member = $this->MemberModel->getMemberInfoByIdUseridEmail($arr['from_id']);
		if (isset($from_member['id'])) {
			$member = $this->MemberModel->getMemberInfoByIdUseridEmail($arr['member_id']);
			if (isset($member['id'])) {
				$p['id'] = $member['package_id'];
				$member_package = $this->ProductModel->getPackages($p);
				if (isset($member_package['id']) && $member_package['sponsor_bonus'] > 0) {
					$pct = $member_package['sponsor_bonus'];
					if ($pct > 0) {
						$bonus = round($pct / 100 * $arr['amount'], 2);
						$trans_no = getTransId();
						$description = "[[SPONSOR_BONUS]] [[FROM]] " . $from_member['userid'];
						$mcl = array(
							"period" => $period,
							"member_id" => $member_id,
							"from_member_id" => $from_id,
							"comm_type" => $type,
							"amount" => $bonus,
							"date_created" => $insert_time,
							"based_on" => $amount,
							"percent" => $pct,
							"is_push" => 1,
							"description" => $description
						);
						$this->db->insert("member_commission_ledger", $mcl);
						$mcl_id = $this->db->insert_id();

						if ($bonus > 0) {
							$balance = 0;
							$ledger = array(
								"currency" => "CC",
								"member_id" => $member_id,
								"period" => $period,
								"trans_source_type" => $type,
								"trans_id" => $trans_id,
								"trans_no" => $mcl_id,
								"description" => $description,
								"debit" => 0,
								"credit" => $balance,
								"balance" => $insert_time,
								"insert_time" => $insert_time,
								"insert_by" => $from_member['userid']
							);
							$this->LedgerModel->insertLedger($ledger);

							if ($member['main_acct_id'] > 0) {
								$main_account = $this->MemberModel->getMemberInfoByIdUseridEmail($member['main_acct_id']);
								$desc = "[[AUTO_TRANSFER]] [[TO]] " . $main_account['userid'] .
									"  [[FROM]] " . $member['userid'] . " => " . $description . " __ " . $mcl_id . " __ " . $trans_id;
								$this->TransferModel->transfer("CC", $member['main_acct_id'], $member_id, $bonus, "AUTO_TRANSFER", $desc);
							}
						}
					}
				}
			}
		}
	}


	public function raw_query($q, $single = true, $get = true)
	{
		$query = $this->db->query($q);
		if($get) {
			if ($single) {
				return $query->row_array();
			} else {
				return $query->result_array();
			}
		}
	}
}
