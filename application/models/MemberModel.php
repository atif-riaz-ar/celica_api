<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MemberModel extends CI_Model
{
	function getAdminInfoByIdLogin($user_key)
	{
		$this->db->select("admin.*, admin_category.access_list");
		$this->db->where("admin.id", $user_key);
		$this->db->or_where("admin.login", $user_key);
		$this->db->join('admin_category', 'admin_category.id = admin.group_id');
		$query = $this->db->get('admin');
		return $query->row_array();
	}

	function getMemberInfoByIdUseridEmail($user_key)
	{
		$this->db->where("id", $user_key);
		$this->db->or_where("userid", $user_key);
		$this->db->or_where("email", $user_key);
		$query = $this->db->get('member');
		return $query->row_array();
	}

	function getMemberInfoByUserid($userid)
	{
		$this->db->where("userid", $userid);
		$query = $this->db->get('member');
		return $query->row_array();
	}

	function getMemberInfoById($userid)
	{
		$this->db->where("id", $userid);
		$query = $this->db->get('member');
		return $query->row_array();
	}

	function getMemberInfoByToken($access_token)
	{
		$this->db->select("member.* , country_list.full_name, country_list.short_name, country_list.code, 
		country_list.currency, country_list.language_id");
		$this->db->where("member.access_token", $access_token);
		$this->db->join('country_list', 'country_list.id = member.country');
		$query = $this->db->get('member');
		return $query->row_array();
	}

	function updateMemberInfo($id, $posts)
	{
		$arr = new stdClass();
		foreach ($posts as $key => $post) {
			$arr->$key = $post;
		}
		return $this->db->update('member', $arr, array('id' => $id));
	}

	function getSubAccounts($user_id)
	{
		$this->db->where("main_acct_id", $user_id);
		$query = $this->db->get('member');
		return $query->result_array();
	}

	function checkSubAccount($user_id)
	{
		$this->db->where("main_acct_id", $user_id);
		$query = $this->db->get('member');
		return $query->result_array();
	}

	function getMemberByMatrixId($user_id)
	{
		$this->db->where("matrixid", $user_id);
		$query = $this->db->get('member');
		return $query->result_array();
	}

	function getMemberByMatrixIdSide($user_id, $side)
	{
		$this->db->where("matrix_side", $side);
		$this->db->where("matrixid", $user_id);
		$query = $this->db->get('member');
		return $query->row_array();
	}

	function getSponsored($user_id)
	{
		$this->db->where("sponsorid", $user_id);
		$query = $this->db->get('member');
		return $query->result_array();
	}

	function getAllMembers()
	{
		$this->db->select('
			m.id, m.userid, m.f_name, m.l_name, m.mobile, m.email,
			spn.userid as sponsor_name,
			mtr.userid as matrix_name,
			mr.name as rank_name,
			p.name as package_name
		');
		$this->db->join('product p', 'p.id = m.package_id', 'left');
		$this->db->join('member_rank mr', 'mr.id = m.rank', 'left');
		$this->db->join('member mtr', 'mtr.id = m.matrixid', 'left');
		$this->db->join('member spn', 'spn.id = m.sponsorid', 'left');
//		$this->db->limit(50);
		$query = $this->db->get('member m');
		return $query->result_array();
	}

	function getMemberDetailsByUserid($userid)
	{
		$this->db->select('
			m.id, m.userid, m.f_name, m.l_name, m.mobile, m.email, m.can_withdraw, m.referral_side,
			spn.userid as sponsor_name,
			rfr.userid as rfr_name,
			c.full_name as country_name, c.short_name as country_short_name, c.code as country_code,
			spn.userid as sponsor_name,
			spn.userid as sponsor_name,
			ms.accu_group_sales, ms.accu_personal_sales, ms.accu_direct_sales,
			mtr.userid as matrix_name,
			mr.name as rank_name,
			p.name as package_name
		');
		$this->db->join('product p', 'p.id = m.package_id', 'left');
		$this->db->join('member_rank mr', 'mr.id = m.rank', 'left');
		$this->db->join('member mtr', 'mtr.id = m.matrixid', 'left');
		$this->db->join('member spn', 'spn.id = m.sponsorid', 'left');
		$this->db->join('member rfr', 'rfr.id = m.referral_placement', 'left');
		$this->db->join('member_sales ms', 'ms.member_id = m.id', 'left');
		$this->db->join('country_list c', 'c.id = m.country', 'left');
		$this->db->where('m.userid', $userid);
		$query = $this->db->get('member m');
		$user = $this->getMemberInfoByUserid($userid);
		$data['member_detail'] = $query->row_array();
		$data['reports'] = $this->ReportModel->dashboard($user, getMonth());
		$data['banks'] = $this->BankModel->getMemberBanks($user['id']);
		return $data;
	}

	function getMemberWithPackageName($userid, $type)
	{
		if(empty($userid)){
			return array();
		}
		$query = $this->db->query("
		SELECT 
			a.id, a.sponsorid, a.matrixid, a.userid, a.f_name, a.l_name, a.rank, a.account_status,
			b.name as package_name, b.img_file as package_image, c.name as rank_name, 
			d.short_name as country_short, count(s.id) as total_count 
		FROM member a 
		    LEFT JOIN member s ON a.id=s.sponsorid 
		    LEFT JOIN product b ON a.package_id=b.id 
		    LEFT JOIN member_rank c ON a.rank=c.id 
		    LEFT JOIN country_list d ON a.country=d.id 
		WHERE a.$type = $userid 
		GROUP BY a.id;");
		return $query->result_array();
	}

	function getMemberWithRankDetail($user_id)
	{
		$this->db->select("member.id, member.userid, member.f_name, member.l_name, member.rank, member_rank.name as rank_name, member.email, member.mobile, member.join_date, member.account_status");
		$this->db->where("sponsorid", $user_id);
		$this->db->join('member_rank', 'member_rank.id = member.rank');
		$query = $this->db->get('member');
		return $query->result_array();
	}

	function getMemberSummary($period, $main=false)
	{
		if($main == true) {
			$this->db->where("main_acct_id", 0);
		}
		$this->db->like("join_date", $period);
		$query = $this->db->get('member');
		return $query->num_rows();
	}
}
