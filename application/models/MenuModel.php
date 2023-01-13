<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MenuModel extends CI_Model
{
	public function getMenus($admin_id)
	{

		$result = $this->MemberModel->getAdminInfoByIdLogin($admin_id);
		$permission = explode(",", $result['access_list']);
		$this->db->order_by("order", "asc");
		$query = $this->db->get("admin_menu_category");
		$menus = $query->result_array();
		$return = array();
		$n = 0;
		foreach ($menus as $menu) {
			$children = $this->getChildren($menu['id']);
			if (count($children) > 0) {
				$new_children = array();
				foreach ($children as $child) {
					if(in_array($child['id'], $permission) or $result['group_id'] == 1) {
						 $new_children[] = $child;
					}
				}
				if(count($new_children) > 0) {
					$return[$n]['children'] = $new_children;
					$return[$n]['parent'] = $menu;
				}
				$n++;
			}
		}
		return $return;
	}

	public function getChildren($parent)
	{
		$this->db->where("privilege_type", "navigate");
		$this->db->where("category_id", $parent);
		$this->db->order_by("order", "asc");
		$query = $this->db->get("admin_menu_item");
		return $query->result_array();
	}

	public function update($vals, $id)
	{
		$this->db->update('admin_category', array(
			"access_list" => $vals
		), array('id' => $id));
	}
}
