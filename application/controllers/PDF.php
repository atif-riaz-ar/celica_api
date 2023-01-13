<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PDF extends CI_Controller
{
	public function order($order_id)
	{
		$data['salesInfo'] = $this->PdfModel->getOrderInfo($order_id);
		if (!empty($data['salesInfo'])) {
			$data['rsProducts'] = $this->PdfModel->getSalesDetail($order_id);
			$data['country'] = $data['salesInfo']['full_name'];
			$all_html = $this->load->view('order', $data, true);
			$live_mpdf = new \Mpdf\Mpdf();
			$live_mpdf->WriteHTML($all_html);
			$live_mpdf->Output('assets/invoices/' . $data['salesInfo']['order_num'] . '.pdf', 'D');
		}
	}

	public function delivery($order_id)
	{
		$data['salesInfo'] = $this->PdfModel->getOrderInfo($order_id);
		if (!empty($data['salesInfo'])) {
			$data['rsProducts'] = $this->PdfModel->getSalesDetail($order_id);
			$data['country'] = $data['salesInfo']['full_name'];
			$all_html = $this->load->view('DOs', $data, true);
			$live_mpdf = new \Mpdf\Mpdf();
			$live_mpdf->WriteHTML($all_html);
			$live_mpdf->Output('assets/invoices/' . $data['salesInfo']['order_num'] . '.pdf', 'D');
		}
	}
}
