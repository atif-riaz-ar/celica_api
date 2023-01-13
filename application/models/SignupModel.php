<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SignupModel extends CI_Model
{
	public function getMemberFormData()
	{
		$return['gateways'] = $this->PaymentModel->getGateways("ACTIVE");
		$return['packages'] = $this->ProductModel->getPackages("ACTIVE");
		$return['countries'] = $this->CountryModel->getAllCountries();
		return $return;
	}

	public function getMax(){
		$this->db->select_max('userid');
		$query = $this->db->get('member');
		return $query->row_array()['userid'] + 1;
	}

	public function createUser($signup_data)
	{
		$max = $this->getMax();
		$user_data = array(
			"sponsorid" => $signup_data['sponsor_id'],
			"matrixid" => $signup_data['matrix_id'],
			"matrix_side" => $signup_data['matrix_side'],
			"userid" => $max,
			"rank" => 1,
			"f_name" => $signup_data['f_name'],
			"l_name" => $signup_data['l_name'],
			"country" => $signup_data['country'],
			"mobile" => $signup_data['mobile'],
			"package_id" => $signup_data['package_id'],
			"email" => $signup_data['email'],
			"account_status" => "ACTIVE",
		);

		if($signup_data['downline_type'] == 0) {
			$pass = randomString(8);
			$ps = generatePassword($pass);
			$user_data["main_acct_id"] = 0;
			$user_data["password"] = $ps['password'];
			$user_data["primary_salt"] = $ps['salt'];
			$user_data["sec_password"] = "";
			$user_data["secondary_salt"] = "";
		} else {
			$user = $this->MemberModel->getMemberInfoByUserid($signup_data['user']);
			$user_data["main_acct_id"] = $signup_data['sponsor_id'];
			$user_data["password"] = $user["password"];
			$user_data["primary_salt"] = $user["primary_salt"];
			$user_data["sec_password"] = $user["sec_password"];
			$user_data["secondary_salt"] = $user["secondary_salt"];
		}
		$this->db->insert('member', $user_data);
		$id = $this->db->insert_id();
		$user_data['id'] = $id;
		$number = sms_number($user_data['mobile']);
		if($signup_data['downline_type'] == 0) {
			$message = "User $max has beed created and auto generated password by Ginseng is: " . $pass;
		} else {
			$message = "User $max has beed created. Please use the password of main account. ";
		}
		sms($number, $message);
		return $user_data;
	}
}
