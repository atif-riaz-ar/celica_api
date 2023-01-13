<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rank extends CI_Controller
{
	public function tally()
	{
		$user = $this->MemberModel->getMemberInfoByUserid($_POST['target']);
		$return['report'] = $this->RankModel->getDownlineRankTally($user['id']);
		$return['ranks'] = $this->RankModel->getAllRanks();
		return successReponse("", $return);
	}
}
