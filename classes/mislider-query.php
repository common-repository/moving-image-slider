<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class mislider_cls_dbquery {

	public static function mislider_count($id = 0) {
	
		global $wpdb;
		$result = '0';
		
		if($id <> "" && $id > 0) {
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "moving_image_slider WHERE mislider_id = %d", array($id));
		} 
		else {
			$sSql = "SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "moving_image_slider";
		}
		
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function mislider_select_bygroup($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "moving_image_slider";

		if($group <> "") {
			$sSql = $sSql . " WHERE mislider_group = %s order by mislider_id desc";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " order by mislider_id desc";
		}

		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function mislider_select_byid($id = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "moving_image_slider";

		if($id <> "") {
			$sSql = $sSql . " WHERE mislider_id = %d LIMIT 1";
			$sSql = $wpdb->prepare($sSql, array($id));
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else {
			$sSql = $sSql . " order by mislider_group";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		
		return $arrRes;
	}
	
	public static function mislider_select_bygroup_rand($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "moving_image_slider";

		if($group <> "") {
			$sSql = $sSql . " WHERE mislider_group = %s order by rand() LIMIT 0,20";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " order by rand() LIMIT 0,20";
		}

		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function mislider_select_shortcode($id = "", $group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "moving_image_slider WHERE mislider_status = 'Yes'";
		//$sSql .= " AND ( mislider_start <= NOW() or mislider_start = '0000-00-00' )";
		//$sSql .= " AND ( mislider_end >= NOW() or mislider_end = '0000-00-00' )";
		
		if($id <> "" && $id <> "0") {
			$sSql .= " AND mislider_id = %d LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($id));
		}
		elseif($group <> "") {
			$sSql .= " AND mislider_group = %s Order by rand() LIMIT 0,25";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,25";
		}
		
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		
		return $arrRes;
	}
	
	public static function mislider_group() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT distinct(mislider_group) FROM " . $wpdb->prefix . "moving_image_slider order by mislider_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}

	public static function mislider_delete($id = "") {

		global $wpdb;

		if($id <> "") {
			$sSql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "moving_image_slider WHERE mislider_id = %s LIMIT 1", $id);
			$wpdb->query($sSql);
		}
		
		return true;
	}

	public static function mislider_action_ins($data = array(), $action = "insert") {

		global $wpdb;
		
		if($action == "insert") {
			$sSql = $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "moving_image_slider
				(mislider_title, mislider_desc, mislider_img, mislider_slide, mislider_group, mislider_status) VALUES 
				(%s, %s, %s, %s, %s, %s)", 
				array($data["mislider_title"], $data["mislider_desc"], $data["mislider_img"], $data["mislider_slide"], $data["mislider_group"], $data["mislider_status"]));
			$wpdb->query($sSql);
			return "inserted";
		}
		elseif($action == "update") {
			$sSql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "moving_image_slider SET mislider_title = %s, mislider_desc = %s, mislider_img = %s, 
				mislider_slide = %s, mislider_group = %s, mislider_status = %s WHERE mislider_id = %d LIMIT 1", 
				array($data["mislider_title"], $data["mislider_desc"], $data["mislider_img"], $data["mislider_slide"], $data["mislider_group"], $data["mislider_status"], 
				$data["mislider_id"]));
			$wpdb->query($sSql);
			return "update";
		}
	}
	
	public static function mislider_default() {

		$count = mislider_cls_dbquery::mislider_count($id = 0);
		if($count == 0){
			$folderpath = plugin_dir_url( __DIR__ );
			if (mislider_cls_dbquery::mislider_endswith($folderpath, '/') == false) {
				$folderpath = $folderpath . "/";
			}
			
			$sing_bg_1 = $folderpath . 'sample/sing_bg_1.jpg';
			$data['mislider_title'] = 'Sample Image 1';
			$data['mislider_desc'] = 'Sample image default test to show on the image 1';
			$data['mislider_img'] = $sing_bg_1;
			$data['mislider_slide'] = 'Zoom In';
			$data['mislider_group'] = 'Group1';
			$data['mislider_status'] = 'Yes';
			mislider_cls_dbquery::mislider_action_ins($data, "insert");
			
			$sing_bg_2 = $folderpath . 'sample/sing_bg_2.jpg';
			$data['mislider_title'] = 'Sample Image 2';
			$data['mislider_desc'] = 'Sample image default test to show on the image 2';
			$data['mislider_img'] = $sing_bg_2;
			$data['mislider_slide'] = 'Zoom Out';
			$data['mislider_group'] = 'Group1';
			$data['mislider_status'] = 'Yes';
			mislider_cls_dbquery::mislider_action_ins($data, "insert");
			
			$sing_sm_1 = $folderpath . 'sample/sing_sm_1.jpg';
			$data['mislider_title'] = 'Sample Image 3';
			$data['mislider_desc'] = 'Sample image default test 1';
			$data['mislider_img'] = $sing_sm_1;
			$data['mislider_slide'] = 'Zoom In';
			$data['mislider_group'] = 'Group2';
			$data['mislider_status'] = 'Yes';
			mislider_cls_dbquery::mislider_action_ins($data, "insert");
			
			$sing_sm_2 = $folderpath . 'sample/sing_sm_2.jpg';
			$data['mislider_title'] = 'Sample Image 4';
			$data['mislider_desc'] = 'Sample image default test 2';
			$data['mislider_img'] = $sing_sm_2;
			$data['mislider_slide'] = 'Zoom Out';
			$data['mislider_group'] = 'Group2';
			$data['mislider_status'] = 'Yes';
			mislider_cls_dbquery::mislider_action_ins($data, "insert");
		}
	}
	
	public static function mislider_common_text($value) {
		
		$returnstring = "";
		switch ($value) 
		{
			case "Yes":
				$returnstring = '<span style="color:#006600;">Yes</span>';
				break;
			case "No":
				$returnstring = '<span style="color:#FF0000;">No</span>';
				break;
			case "zoomin":
				$returnstring = 'Zoom In';
				break;
			case "zoomout":
				$returnstring = 'Zoom Out';
				break;
			case "panup":
				$returnstring = 'Pan Up';
				break;
			case "panleft":
				$returnstring = 'Pan Left';
				break;
			default:
       			$returnstring = $value;
		}
		return $returnstring;
	}
	
	public static function mislider_endswith($fullstr, $needle)
    {
        $strlen = strlen($needle);
        $fullstrend = substr($fullstr, strlen($fullstr) - $strlen);
        return $fullstrend == $needle;
    }
}