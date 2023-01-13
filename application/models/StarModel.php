<?php
defined('BASEPATH') or exit('No direct script access allowed');

class StarModel extends CI_Model
{
	public function getStarReport($userid, $rank, $period)
	{
		if ($period != "") {
			$this->db->like('month1', $period);
		}

		if ($userid != "") {
			$this->db->where('userid', $userid);
		}

		if ($rank == "any") {
			$this->db->group_start();
			$this->db->where("(rank1 like '%star%' or rank2 like '%star%' or rank3 like '%star%' or rank4 like '%star%' or rank5 like '%star%' or rank6 like '%star%')");
			$this->db->group_end();
		}

		$query = $this->db->get('star_report');
		return $query->result_array();
	}
}
