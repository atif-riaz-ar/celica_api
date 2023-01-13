<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Star extends CI_Controller
{

	public function refine()
	{
		$this->findRankTwo();
		$this->findRankThree();
	}

	public function findRankFive()
	{
		ini_set('max_execution_time', 0);
		$this->db->where('rank', 4);
		$this->db->where('(lbv+rbv) >=', 240000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		foreach ($result as $record) {
			$left_team = $record['left_downline'];
			$right_team = $record['right_downline'];
			$left_count = $this->find_target($left_team, 4);
			$right_count = $this->find_target($right_team, 4);
			if ($left_count > 0 and $right_count > 0) {
				$this->updateFive($record);
			}
		}
	}

	public function updateFive($star)
	{
		ini_set('max_execution_time', 0);
		$period = $p[] = $star['period'];
		$p[] = date('Y-m', strtotime("+1 month", strtotime($period)));
		$p[] = date('Y-m', strtotime("+2 month", strtotime($period)));
		$this->db->where('member_id', $star['member_id']);
		$this->db->where_in('period', $p);
		$this->db->where('rank', 4);
		$this->db->where('(lbv+rbv) >=', 240000);
		$query = $this->db->get('star_helper');
		$result = $query->num_rows();
		if ($result >= 3) {
			$this->updateRank(5, array($star['member_id']));
		}
	}

	public function findRankSix()
	{
		ini_set('max_execution_time', 0);
		$this->db->where('rank', 5);
		$this->db->where('(lbv+rbv) >=', 480000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		foreach ($result as $record) {
			$team = $record['left_downline'] . "," . $record['right_downline'];
			$count = $this->find_target($team, 5);
			if ($count > 1) {
				$this->updateSix($record);
			}
		}
	}

	public function updateSix($star)
	{
		ini_set('max_execution_time', 0);
		$period = $p[] = $star['period'];
		$p[] = date('Y-m', strtotime("+1 month", strtotime($period)));
		$p[] = date('Y-m', strtotime("+2 month", strtotime($period)));
		$this->db->where('member_id', $star['member_id']);
		$this->db->where_in('period', $p);
		$this->db->where('rank', 5);
		$this->db->where('(lbv+rbv) >=', 480000);
		$query = $this->db->get('star_helper');
		$result = $query->num_rows();
		if ($result >= 3) {
			$this->updateRank(6, array($star['member_id']));
		}
	}

	public function findRankFour()
	{
		ini_set('max_execution_time', 0);
//		$this->db->where('member_id', $member['id']);
//		$this->db->where('period', $period);
		$this->db->where('rank', 3);
		$this->db->where('(lbv+rbv) >=', 80000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		$to_update = array();
		foreach ($result as $record) {
			$left_team = $record['left_downline'];
			$right_team = $record['right_downline'];
			$left_count = $this->find_target($left_team, 3);
			$right_count = $this->find_target($right_team, 3);
			if ($left_count > 0 and $right_count > 0) {
				$to_update[] = $record['id'];
			}
		}
		$this->updateRank(4, $to_update);
	}

	public function findRankThree()
	{
		ini_set('max_execution_time', 0);
//		$this->db->where('member_id', $member['id']);
//		$this->db->where('period', $period);
		$this->db->where('rank', 2);
		$this->db->where('(lbv+rbv) >=', 40000);
		$query = $this->db->get('star_helper');
		$result = $query->result_array();
		$to_update = array();
		foreach ($result as $record) {
			$left_team = $record['left_downline'];
			$right_team = $record['right_downline'];
			$left_count = $this->find_target($left_team, 2);
			$right_count = $this->find_target($right_team, 2);
			if ($left_count > 0 and $right_count > 0) {
				$to_update[] = $record['id'];
			}
		}
		$this->updateRank(3, $to_update);
	}

	public function findRankTwo()
	{
//		$this->db->where('member_id', $member['id']);
//		$this->db->where('period', $period);
		$this->db->where('lbv >=', 10000);
		$this->db->where('rbv >=', 10000);
		$this->db->update('star_helper', array(
			"rank" => 2
		));
	}

	public function findRankOne($period)
	{
//		$period = date("Y-m");
		$entry_date = date("Y-m-t", strtotime($period)) . " 23:59:59";
		$members = $this->members();

		foreach ($members as $member) {
			$RightTeam = array();
			$LeftTeam = array();

			$Left = array();
			$Right = array();
			$downlines = $this->getNode($member['id'], $entry_date);
			foreach ($downlines as $downline) {
				if ($downline['matrix_side'] == "L") {
					$Left['id'] = $downline['id'];
				}
				if ($downline['matrix_side'] == "R") {
					$Right['id'] = $downline['id'];
				}
			}

			if (count($downlines) > 0) {
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
				$post['rank'] = 1;
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
	}


	public function members()
	{
		$this->db->select("m.id, m.email, m.userid, m.f_name, m.l_name, m.rank, m.join_date");
		$query = $this->db->get('member m');
		return $query->result_array();
	}

	public function getNode($id, $entry_date)
	{
		$this->db->select("id, matrixid, matrix_side");
		$this->db->where("matrixid", $id);
		$this->db->where("join_date <=", $entry_date);
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

	public function find_target($members, $rank)
	{
		$query = $this->db->query("SELECT * FROM star_helper WHERE rank ='$rank' AND member_id in ($members)");
		return $query->num_rows();
		$this->db->where('rank', $rank);
		$this->db->where_in("member_id", $members);
		$query = $this->db->get('star_helper');
		return $query->num_rows();
	}

	public function updateRank($rank, $to_update)
	{
		if (count($to_update) < 1) {
			return false;
		}
		$this->db->where_in("id", $to_update);
		$this->db->update('star_helper', array("rank" => $rank));
	}

	public function process()
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$array = array(
//			"2019-08",
//			"2019-09",
//			"2019-10",
//			"2019-11",
//			"2019-12",
//			"2020-01",
//			"2020-02",
//			"2020-03",
//			"2020-04",
//			"2020-05",
//			"2020-06",
//			"2020-07",
//			"2020-08",
//			"2020-09",
//			"2020-10",
//			"2020-11",
//			"2020-12",
			"2021-01",
//			"2021-02",
//			"2021-03",
//			"2021-04",
//			"2021-05",
//			"2021-06",
//			"2021-07",
//			"2021-08",
//			"2021-09",
//			"2021-10",
//			"2021-11",
//			"2021-12",
//			"2022-01",
//			"2022-02",
//			"2022-03",
			"2022-04",
//			"2022-05",
//			"2022-06",
//			"2022-07",
//			"2022-08",
//			"2022-09",
//			"2022-10",
//			"2022-11",
//			"2022-12",
			"2023-01"
		);
		$members = $this->MemberModel->getAllMembers();
		foreach ($array as $date) {
			$this->transfer($date, $members);
		}
	}

	public function transfer($p, $members)
	{
		$period = ($p == '' ? date("Y-m") : $p);
		$m1 = $m['1'] = $period;
		$m2 = $m['2'] = date("Y-m", strtotime($period . " -1 months"));
		$m3 = $m['3'] = date("Y-m", strtotime($period . " -2 months"));
		$m4 = $m['4'] = date("Y-m", strtotime($period . " -3 months"));
		$m5 = $m['5'] = date("Y-m", strtotime($period . " -4 months"));
		$m6 = $m['6'] = date("Y-m", strtotime($period . " -5 months"));

		foreach ($members as $key => $member) {
			$results = $this->getStars($m, $member['id']);
			$rnk1 = $rnk2 = $rnk3 = $rnk4 = $rnk5 = $rnk6 = 1;
			$lbv1 = $lbv2 = $lbv3 = $lbv4 = $lbv5 = $lbv6 = 0;
			$rbv1 = $rbv2 = $rbv3 = $rbv4 = $rbv5 = $rbv6 = 0;
			if (count($results) > 0) {
				foreach ($results as $r){
					if($r['period'] == $m1){ $rnk1 = $r['rank']; $lbv1 = $r['lbv']; $rbv1 = $r['rbv']; }
					if($r['period'] == $m2){ $rnk2 = $r['rank']; $lbv2 = $r['lbv']; $rbv2 = $r['rbv']; }
					if($r['period'] == $m3){ $rnk3 = $r['rank']; $lbv3 = $r['lbv']; $rbv3 = $r['rbv']; }
					if($r['period'] == $m4){ $rnk4 = $r['rank']; $lbv4 = $r['lbv']; $rbv4 = $r['rbv']; }
					if($r['period'] == $m5){ $rnk5 = $r['rank']; $lbv5 = $r['lbv']; $rbv5 = $r['rbv']; }
					if($r['period'] == $m6){ $rnk6 = $r['rank']; $lbv6 = $r['lbv']; $rbv6 = $r['rbv']; }
				}
			}
			$rnk_arr = array(1 => "[[RANK_MEMBER]]", 2 => "[[RANK_1STAR]]", 3 => "[[RANK_2STAR]]", 4 => "[[RANK_3STAR]]", 6 => "[[RANK_SSTAR]]", 5 => "[[RANK_PEACOCK]]", 7 => "[[RANK_PHOENIX]]", 8 => "[[RANK_KIRIN]]", 9 => "[[RANK_UNICORN]]", 10 => "[[RANK_DRAGON]]");
			$final['period'] = $m1;
			$final['member_id'] = $member['id'];
			$final['email'] = $member['email'];
			$final['userid'] = $member['userid'];
			$final['f_name'] = $member['f_name'];
			$final['l_name'] = $member['l_name'];
			$final['rank'] = $rnk1;
			$final['rank1'] = $rnk_arr[$rnk1];
			$final['rank2'] = $rnk_arr[$rnk2];
			$final['rank3'] = $rnk_arr[$rnk3];
			$final['rank4'] = $rnk_arr[$rnk4];
			$final['rank5'] = $rnk_arr[$rnk5];
			$final['rank6'] = $rnk_arr[$rnk6];
			$final['month1'] = $m1 . "__" . $rbv1 . "__" . $lbv1;
			$final['month2'] = $m2 . "__" . $rbv2 . "__" . $lbv2;
			$final['month3'] = $m3 . "__" . $rbv3 . "__" . $lbv3;
			$final['month4'] = $m4 . "__" . $rbv4 . "__" . $lbv4;
			$final['month5'] = $m5 . "__" . $rbv5 . "__" . $lbv5;
			$final['month6'] = $m6 . "__" . $rbv6 . "__" . $lbv6;
			$this->db->insert("star_report", $final);
		}
//		$this->db->insert_batch('star_report', $final);
	}

	public function getStars($m, $id)
	{
		$this->db->where("member_id", $id);
		$this->db->where_in("period", $m);
		$query = $this->db->get("star_helper");
		return $query->result_array();
	}
}
