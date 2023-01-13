<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RankModel extends CI_Model
{
	public function getTallyReport($userid)
	{

	}

	public function getUserRankByMonth($user_id, $month)
	{
		$this->db->where("member_id", $user_id);
		$this->db->where("period", $month);
		$this->db->join('member_rank', 'member_rank.id = star_report.rank');
		$query = $this->db->get('star_report');
		return $query->row_array();
	}

	public function getRankById($rank)
	{
		$this->db->where("id", $rank);
		$query = $this->db->get('member_rank');
		return $query->row_array();
	}

	public function getAllRanks()
	{
		$query = $this->db->get('member_rank');
		return $query->result_array();
	}

	public function getUserRankInfo($ids, $rank, $period)
	{
		$this->db->where("month", $period);
		$this->db->where("rank >", $rank);
		$this->db->where_in("member_id", $ids);
		$query = $this->db->get('star_report');
		return $query->row_array();
	}

	public function getRankList($post)
	{
		$member = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		$return['ranks'] = $this->getAllRanks();
		$return['rank_tally'] = $this->getDownlineRankTally($member['id']);
		return $return;
	}

	public function getDownlineRankTally($member_id)
	{
		$downlines = $this->MemberModel->getMemberByMatrixId($member_id);
		foreach ($downlines as $row) {
			$rank = $row['rank'];
			if ($row['matrix_side'] == 'L') {
				$data['L'] = $this->getDownlineRankTallySub($row['id']);
				if (isset($data['L'][$rank])) {
					$data['L'][$rank]++;
				} else {
					$data['L'][$rank] = 1;
				}
			}
			if ($row['matrix_side'] == 'R') {
				$data['R'] = $this->getDownlineRankTallySub($row['id']);
				if (isset($data['R'][$rank])) {
					$data['R'][$rank]++;
				} else {
					$data['R'][$rank] = 1;
				}
			}
		}
		return $data;
	}

	public function getDownlineRankTallySub($member_id) {
		$tally = array();
		$downlines = $this->MemberModel->getMemberByMatrixId($member_id);
		foreach ($downlines as $row)
		{
			$rank = $row['rank'];
			if (isset($tally[$rank])) $tally[$rank]++;
			else $tally[$rank] = 1;
			$temp_tally = $this->getDownlineRankTallySub($row['id']);
			for ($i=0; $i<12; $i++)
			{
				if (isset($temp_tally[$i])) {
					if (isset($tally[$i])) $tally[$i] += $temp_tally[$i];
					else $tally[$i] = $temp_tally[$i];
				}
			}
		}
		return $tally;
	}


}
