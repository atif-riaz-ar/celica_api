<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SystemModel extends CI_Model
{
	public function getConstants()
	{
		$query = $this->db->get('constants');
		$constants = $query->result_array();
		$return = array();
		foreach ($constants as $constant){
			if(ENVIRONMENT == "development") {
				$return[$constant['item']] = $constant['dummy'];
			} else {
				$return[$constant['item']] = $constant['value'];
			}
		}
		return $return;
	}
}
