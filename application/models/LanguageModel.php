<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LanguageModel extends CI_Model
{
	public function get($language)
	{
		$this->db->select("$language, template");
		$query = $this->db->get("lang_presentation");
		return $query->result_array();
	}

	public function update($post)
	{
		$this->db->update('lang_presentation', array(
			"en" => $post['english'],
			"si_cn" => $post['chinese']
		), array('id' => $post['id']));
	}

	public function getAll()
	{
		$this->db->select("id, en, si_cn");
		$query = $this->db->get("lang_presentation");
		return $query->result_array();
	}

	public function replace($language, $tag)
	{
		$this->db->select($language);
		$this->db->where("template", $tag);
		$query = $this->db->get("lang_presentation");
		return $query->row_array();
	}


}
