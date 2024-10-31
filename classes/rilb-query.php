<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class rilb_cls_dbquery {

	public static function rilb_count($id = 0) {

		global $wpdb;
		$result = '0';
		
		if($id <> "" && $id > 0) {
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "randomimage_lb WHERE ri_id = %d", array($id));
		} 
		else {
			$sSql = "SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "randomimage_lb";
		}
		
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function rilb_select_bygroup($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "randomimage_lb";

		if($group <> "") {
			$sSql = $sSql . " WHERE ri_group = %s order by ri_id desc";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " order by ri_group, ri_id desc";
		}

		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function rilb_select_byid($id = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "randomimage_lb";

		if($id <> "") {
			$sSql = $sSql . " WHERE ri_id = %d LIMIT 1";
			$sSql = $wpdb->prepare($sSql, array($id));
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else {
			$sSql = $sSql . " order by ri_group, ri_order";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		
		return $arrRes;
	}
	
	public static function rilb_select_bygroup_rand($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "randomimage_lb";

		if($group <> "") {
			$sSql = $sSql . " WHERE ri_group = %s order by rand() LIMIT 1";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " order by rand() LIMIT 1";
		}

		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function rilb_group() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT distinct(ri_group) FROM " . $wpdb->prefix . "randomimage_lb order by ri_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}

	public static function rilb_delete($id = "") {

		global $wpdb;

		if($id <> "") {
			$sSql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "randomimage_lb WHERE ri_id = %s LIMIT 1", $id);
			$wpdb->query($sSql);
		}
		
		return true;
	}

	public static function rilb_action_ins($data = array(), $action = "insert") {

		global $wpdb;
		
		if($action == "insert") {
			$sql = $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "randomimage_lb
				(ri_image, ri_link, ri_title, ri_width, ri_group, ri_status) VALUES (%s, %s, %s, %d, %s, %s)", 
				array($data["ri_image"], $data["ri_link"], $data["ri_title"], $data["ri_width"], $data["ri_group"], $data["ri_status"]));
			$wpdb->query($sql);
			return "inserted";
		}
		elseif($action == "update") {
			$sSql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "randomimage_lb SET ri_image = %s, ri_link = %s, ri_title = %s, 
				ri_width = %d, ri_group = %s, ri_status = %s WHERE ri_id = %d LIMIT 1", 
				array($data["ri_image"], $data["ri_link"], $data["ri_title"], $data["ri_width"], $data["ri_group"], $data["ri_status"], $data["ri_id"]));
			$wpdb->query($sSql);
			return "update";
		}
	}
	


	public static function rilb_default() {

		$count = rilb_cls_dbquery::rilb_count($id = 0);
		if($count == 0){
			$img_sm1 = plugin_dir_url( __DIR__ ) . '/sample/1_sm.jpg';
			$img_bg1 = plugin_dir_url( __DIR__ ) . '/sample/1_bg.jpg';
			$img_sm2 = plugin_dir_url( __DIR__ ) . '/sample/2_sm.jpg';
			$img_bg2 = plugin_dir_url( __DIR__ ) . '/sample/2_bg.jpg';
			
			$data['ri_image'] = $img_sm1;
			$data['ri_link'] = $img_bg1;
			$data['ri_title'] = 'Sample Image 1';
			$data['ri_width'] = '0';
			$data['ri_group'] = 'Group1';
			$data['ri_status'] = 'Yes';
			rilb_cls_dbquery::rilb_action_ins($data, "insert");
			
			$data['ri_image'] = $img_sm2;
			$data['ri_link'] = $img_bg2;
			$data['ri_title'] = 'Sample Image 2';
			rilb_cls_dbquery::rilb_action_ins($data, "insert");

		}
	}
	
	public static function rilb_common_text($value) {
		
		$returnstring = "";
		switch ($value) 
		{
			case "Yes":
				$returnstring = '<span style="color:#006600;">Yes</span>';
				break;
			case "No":
				$returnstring = '<span style="color:#FF0000;">No</span>';
				break;
			case "_blank":
				$returnstring = '<span style="color:#006600;">New window</span>';
				break;
			case "_self":
				$returnstring = '<span style="color:#0000FF;">Same window</span>';
				break;
			default:
       			$returnstring = $value;
		}
		return $returnstring;
	}
	
	public static function endswith($fullstr, $needle)
    {
        $strlen = strlen($needle);
        $fullstrend = substr($fullstr, strlen($fullstr) - $strlen);
        return $fullstrend == $needle;
    }
}