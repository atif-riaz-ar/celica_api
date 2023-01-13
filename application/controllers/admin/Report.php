<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{
	public function summary()
	{
		$report = $this->ReportModel->getSummary($_POST['period']);
		return successReponse("", $report);
	}

	public function yearly()
	{
		$end_year = "2018";
		$the_year = date("Y");
		while ($the_year >= $end_year) {
			$report[$the_year] = $this->ReportModel->getYearlySummary($the_year);
			$the_year--;
		}
		return successReponse("", $report);
	}

	public function year()
	{

		for ($the_month = 1; $the_month <= 12; $the_month++) {
			if ($the_month < 10) {
				$the_month = "0" . $the_month;
			}
			$period = $_POST['year'] . "-" . $the_month;
			$report[$period] = $this->ReportModel->getYearlySummary($period);
		}
		return successReponse("", $report);
	}

	public function month()
	{

		for ($the_date = 1; $the_date <= date("t"); $the_date++) {
			if ($the_date < 10) {
				$the_date = "0" . $the_date;
			}
			$period = $_POST['month'] . "-" . $the_date;
			$report[$period] = $this->ReportModel->getYearlySummary($period);
		}
		return successReponse("", $report);
	}

	public function commissions()
	{
		$period = $_POST['period'];
		$type = $_POST['type'];
		$report = $this->CommissionModel->getCommissionByPeriod($period, $type);
		return successReponse("", $report);
	}

	public function sales()
	{
		$period = $_POST['period'];
		$report = $this->SaleModel->getSaleByPeriod($period);
		return successReponse("", $report);
	}

	public function star()
	{
		$report = $this->StarModel->getStarReport($_POST['fl_user'], $_POST['fl_rank'], $_POST['period']);
		return successReponse("", $report);
	}

	public function ledger()
	{
		$report = $this->ReportModel->admin_ledger($_POST, getMonth());
		return successReponse("", $report);
	}
}
