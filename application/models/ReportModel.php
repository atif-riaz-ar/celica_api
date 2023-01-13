<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportModel extends CI_Model
{
	public function withdrawal($post)
	{
		$member = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		$return = $this->CashBankModel->getWithdrawals($member['id']);
		return $return;
	}

	public function commission_summary($post)
	{
		$member = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		$period = isset($post['selPeriod'])?$post['selPeriod'] : getMonth();
		$return = $this->CommissionModel->getCommissions($member['id'], $period);
		return $return;
	}

	public function commission_details($post, $type, $period)
	{
		$member = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		$return = $this->CommissionModel->getCommissionByTypePeriod($member['id'], $type, $period);
		return $return;
	}

	public function ledger($post)
	{
		$currency = isset($post['currency']) ? $post['currency'] : "CC";
		$start_time = isset($post['selyrfrom']) && isset($post['selmonfrom']) ? $post['selyrfrom'] . '-' . $post['selmonfrom'] . '-01 00:00:00' : date("Y-m-01 00:00:00");
		$end_time = isset($post['selyrto']) && isset($post['selmonto']) ? date('Y-m-t', strtotime($post['selyrto'] . '-' . $post['selmonto'] . '-01')) . " 23:59:59" : date("Y-m-t 23:59:59");
		$member = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		$return = $this->LedgerModel->getLedger($member['id'], $currency, $start_time, $end_time);
		return $return;
	}

	public function admin_ledger($post)
	{
		$currency = isset($post['currency']) ? $post['currency'] : "CC";
		$start_time = isset($post['selyrfrom']) && isset($post['selmonfrom']) ? $post['selyrfrom'] . '-' . $post['selmonfrom'] . '-01 00:00:00' : date("Y-m-01 00:00:00");
		$end_time = isset($post['selyrto']) && isset($post['selmonto']) ? date('Y-m-t', strtotime($post['selyrto'] . '-' . $post['selmonto'] . '-01')) . " 23:59:59" : date("Y-m-t 23:59:59");
		$member = $this->MemberModel->getMemberInfoByIdUseridEmail($post['member_id']);
		$type = $post['type'];
		if($type == 'commissions'){
			$currency = $_POST['bonus'];
		}
		$member_id = isset($member['id']) ? $member['id'] : "";
		$return = $this->LedgerModel->getUniversalLedger($member_id, $currency, $start_time, $end_time, $type);
		return $return;
	}

	public function dashboard($user, $crr_month)
	{
		$mainAccountId = $user['main_acct_id'] > 0 ? $user['main_acct_id'] : $user['id'];
		$subAccounts = $this->MemberModel->getSubAccounts($mainAccountId);
		if ($user['main_acct_id'] == 0) {
			$mainAccount = $user;
		} else {
			$mainAccount = $this->MemberModel->getMemberInfoByIdUseridEmail($mainAccountId);
		}
		$one_month = calcMonth("Y-m", "-1", $crr_month);
		$two_month = calcMonth("Y-m", "-2", $crr_month);
		$general_rank = $this->RankModel->getRankById(1);

		$crr_month_log_rank = $this->RankModel->getUserRankByMonth($user['id'], $crr_month);
		if (!isset($crr_month_log_rank['id']))
			$crr_month_log_rank = $general_rank;

		$one_month_log_rank = $this->RankModel->getUserRankByMonth($user['id'], $one_month);
		if (!isset($one_month_log_rank['id']))
			$one_month_log_rank = $general_rank;

		$two_month_log_rank = $this->RankModel->getUserRankByMonth($user['id'], $two_month);
		if (!isset($two_month_log_rank['id']))
			$two_month_log_rank = $general_rank;

		$last_day_of_month = date('t-M-Y');

		$sponsored = $this->MemberModel->getSponsored($user['id']);
		$country = $this->CountryModel->getCountryById($user['country']);
		$bv = $this->SaleModel->getBv($user['id']);
		$monthly = $this->SaleModel->getSalesByPeriod($user['id'], $crr_month);
		$accu_sales = $this->SaleModel->getMemberSales($user['id']);
		$accu_matrix = $this->SaleModel->getMemberSalesMatrix($user['id']);
		$data['monthly_accu_matrix'] = $this->SaleModel->getMemberSalesMatrixPeriod($user['id'], $crr_month);
		$one_month_sale = $this->SaleModel->getMemberSalesMatrixPeriod($user['id'], $one_month);
		$two_month_sale = $this->SaleModel->getMemberSalesMatrixPeriod($user['id'], $two_month);
		$children = $this->MemberModel->getMemberByMatrixId($user['id']);
		$left_sales = 0;
		$right = 0;
		foreach ($children as $child) {
			$salex = $this->SaleModel->getMemberSalesMatrixPeriod($child['id'], $crr_month);
			$child['matrix_side'] == "L" ? ($left_sales = $salex) : ($right_sales = $salex);
		}
		$next_rank = $this->RankModel->getRankById($crr_month_log_rank['id'] + 1);
		$next_required_rank = isset($next_rank['required_rank']) ? $next_rank['required_rank'] : 0;
		$downline_required_rank = $this->RankModel->getRankById($next_required_rank);
		$current_downlines = $this->DownlineModel->getDownlines($user['id'], $crr_month);
		$cc_balance = $this->LedgerModel->getBalance("CC", $user['id']);
		$rc_balance = $this->LedgerModel->getBalance("RC", $user['id']);

		$return['CCs']['credit'] = $cc_balance['cr'];
		$return['CCs']['debit'] = $cc_balance['dr'];
		$return['CCs']['balance'] = $cc_balance['cr'] - $cc_balance['dr'];
		$return['RCs']['credit'] = $rc_balance['cr'];
		$return['RCs']['debit'] = $rc_balance['dr'];
		$return['RCs']['balance'] = $rc_balance['cr'] - $rc_balance['dr'];
		$return['MAIN_ACCOUNT'] = $mainAccount;
		$return['SUB_ACCOUNTS'] = $subAccounts;
		$return['title_1'][] = '[[LABEL_ACCOUNT_OVERVIEW]]';
		$return['title_1'][] = '[[LABEL_CURRENT_MONTH]] ' . $crr_month . ' - [[LABEL_ENDING]] ' . $last_day_of_month;
		$return['title_2'][] = '[[LABEL_CAREER_PLAN]]';
		$return['title_2'][] = '[[LABEL_CURRENT_MONTH]] ' . $crr_month . ' - [[LABEL_ENDING]] ' . $last_day_of_month;
		$return['summary']['LABEL_NAME'] = $user['f_name'] . " " . $user['l_name'];
		$return['summary']['ID'] = (isset($country['short_name']) ? $country['short_name'] . " " . $user['userid'] : $user['userid']);
		$return['summary']['LABEL_LEFT_BV'] = isset($bv['bleft_node']) ? $bv['bleft_node'] : 0;;
		$return['summary']['LABEL_RIGHT_BV'] = isset($bv['bright_node']) ? $bv['bright_node'] : 0;;
		$return['summary']['LABEL_ACCU_LEFT_BV'] = isset($bv['accu_left_node']) ? $bv['accu_left_node'] : 0;
		$return['summary']['LABEL_ACCU_RIGHT_BV'] = isset($bv['accu_right_node']) ? $bv['accu_right_node'] : 0;
		$return['summary']['LABEL_PERSONALLY_SPONSOR'] = count($sponsored);
		$return['summary']['LABEL_MONTHLY_PERSONAL_PURCHASE'] = number_format((isset($monthly['personal_sales']) ? $monthly['personal_sales'] : 0), 2);
		$return['summary']['LABEL_TOTAL_PERSONAL_PURCHASE'] = number_format((isset($accu_sales['accu_personal_sales']) ? $accu_sales['accu_personal_sales'] : 0), 2);
		$return['summary']['LABEL_MONTHLY_DIRECT_SALES'] = number_format((isset($monthly['direct_sales']) ? $monthly['direct_sales'] : 0), 2);
		$return['summary']['LABEL_TOTAL_DIRECT_SALES'] = number_format((isset($accu_sales['accu_direct_sales']) ? $accu_sales['accu_direct_sales'] : 0), 2);
		$return['summary']['LABEL_ACCU_GROUP_SALES'] = number_format((isset($accu_matrix['accu_group_sales']) ? $accu_matrix['accu_group_sales'] : 0) - (isset($accu_matrix['accu_personal_sales']) ? $accu_matrix['accu_personal_sales'] : 0), 2);
		$return['career']['LABEL_CURRENT_RANK'] = $crr_month_log_rank['name'];
		$return['career']['LABEL_PREVIOUS_MONTH_RANK'] = $one_month_log_rank['name'];
		$return['career']['LABEL_MONTH_BEFORE_LAST_RANK'] = $two_month_log_rank['name'];
		$return['career']['LABEL_NEXT_RANK'] = $next_rank['name'];
		$return['career']['LABEL_NEXT_RANK_TARGET'] = "[[LABEL_LEFT]] " . number_format($next_rank['required_left_sales'], 2) . " [[LABEL_RIGHT]] " . number_format($next_rank['required_right_sales'], 2);
		$return['career']['LABEL_MONTHLY_GROUP_SALES'] = "[[LABEL_LEFT]] " . number_format((isset($left_sales['group_sales']) ? $left_sales['group_sales'] : 0), 2) . " [[LABEL_RIGHT]] " . number_format((isset($right_sales['group_sales']) ? $right_sales['group_sales'] : 0), 2);
		$return['career']['LABEL_PREV_MONTH_GROUP_SALES'] = number_format((isset($one_month_sale['group_sales']) ? $one_month_sale['group_sales'] : 0) - (isset($one_month_sale['personal_sales']) ? $one_month_sale['personal_sales'] : 0), 2);
		$return['career']['LABEL_PREV_PREV_MONTH_GROUP_SALES'] = number_format((isset($two_month_sale['group_sales']) ? $two_month_sale['group_sales'] : 0) - (isset($two_month_sale['personal_sales']) ? $two_month_sale['personal_sales'] : 0), 2);
		if ($next_rank['months'] == 3) {
			$return['career']['LABEL_DOWNLINES_RANK_TARGET'] = number_format($one_month_sale['group_sales'] - $one_month_sale['personal_sales'], 2);
			$return['career']['LABEL_DOWNLINES_RANK_CURRENT'] = number_format($two_month_sale['group_sales'] - $two_month_sale['personal_sales'], 2);
		}
		if ($next_rank['required_left_qty'] > 0) {
			$return['career']['LABEL_DOWNLINES_RANK_PREV_MONTH'] = "[[LABEL_LEFT]] " . $next_rank['required_left_qty'] . " x " . $downline_required_rank['name'] . " [[LABEL_RIGHT]] " . $next_rank['required_right_qty'] . " x " . $downline_required_rank['name'];
			$return['career']['LABEL_DOWNLINES_RANK_PREV_PREV_MONTH'] = "[[LABEL_LEFT]] " . $current_downlines['left'] . " x " . $downline_required_rank['name'] . " [[LABEL_RIGHT]] " . $current_downlines['right'] . " x " . $downline_required_rank['name'];
		} else if ($next_rank['required_qty'] > 0) {
			$return['career']['LABEL_DOWNLINES_RANK_PREV_MONTH'] = $next_rank['required_qty'] . " x " . $downline_required_rank['name'];
			$return['career']['LABEL_DOWNLINES_RANK_PREV_PREV_MONTH'] = $current_downlines['total'] . " x " . $downline_required_rank['name'];
			if ($next_rank['months'] == 3) {
				$return['career']['LABEL_DOWNLINES_RANK_PREV_MONTH'] = $current_downlines['prev_total'] . " x " . $downline_required_rank['name'];
				$return['career']['LABEL_DOWNLINES_RANK_PREV_PREV_MONTH'] = $current_downlines['prev_prev_total'] . " x " . $downline_required_rank['name'];
			}
		}
		return $return;
	}

	public function getSummary($period)
	{
		$data['ledgers'] = $this->LedgerModel->getLedgerSummary($period);
		$data['sales'] = $this->SaleModel->getMemberSaleSummary()['ps'];
		$data['period_sales'] = $this->SaleModel->getMemberSaleSummary($period)['ps'];
		$data['members'] = $this->MemberModel->getMemberSummary($period, false);
		$data['main_members'] = $this->MemberModel->getMemberSummary($period, true);
		return $data;
	}

	public function getYearlySummary($year)
	{
		$return['SALES'] = (float)($this->SaleModel->getMemberSaleYearly($year)['ps']);
		$return['SPONSOR'] = $this->CommissionModel->getCommissionByYear($year, "SPONSOR_BONUS");
		$return['BINARY'] = $this->CommissionModel->getCommissionByYear($year, "BINARY_BONUS");
		$return['MATCHING'] = $this->CommissionModel->getCommissionByYear($year, "MATCHING_BONUS");
		$return['MODEL'] = $this->CommissionModel->getCommissionByYear($year, "MODEL_BONUS");
		return $return;
	}
}
