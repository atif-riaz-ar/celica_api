<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Announcements extends CI_Controller
{
	public function all()
	{
		$list = $this->AnnouncementModel->getAllAnnouncements();
		return successReponse("", $list);
	}

	public function add()
	{
		unset($_POST['access_token']);
		$list = $this->AnnouncementModel->update($_POST);
		return successReponse("", $list);
	}

	public function get($id)
	{
		unset($_POST['access_token']);
		$announcement = $this->AnnouncementModel->get($id);
		return successReponse("", $announcement);
	}

	public function delete()
	{
		unset($_POST['access_token']);
		$announcement = $this->AnnouncementModel->delete($_POST['id_to_delete']);
		return successReponse("", $announcement);
	}
}

