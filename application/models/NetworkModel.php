<?php
defined('BASEPATH') or exit('No direct script access allowed');

class NetworkModel extends CI_Model
{
	public function getPlacementTree($upline_id, $type)
	{

		$data = array();
		$members = $this->MemberModel->getMemberWithPackageName($upline_id, $type);
		return $members;
		foreach ($members as $member) {
			$downlines = $this->getPlacementTree($member['id'], $type);
			$data[] = array("member" => $member,
				"downlines" => $downlines);
		}
		return $data;
	}

	public function getBinary($user, $target)
	{
		if ($target != "") {
			$target_user = $this->MemberModel->getMemberInfoByIdUseridEmail($target);
			if (isset($target_user['id'])) {
				if (!$this->DownlineModel->checkDownlineRelationship($user['id'], $target_user['id'])) {
					$_SESSION['warning'] = "[[LABEL_NOT_IN_DOWNLINE]]";
					$target_user = $user;
				}
			} else {
				$target_user = $user;
			}
		} else {
			$target_user = $user;
		}
		$report = $this->getBinaryNetwork($target_user['id']);
		return successReponse("", json_decode(trans(json_encode($report), $_POST['language'])));
	}

	public function getSponsoredTree($user, $target)
	{
		if ($target != "") {
			$target_user = $this->MemberModel->getMemberInfoByIdUseridEmail($target);
			if (isset($target_user['id'])) {
				if (!$this->DownlineModel->checkDownlineRelationship($user['id'], $target_user['id'])) {
					$_SESSION['warning'] = "[[LABEL_NOT_IN_DOWNLINE]]";
					$target_user = $user;
				}
			} else {
				$target_user = $user;
			}
		} else {
			$target_user = $user;
		}
		$report = $this->NetworkModel->getSponsorNetwork($target_user['id']);
		return successReponse("", $report);
	}

	public function getSponsorNetwork($user_id)
	{
		$data = array();
		$members = $this->MemberModel->getMemberWithRankDetail($user_id);
		foreach ($members as $member) {
			$member['sales'] = $this->SaleModel->getMemberSaleDetails($member['id']);
			$member['sponsored'] = count($this->MemberModel->getSponsored($member['id']));
			$data[] = $member;
		}
		return $data;
	}

	public function getBinaryNetwork($user_id)
	{
		$data = array();
		$top = $this->MemberModel->getMemberInfoByIdUseridEmail($user_id);
		$data['top']['upline_id'] = $top['matrixid'];
		$data['top']['data'] = $this->SaleModel->getBv($user_id);

		$loops = array(1, 2, 1, 4, 1, 8);
		$data = $this->level_2($data, $user_id, $top, $loops, 1, "L");

		$loops = array(3, 4, 5, 8, 9, 16);
		$data = $this->level_2($data, $user_id, $top, $loops, 2, "R");
		return $data;
	}


	public function level_2($data, $member_id, $top, $loops, $col, $position)
	{
		$mem = $this->MemberModel->getMemberByMatrixIdSide($member_id, $position);
		if (isset($mem['id'])) {
			$bv = $this->SaleModel->getBv($mem['id']);
			$data['level2'][] = array("type" => "person", "userid" => $mem['userid'], "col" => $col, "data" => $bv);

			$loop = array(1, 4, 1, 8);
			$newcol = ($col % 2 == 0 ? $col : $col - 1);
			$data = $this->level_3($data, $mem, $loop, $newcol + 1, "L");

			$loop = array(5, 8, 9, 16);
			$data = $this->level_3($data, $mem, $loop, $newcol + 2, "R");

		} else {
			$data['level2'][] = array("type" => "blank", "col" => $col, "upline_userid" => $top['userid'], "upline_side" => "$position");
			for ($i = $loops[0]; $i <= $loops[1]; $i++) {
				$data['level3'][] = array("type" => "empty", "col" => $i);
			}
			for ($i = $loops[2]; $i <= $loops[3]; $i++) {
				$data['level4'][] = array("type" => "empty", "col" => $i);
			}
			for ($i = $loops[4]; $i <= $loops[5]; $i++) {
				$data['level5'][] = array("type" => "empty", "col" => $i);
			}
		}
		return $data;
	}

	public function level_3($data, $member2_1, $loops, $col, $position)
	{

		$col = isset($data['level3'])? count($data['level3']) + 1 : 1;
		$member3_1 = $this->MemberModel->getMemberByMatrixIdSide($member2_1['id'], $position);
		if (isset($member3_1['id'])) {
			$bv = $this->SaleModel->getBv($member3_1['id']);
			$data['level3'][] = array("type" => "person", "userid" => $member3_1['userid'], "col" => $col, "data" => $bv);

			$loop = array(1, 8);
			$data = $this->level_4($data, $member3_1, $loop, $col, "L");

			$loop = array(9, 16);
			$data = $this->level_4($data, $member3_1, $loop, $col, "R");

		} else {
			$data['level3'][] = array("type" => "blank", "col" => $col, "upline_userid" => $member2_1['userid'], "upline_side" => "$position");
			for ($i = $loops[0]; $i <= $loops[1]; $i++) {
				$data['level4'][] = array("type" => "empty", "col" => $i);
			}
			for ($i = $loops[2]; $i <= $loops[3]; $i++) {
				$data['level5'][] = array("type" => "empty", "col" => $i);
			}
		}
		return $data;
	}

	public function level_4($data, $member2_1, $loops, $col, $position)
	{
		$col = isset($data['level4'])? count($data['level4']) + 1 : 1;
		$member3_1 = $this->MemberModel->getMemberByMatrixIdSide($member2_1['id'], $position);
		if (isset($member3_1['id'])) {
			$bv = $this->SaleModel->getBv($member3_1['id']);
			$data['level4'][] = array("type" => "person", "userid" => $member3_1['userid'], "col" => $col, "data" => $bv);


			$data = $this->level_5($data, $member3_1, array(), $col, "L");

			$data = $this->level_5($data, $member3_1, array(), $col, "R");

		} else {
			$data['level4'][] = array("type" => "blank", "col" => $col, "upline_userid" => $member2_1['userid'], "upline_side" => "$position");
			for ($i = $loops[0]; $i <= $loops[1]; $i++) {
				$data['level5'][] = array("type" => "empty", "col" => $i);
			}
		}
		return $data;
	}

	public function level_5($data, $member2_1, $loops, $col, $position)
	{
		$col = isset($data['level5'])? count($data['level5']) + 1 : 1;
		$member3_1 = $this->MemberModel->getMemberByMatrixIdSide($member2_1['id'], $position);
		if (isset($member3_1['id'])) {
			$bv = $this->SaleModel->getBv($member3_1['id']);
			$data['level5'][] = array("type" => "person", "userid" => $member3_1['userid'], "col" => $col, "data" => $bv);

		} else {
			$data['level5'][] = array("type" => "blank", "col" => $col, "upline_userid" => $member2_1['userid'], "upline_side" => "$position");
		}
		return $data;
	}
}
