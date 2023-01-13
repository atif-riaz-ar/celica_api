<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{

	public function dashboard()
	{
		$user = $this->MemberModel->getMemberInfoByToken($_POST['access_token']);
		$report = $this->ReportModel->dashboard($user, getMonth());
		return successReponse("", $report);
	}

	public function ledger()
	{
		$report = $this->ReportModel->ledger($_POST, getMonth());
		return successReponse("", $report);
	}

	public function withdrawal()
	{
		$report = $this->ReportModel->withdrawal($_POST);
		return successReponse("", $report);
	}

	public function commission_summary()
	{
		$report = $this->ReportModel->commission_summary($_POST);
		return successReponse("", $report);
	}

	public function commission($type, $period)
	{
		$report = $this->ReportModel->commission_details($_POST, $type, $period);
		return successReponse("", $report);
	}

	public function ranks()
	{
		$report = $this->RankModel->getRankList($_POST);
		return successReponse("", $report);
	}
}
