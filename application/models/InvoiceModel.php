<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceModel extends CI_Model
{
	public function getInvoices($post)
	{
		$start_time = isset($post['selyrfrom']) && isset($post['selmonfrom']) ? $post['selyrfrom'] . '-' . $post['selmonfrom'] . '-01 00:00:00' : date("Y-m-01 00:00:00");
		$end_time = isset($post['selyrto']) && isset($post['selmonto']) ? date('Y-m-t', strtotime($post['selyrto'] . '-' . $post['selmonto'] . '-01')) . " 23:59:59" : date("Y-m-t 23:59:59");
		$member = $this->MemberModel->getMemberInfoByToken($post['access_token']);
		$return = $this->getSelectedInvoices($member['id'], $start_time, $end_time);
		return $return;
	}

	public function getSelectedInvoices($user_id, $start_time, $end_time)
	{
		$last_date = date('Y-m-t 23:59:59', strtotime($end_time . '-1'));
		$q = "SELECT b.id as 'order_id',b.invoice_no,b.member_id,b.payment_type,p.name,b.order_date,b.total_amount
				as 'package_price',p.id FROM order_master b
				LEFT JOIN order_detail c ON b.id = c.order_master_id
				LEFT JOIN product p ON (p.id = b.entry_package OR p.id=c.product_id) 
					WHERE b.status ='PAID' and member_id = '$user_id' ";
		if ($start_time != "") {
			if ($end_time != "") {
				$q .= " AND (b.order_date >= '" . $start_time . "' AND b.order_date <= '" . $last_date . "')";
			} else {
				$q .= " AND b.order_date LIKE '%" . $start_time . "%'";
			}
			$q .= " ORDER BY order_date ASC";
		}
		$query = $this->db->query($q);
		return $query->result_array();
	}

}
