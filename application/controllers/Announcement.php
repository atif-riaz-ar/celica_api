<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Announcement extends CI_Controller
{
	public function index()
	{
		$report = $this->AnnouncementModel->getAnnouncement($_POST);
		return successReponse("", $report);
	}
}
