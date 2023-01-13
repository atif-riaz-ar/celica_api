<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CountryModel extends CI_Model
{
	public function getCountryById($country)
	{
		$this->db->where("id", $country);
		$query = $this->db->get('country_list');
		return $query->row_array();
	}

	public function getAllCountries()
	{
		$query = $this->db->get('country_list');
		return $query->result_array();
	}
}
