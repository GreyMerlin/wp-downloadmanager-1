<?php
/*
Plugin Name: WP-DownloadManager
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Adds a simple download manager to your WordPress blog.
Version: 1.00
Author: Lester 'GaMerZ' Chan
Author URI: http://www.lesterchan.net
*/


/*  
	Copyright 2007  Lester Chan  (email : gamerz84@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Create text domain for translations
load_plugin_textdomain('wp-downloadmanager', 'wp-content/plugins/downloadmanager');


### Downloads Table Name
$wpdb->downloads				= $table_prefix . 'downloads';


### Function: Downloads Administration Menu
add_action('admin_menu', 'downloads_menu');
function downloads_menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page(__('Downloads', 'wp-downloadmanager'), __('Downloads', 'wp-downloadmanager'), 'manage_downloads', 'downloadmanager/download-manager.php');
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page('downloadmanager/download-manager.php', __('Manage Downloads', 'wp-downloadmanager'), __('Manage Downloads', 'wp-downloadmanager'), 'manage_downloads', 'downloadmanager/download-manager.php');
		add_submenu_page('downloadmanager/download-manager.php', __('Add File', 'wp-downloadmanager'), __('Add File', 'wp-downloadmanager'), 'manage_downloads', 'downloadmanager/download-add.php');
		add_submenu_page('downloadmanager/download-manager.php', __('Download Options', 'wp-downloadmanager'), __('Download Options', 'wp-downloadmanager'), 'manage_downloads', 'downloadmanager/download-options.php');
	}
}


### Function: Add Download Query Vars
add_filter('query_vars', 'download_query_vars');
function download_query_vars($public_query_vars) {
	$public_query_vars[] = "download_id";
	return $public_query_vars;
}


### Function: Download htaccess ReWrite Rules   
add_action('init', 'download_rewrite'); 
function download_rewrite() { 
	add_rewrite_rule('download/([0-9]{1,})/?$', 'index.php?download_id=$matches[1]');
}


### Function: Download File
add_action('template_redirect', 'download_file');
function download_file() {
	global $wpdb;
	$id = intval(get_query_var('download_id'));
	if($id > 0) {
		$file = $wpdb->get_var("SELECT file FROM $wpdb->downloads WHERE file_id = $id");
		if(!$file) {
			die(__('File does not exist.', 'wp-downloadmanager'));
		}
		$update_hits = $wpdb->query("UPDATE $wpdb->downloads SET file_hits = file_hits + 1 WHERE file_id = $id");
		$file_path = get_option('download_path');
		$file_name = stripslashes($file);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".basename($file_name).";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($file_path.$file_name));
		@readfile($file_path.$file_name);
		exit();
	}
}


### Function: Format Bytes Into TB/GB/MB/KB/Bytes
if(!function_exists('format_size')) {
	function format_size($rawSize) {
		if($rawSize / 1099511627776 > 1) {
			return round($rawSize/1099511627776, 1) . ' TB';
		} elseif($rawSize / 1073741824 > 1) {
			return round($rawSize/1073741824, 1) . ' GB';
		} elseif($rawSize / 1048576 > 1) {
			return round($rawSize/1048576, 1) . ' MB';
		} elseif($rawSize / 1024 > 1) {
			return round($rawSize/1024, 1) . ' KB';
		} else {
			return round($rawSize, 1) . ' bytes';
		}
	}
}


### Function: Get Max File Size That Can Be Uploaded
function get_max_upload_size() {
	$maxsize = ini_get('upload_max_filesize');
	if (!is_numeric($maxsize)) {
		if (strpos($maxsize, 'M') !== false) {
			$maxsize = intval($maxsize)*1024*1024;
		} elseif (strpos($maxsize, 'K') !== false) {
			$maxsize = intval($maxsize)*1024;
		} elseif (strpos($maxsize, 'G') !== false) {
			$maxsize = intval($maxsize)*1024*1024*1024;
		}
	}
	return $maxsize;
}


### Function: Place Download Page In Content
add_filter('the_content', 'place_downloadpage', '7');
function place_downloadpage($content){
     $content = str_replace("[page_downloads]", "downloads_page()", $content); 
    return $content;
}


### Function: Downloads Page
function downloads_page() {
	global $wpdb;
	$output = '';
	$file_sort = get_option('download_sort');
	$files = $wpdb->get_results("SELECT * FROM $wpdb->downloads ORDER BY file_category ASC, {$file_sort['by']} {$file_sort['order']} LIMIT {$file_sort['perpage']}");
	if($files) {
		
	}
}


### Function: List Out All Files In Downloads Directory
function list_files($dir, $orginal_dir) {
	global $download_files, $download_files_subfolder;
	if (is_dir($dir)) {
	   if ($dh = opendir($dir)) {
		   while (($file = readdir($dh)) !== false) {
				if($file != '.' && $file != '..')	{
					if(is_dir($dir.'/'.$file)) {						
						list_files($dir.'/'.$file, $orginal_dir);
					} else {
						$folder_file =str_replace($orginal_dir, '', $dir.'/'.$file);
						$sub_dir = explode('/', $folder_file);
						if(sizeof($sub_dir)  > 2) {
							$download_files_subfolder[] = $folder_file;
						} else {
							$download_files[] = $folder_file;
						}
					}
				}
		   }
		   closedir($dh);
	   }
	}
}


### Function: List Out All Files In Downloads Directory
function list_folders($dir, $orginal_dir) {
	global $download_folders;
	if (is_dir($dir)) {
	   if ($dh = opendir($dir)) {
		   while (($file = readdir($dh)) !== false) {
				if($file != '.' && $file != '..')	{
					if(is_dir($dir.'/'.$file)) {
						$folder =str_replace($orginal_dir, '', $dir.'/'.$file);
						$download_folders[] = $folder;
						list_files($dir.'/'.$file, $orginal_dir);
					}
				}
		   }
		   closedir($dh);
	   }
	}
}


### Function: Print Listing Of Files In Alphabetical Order
function print_list_files($dir, $orginal_dir, $selected = '') {
	global $download_files, $download_files_subfolder;
	list_files($dir, $orginal_dir);
	natcasesort($download_files);
	natcasesort($download_files_subfolder);
	foreach($download_files as $download_file) {
		if($download_file == $selected) {
			echo '<option value="'.$download_file.'" selected="selected">'.$download_file.'</option>'."\n";	
		} else {
			echo '<option value="'.$download_file.'">'.$download_file.'</option>'."\n";	
		}
	}
	foreach($download_files_subfolder as  $download_file_subfolder) {
		if($download_file == $selected) {
			echo '<option value="'.$download_file_subfolder.'" selected="selected">'.$download_file_subfolder.'</option>'."\n";	
		} else {
			echo '<option value="'.$download_file_subfolder.'">'.$download_file_subfolder.'</option>'."\n";	
		}
	}
}


### Function: Print Listing Of Folders In Alphabetical Order
function print_list_folders($dir, $orginal_dir) {
	global $download_folders;
	list_folders($dir, $orginal_dir);
	natcasesort($download_folders);
	echo '<option value="/">/</option>'."\n";	
	foreach($download_folders as $download_folder) {
		echo '<option value="'.$download_folder.'">'.$download_folder.'</option>'."\n";	
	}
}


### Function: Editable Timestamp
function file_timestamp($file_timestamp) {
	global $month;
	$day = gmdate('j', $file_timestamp);
	echo '<select name="file_timestamp_day" size="1">'."\n";
	for($i = 1; $i <=31; $i++) {
		if($day == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>&nbsp;&nbsp;'."\n";
	$month2 = gmdate('n', $file_timestamp);
	echo '<select name="file_timestamp_month" size="1">'."\n";
	for($i = 1; $i <= 12; $i++) {
		if ($i < 10) {
			$ii = '0'.$i;
		} else {
			$ii = $i;
		}
		if($month2 == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$month[$ii]</option>\n";	
		} else {
			echo "<option value=\"$i\">$month[$ii]</option>\n";	
		}
	}
	echo '</select>&nbsp;&nbsp;'."\n";
	$year = gmdate('Y', $file_timestamp);
	echo '<select name="file_timestamp_year" size="1">'."\n";
	for($i = 2000; $i <= gmdate('Y'); $i++) {
		if($year == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>&nbsp;@'."\n";
	$hour = gmdate('H', $file_timestamp);
	echo '<select name="file_timestamp_hour" size="1">'."\n";
	for($i = 0; $i < 24; $i++) {
		if($hour == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>&nbsp;:'."\n";
	$minute = gmdate('i', $file_timestamp);
	echo '<select name="file_timestamp_minute" size="1">'."\n";
	for($i = 0; $i < 60; $i++) {
		if($minute == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	
	echo '</select>&nbsp;:'."\n";
	$second = gmdate('s', $file_timestamp);
	echo '<select name="file_timestamp_second" size="1">'."\n";
	for($i = 0; $i <= 60; $i++) {
		if($second == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>'."\n";
}


### Function: Create Downloads Table
add_action('activate_downloadmanager/downloadmanager.php', 'create_download_table');
function create_download_table() {
	global $wpdb;
	include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
	// Create WP-Downloads Table
	$create_table = "CREATE TABLE $wpdb->downloads (".
							"file_id int(10) NOT NULL auto_increment,".
							"file varchar(200) NOT NULL default '',".
							"file_name text NOT NULL,".
							"file_des text NOT NULL,".
							"file_size varchar(20) NOT NULL default '',".
							"file_category int(2) NOT NULL default '0',".
							"file_date varchar(20) NOT NULL default '',".
							"file_hits int(10) NOT NULL default '0',".
							"PRIMARY KEY  (file_id));";
	maybe_create_table($wpdb->downloads, $create_table);
	// WP-Downloads Options
	add_option('download_path', ABSPATH.'wp-content/files', 'Download Path');
	add_option('download_categories', array('General'), 'Download Categories');
	add_option('download_sort', array('by' => 'file_name', 'order' => 'asc', 'perpage' => 20), 'Download Sorting Options');
	add_option('download_template_category_header', '', 'Download Category Header Template');
	add_option('download_template_category_footer', '', 'Download Category FooterTemplate');
	add_option('download_template_listing', '', 'Download Listing Template');
	add_option('download_template_embedded', '', 'Download Embedded Template');
	// Create Files Folder
	if(!is_dir(ABSPATH.'/wp-content/files')) {
		mkdir(ABSPATH.'/wp-content/files');
	}
	// Set 'manage_downloads' Capabilities To Administrator	
	$role = get_role('administrator');
	if(!$role->has_cap('manage_downloads')) {
		$role->add_cap('manage_downloads');
	}
}
?>