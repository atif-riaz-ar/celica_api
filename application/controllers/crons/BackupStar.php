<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Star extends CI_Controller
{

	public function trigger()
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$array = array(
			"2019-03",
			"2019-04",
			"2019-05",
			"2019-06",
			"2019-07",
			"2019-08",
			"2019-09",
			"2019-10",
			"2019-11",
			"2019-12",
			"2020-01",
			"2020-02",
			"2020-03",
			"2020-04",
			"2020-05",
			"2020-06",
			"2020-07",
			"2020-08",
			"2020-09",
			"2020-10",
			"2020-11",
			"2020-12",
			"2020-01",
			"2021-02",
			"2021-03",
			"2021-04",
			"2021-05",
			"2021-06",
			"2021-07",
			"2021-08",
			"2021-09",
			"2021-10",
			"2021-11",
			"2021-12",
			"2022-01",
			"2022-02",
			"2022-03",
			"2022-04",
			"2022-05",
			"2022-06",
			"2022-07",
			"2022-08",
			"2022-09",
			"2022-10",
			"2022-11",
			"2022-12",
			"2023-01",
		);
		foreach ($array as $date) {
			$this->get_helpers($date);
		}
	}

	public function get_helpers($prd = "")
	{
		$period = $prd == "" ? date("Y-m") : $prd;
		$entry_date = date("Y-m-t", strtotime($period)) . " 23:59:59";
		$members = $this->members();


		foreach ($members as $member) {
			$RightTeam = array();
			$LeftTeam = array();

			$Left = array();
			$Right = array();
			$downlines = $this->getNode($member['id']);
			foreach ($downlines as $downline) {
				if ($downline['matrix_side'] == "L") {
					$Left['id'] = $downline['id'];
				}
				if ($downline['matrix_side'] == "R") {
					$Right['id'] = $downline['id'];
				}
			}

			if (count($Left) > 0) {
				$LeftTeam = $this->getDownlineMemberWithRanks($Left['id'], 'L', $entry_date);
			}
			if (count($Right) > 0) {
				$RightTeam = $this->getDownlineMemberWithRanks($Right['id'], 'R', $entry_date);
			}

			$LeftIds = array_column($LeftTeam, 'id');
			$RightIds = array_column($RightTeam, 'id');

			if (count($Left) > 0) {
				$LeftIds[] = $Left['id'];
			}
			if (count($Right) > 0) {
				$RightIds[] = $Right['id'];
			}

			$lbv = 0;
			$rbv = 0;

			if (count($LeftIds) > 0) {
				$lbv = (float)$this->getSalesofDesiredMonth($period, $LeftIds);
			}

			if (count($RightIds) > 0) {
				$rbv = (float)$this->getSalesofDesiredMonth($period, $RightIds);
			}

			$post['member_id'] = $member['id'];
			$post['period'] = $period;
			$post['lbv'] = $lbv;
			$post['rbv'] = $rbv;
			$post['left_downline'] = implode(",", $LeftIds);
			$post['right_downline'] = implode(",", $RightIds);

			$this->db->where('member_id', $member['id']);
			$this->db->where('period', $period);
			$query = $this->db->get('star_helper');
			$star_helper = $query->row_array();

			if (isset($star_helper['id'])) {
				$this->db->update('star_helper', $post, array(
					'member_id' => $member['id'],
					'period' => $period
				));
			} else {
				$this->db->insert('star_helper', $post);
			}

		}
	}

	public function index($prd = "")
	{
		set_time_limit(0);
		$period = $prd == "" ? date("Y-m") : $prd;
		$months1 = date("Y-m", strtotime($period . " -1 months"));
		$months2 = date("Y-m", strtotime($period . " -2 months"));
		$months3 = date("Y-m", strtotime($period . " -3 months"));
		$months4 = date("Y-m", strtotime($period . " -4 months"));
		$months5 = date("Y-m", strtotime($period . " -5 months"));
		$months6 = date("Y-m", strtotime($period . " -6 months"));
		$months7 = date("Y-m", strtotime($period . " -7 months"));
		$months8 = date("Y-m", strtotime($period . " -8 months"));
		$this->truncateStarReport($months1);
		$members = $this->members();

		foreach ($members as $member) {
			$M1RBV = $M2RBV = $M3RBV = $M4RBV = $M5RBV = $M6RBV = $M7RBV = $M8RBV = 0;
			$M1LBV = $M2LBV = $M3LBV = $M4LBV = $M5LBV = $M6LBV = $M7LBV = $M8LBV = 0;
			$RightTeam = array();
			$LeftTeam = array();

			$Left = array();
			$Right = array();
			$downlines = $this->getNode($member['id']);
			foreach ($downlines as $downline) {
				if ($downline['matrix_side'] == "L") {
					$Left['id'] = $downline['id'];
				}
				if ($downline['matrix_side'] == "R") {
					$Right['id'] = $downline['id'];
				}
			}

			if (count($Left) > 0) {
				$LeftTeam = $this->getDownlineMemberWithRanks($Left['id'], 'L');
			}
			if (count($Right) > 0) {
				$RightTeam = $this->getDownlineMemberWithRanks($Right['id'], 'R');
			}

			$LeftIds = array_column($LeftTeam, 'id');
			$RightIds = array_column($RightTeam, 'id');

			if (count($Left) > 0) {
				$LeftIds[] = $Left['id'];
			}
			if (count($Right) > 0) {
				$RightIds[] = $Right['id'];
			}

			$member_binary['L'] = $LeftIds;
			$member_binary['R'] = $RightIds;

			if (count($member_binary['L']) > 0) {
				$M1LBV = (float)$this->getSalesofDesiredMonth($months1, $member_binary['L']);
				$M2LBV = (float)$this->getSalesofDesiredMonth($months2, $member_binary['L']);
				$M3LBV = (float)$this->getSalesofDesiredMonth($months3, $member_binary['L']);
				$M4LBV = (float)$this->getSalesofDesiredMonth($months4, $member_binary['L']);
				$M5LBV = (float)$this->getSalesofDesiredMonth($months5, $member_binary['L']);
				$M6LBV = (float)$this->getSalesofDesiredMonth($months6, $member_binary['L']);
				$M7LBV = (float)$this->getSalesofDesiredMonth($months7, $member_binary['L']);
				$M8LBV = (float)$this->getSalesofDesiredMonth($months8, $member_binary['L']);
			}
			if (count($member_binary['R']) > 0) {
				$M1RBV = (float)$this->getSalesofDesiredMonth($months1, $member_binary['R']);
				$M2RBV = (float)$this->getSalesofDesiredMonth($months2, $member_binary['R']);
				$M3RBV = (float)$this->getSalesofDesiredMonth($months3, $member_binary['R']);
				$M4RBV = (float)$this->getSalesofDesiredMonth($months4, $member_binary['R']);
				$M5RBV = (float)$this->getSalesofDesiredMonth($months5, $member_binary['R']);
				$M6RBV = (float)$this->getSalesofDesiredMonth($months6, $member_binary['R']);
				$M7RBV = (float)$this->getSalesofDesiredMonth($months7, $member_binary['R']);
				$M8RBV = (float)$this->getSalesofDesiredMonth($months8, $member_binary['R']);
			}

			$results['member_id'] = $member['id'];
			$results['email'] = $member['email'];
			$results['userid'] = $member['userid'];
			$results['f_name'] = $member['f_name'];
			$results['l_name'] = $member['l_name'];
			$results['rank1'] = $this->getRank($M1LBV, $M2LBV, $M3LBV, $M1RBV, $M2RBV, $M3RBV);

			$get_rnk = array("[[RANK_MEMBER]]" => 1, "[[RANK_1STAR]]" => 2, "[[RANK_2STAR]]" => 3, "[[RANK_3STAR]]" => 4, "[[RANK_SSTAR]]" => 6, "[[RANK_PEACOCK]]" => 5, "[[RANK_PHOENIX]]" => 7, "[[RANK_KIRIN]]" => 8, "[[RANK_UNICORN]]" => 9, "[[RANK_DRAGON]]" => 10);

			$results['rank'] = $get_rnk[$results['rank1']];
			$results['period'] = $months1;

			$results['rank2'] = $this->getRank($M2LBV, $M3LBV, $M4LBV, $M2RBV, $M3RBV, $M4RBV);
			$results['rank3'] = $this->getRank($M3LBV, $M4LBV, $M5LBV, $M3RBV, $M4RBV, $M5RBV);
			$results['rank4'] = $this->getRank($M4LBV, $M5LBV, $M6LBV, $M4RBV, $M5RBV, $M6RBV);
			$results['rank5'] = $this->getRank($M5LBV, $M6LBV, $M7LBV, $M5RBV, $M6RBV, $M7RBV);
			$results['rank6'] = $this->getRank($M6LBV, $M7LBV, $M8LBV, $M6RBV, $M7RBV, $M8RBV);

			$M1LBV = number_format($M1LBV, 2);
			$M2LBV = number_format($M2LBV, 2);
			$M3LBV = number_format($M3LBV, 2);
			$M4LBV = number_format($M4LBV, 2);
			$M5LBV = number_format($M5LBV, 2);
			$M6LBV = number_format($M6LBV, 2);
			$M1RBV = number_format($M1RBV, 2);
			$M2RBV = number_format($M2RBV, 2);
			$M3RBV = number_format($M3RBV, 2);
			$M4RBV = number_format($M4RBV, 2);
			$M5RBV = number_format($M5RBV, 2);
			$M6RBV = number_format($M6RBV, 2);

			$results['month1'] = $months1;
			$results['month2'] = $months2;
			$results['month3'] = $months3;
			$results['month4'] = $months4;
			$results['month5'] = $months5;
			$results['month6'] = $months6;
			$results['M1RBV'] = $M1RBV;
			$results['M2RBV'] = $M2RBV;
			$results['M3RBV'] = $M3RBV;
			$results['M4RBV'] = $M4RBV;
			$results['M5RBV'] = $M5RBV;
			$results['M6RBV'] = $M6RBV;
			$results['M1LBV'] = $M1LBV;
			$results['M2LBV'] = $M2LBV;
			$results['M3LBV'] = $M3LBV;
			$results['M4LBV'] = $M4LBV;
			$results['M5LBV'] = $M5LBV;
			$results['M6LBV'] = $M6LBV;
			$this->db->insert("star_report", $results);
		}
	}

	public function getRank($M1LBV, $M2LBV, $M3LBV, $M1RBV, $M2RBV, $M3RBV)
	{
		$rank = "[[RANK_MEMBER]]";
		if ($M1LBV > 10000 and $M1RBV > 10000) {
			$rank = "[[RANK_1STAR]]";
		}
		if ($M1LBV > 20000 and $M1RBV > 20000) {
			$rank = "[[RANK_2STAR]]";
		}
		if ($M1LBV > 40000 and $M1RBV > 40000) {
			$rank = "[[RANK_3STAR]]";
		}

		$Target = 120000;
		if ($M1LBV > $Target and $M1RBV > $Target and $M2LBV > $Target and $M2RBV > $Target and $M3LBV > $Target and $M3RBV > $Target) {
			$rank = "[[RANK_SSTAR]]";
		}

		$Target = 480000;
		if (($M1LBV + $M1RBV) > $Target and ($M2LBV + $M2RBV) > $Target and ($M3LBV + $M3RBV) > $Target) {
			$rank = "[[RANK_PEACOCK]]";
		}

		$Target = 960000;
		if (($M1LBV + $M1RBV) > $Target and ($M2LBV + $M2RBV) > $Target and ($M3LBV + $M3RBV) > $Target) {
			$rank = "[[RANK_PHOENIX]]";
		}

		$Target = 1440000;
		if (($M1LBV + $M1RBV) > $Target and ($M2LBV + $M2RBV) > $Target and ($M3LBV + $M3RBV) > $Target) {
			$rank = "[[RANK_KIRIN]]";
		}

		$Target = 2160000;
		if (($M1LBV + $M1RBV) > $Target and ($M2LBV + $M2RBV) > $Target and ($M3LBV + $M3RBV) > $Target) {
			$rank = "[[RANK_UNICORN]]";
		}

		$Target = 2880000;
		if (($M1LBV + $M1RBV) > $Target and ($M2LBV + $M2RBV) > $Target and ($M3LBV + $M3RBV) > $Target) {
			$rank = "[[RANK_DRAGON]]";
		}
		return $rank;
	}

	public function truncateStarReport($month)
	{
		$this->db->where('period', $month);
		$this->db->delete('star_report');
	}

	public function members()
	{
		$this->db->select("m.id, m.email, m.userid, m.f_name, m.l_name, m.rank, m.join_date");
		$query = $this->db->get('member m');
		return $query->result_array();
	}

	public function getNode($id)
	{
		$this->db->select("id, matrixid, matrix_side");
		$this->db->where("matrixid", $id);
		$this->db->order_by('matrixid', 'asc');
		$query = $this->db->get('member');
		return $query->result_array();
	}

	public function getSalesofDesiredMonth($period, $members)
	{
		$this->db->select("SUM(personal_sales) as BV");
		$this->db->group_start();
		$member_ids_chunk = array_chunk($members, 25);
		foreach ($member_ids_chunk as $member_ids) {
			$this->db->or_where_in("member_id", $member_ids);
		}
		$this->db->group_end();
		$this->db->like("period", $period);
		$query = $this->db->get("member_sales_daily");
		$result = $query->row_array();
		return isset($result['BV']) ? $result['BV'] : 0;
	}

	public function getDownlineMemberWithRanks($matrix_id, $position, $entry_date)
	{
		$groups = array();
		$this->db->select("m.id, m.rank");
		$this->db->where("m.matrixid", $matrix_id);
		$this->db->where("m.join_date <=", $entry_date);
		$query = $this->db->get('member m');
		$members = $query->result_array();
		foreach ($members as $member) {
			$downlines = $this->getDownlineMemberWithRanks($member['id'], $position, $entry_date);
			$groups = array_merge($groups, $downlines);
		}
		return array_merge($members, $groups);
	}

}
