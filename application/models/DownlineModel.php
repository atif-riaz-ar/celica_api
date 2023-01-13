<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DownlineModel extends CI_Model
{
	public function getDownlines($user_id, $period)
	{
		$prev_period = date('Y-m', strtotime("-1 month", strtotime($period)));
		$prev_prev_period = date('Y-m', strtotime("-2 month", strtotime($period)));
		$member = $this->MemberModel->getMemberInfoByIdUseridEmail($user_id);
		$higher_rank = $this->RankModel->getRankById($member['rank'] + 1);
		$downlines['left'] = array();
		$downlines['right'] = array();
		$downs = $this->getDownlinesRankStarted($user_id, $higher_rank['required_rank']);
		if (isset($downs['left'])) {
			$downlines['left'] = $downs['left'];
		}
		if (isset($downs['right'])) {
			$downlines['right'] = $downs['right'];
		}
		$total_left = 0;
		$total_right = 0;
		$prev_total = 0;
		$prev_prev_total = 0;

		$total_left += count($downlines['left']);
		$total_right += count($downlines['right']);
		$left_arr = array();
		$right_arr = array();
		foreach ($downlines['left'] as $l) {
			$left_arr[$l['id']] = $l['rank'];
		}
		foreach ($downlines['right'] as $r) {
			$right_arr[$r['id']] = $r['rank'];
		}

		$pmem = array_merge($left_arr, $right_arr);
		$ppmem = array_merge($left_arr, $right_arr);

		if (count($pmem) > 0) {
			$ss = $this->RankModel->getUserRankInfo($pmem, $higher_rank['required_rank'], $prev_period);
			if (!empty($ss)) {
				$prev_total += count($ss);
			}
		}
		if (count($ppmem) > 0) {
			$rr = $this->RankModel->getUserRankInfo($ppmem, $higher_rank['required_rank'], $prev_prev_period);
			if (!empty($rr)) {
				$prev_prev_total += count($rr);
			}
		}

		$response = array("left" => $total_left,
			"right" => $total_right,
			"total" => $total_left + $total_right,
			"prev_total" => $prev_total,
			"prev_prev_total" => $prev_prev_total
		);
		return $response;
	}

	public function getDownlinesRankStarted($user_id, $required_rank)
	{
		$data = array();
		$this->db->where("matrixid", $user_id);
		$this->db->where("rank >", $required_rank);
		$query = $this->db->get('member');
		$members = $query->result_array();
		foreach ($members as $row) {
			if ($row['matrix_side'] == "L") {
				$downlines = $this->getAllDownlinesRank($row['id']);
				$downlines[] = array("id" => $row['id'],
					"rank" => $row['rank']
				);
				$data['left'] = $downlines;
			}
			if ($row['matrix_side'] == "R") {
				$downlines = $this->getAllDownlinesRank($row['id']);
				$downlines[] = array("id" => $row['id'],
					"rank" => $row['rank']
				);
				$data['right'] = $downlines;
			}
		}
		return $data;
	}

	public function getAllDownlinesRank($user_id)
	{
		$this->db->select("id, userid, rank, sponsorid, matrixid, matrix_side, main_acct_id, f_name, l_name, mobile, country, referral_placement, referral_side");
		$this->db->where("matrixid", $user_id);
		$query = $this->db->get('member');
		$members = $query->result_array();
		foreach ($members as $row) {
			$downlines = $this->getAllDownlinesRank($row['id']);
			$members = array_merge($members, $downlines);
		}
		return $members;
	}

	public function checkDownlineRelationship($upline_id, $downline_id)
	{
		$member = $this->MemberModel->getMemberInfoByIdUseridEmail($downline_id);
		if ($member['matrixid'] == $upline_id) {
			return true;
		} else if ($member['matrixid'] == 0) {
			return false;
		} else {
			return $this->checkDownlineRelationship($upline_id, $member['matrixid']);
		}
	}

	public function getPlacement($post)
	{
//		$desired = $this->MemberModel->getMemberInfoByIdUseridEmail($post['matrix']);
//		if ($post['side'] == "A") {
//			$auto = $this->getAutoPlacement($desired['id']);
//			$placement = $auto['pid'];
//		} else {
//			$placement = $this->getCorrectPlacement($desired['id'], $post['side']);
//		}
//		$available = $this->MemberModel->getMemberInfoById($placement);
		$return['desired'] = $post['matrix'];
		$return['available'] = $post['matrix'];
		$return['side'] = $post['side'];
		return $return;
	}

	public function getCorrectPlacement($matrixid, $side)
	{
		$_start = $matrixid;
		while (true) {
			$this->db->where('matrixid', $_start);
			$this->db->where('matrix_side', $side);
			$query = $this->db->get('member');
			$row = $query->row_array();
			if (!isset($row['id'])) return $_start;
			$_start = $row['id'];
		}
		return $_start;
	}


	public function getAutoPlacement($member_id)
	{
		$ids = array($member_id);
		$downline = $this->getAutos($ids);
		return $downline;
	}


	public function getAutos($ids)
	{
		$str = implode("','", $ids);
		$this->db->select("id, matrixid, matrix_side");
		$this->db->where_in("matrixid", $str);
		$this->db->order_by('matrixid', 'asc');
		$query = $this->db->get('member');
		$downlines = $query->result_array();

		$new_ids = array();
		$triggered = false;
		foreach ($ids as $x) {
			$data[$x] = array();
		}
		foreach ($downlines as $row) {
			$triggered = true;
			if ($row['matrix_side'] == "L") {
				$data[$row['matrixid']]['left'] = $row['matrix_side'];
			}
			if ($row['matrix_side'] == "R") {
				$data[$row['matrixid']]['right'] = $row['matrix_side'];
			}
			$new_ids[$row['id']] = $row['id'];
		}

		foreach ($data as $id => $side) {
			if (count($side) < 2) {
				$return['pside'] = isset($side['left']) ? "R" : "L";
				$return['pid'] = $id;
				return $return;
			}
		}
		if ($triggered == false) {
			$data['pid'] = $ids[0];
			$data['pside'] = 'L';
			return $data;
		}
		return $this->getAutos(array_values($new_ids));
	}

	public function getDownlineMembers($matrix_id)
	{
		$groups = array();
		$this->db->select("m.id");
		$this->db->where("m.matrixid", $matrix_id);
		$query = $this->db->get('member m');
		$members = $query->result_array();
		foreach ($members as $member) {
			$downlines = $this->getDownlineMembers($member['id']);
			$groups = array_merge($groups, $downlines);
		}
		$groups = array_merge($members, $groups);
		return $groups;
	}

}
