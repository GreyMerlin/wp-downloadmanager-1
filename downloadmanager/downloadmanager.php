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
		add_submenu_page('downloadmanager/download-manager.php', __('Download Options', 'wp-downloadmanager'), __('Download Options', 'wp-downloadmanager'), 'manage_downloads', 'downloadmanager/download-options.php');
	}
}


### Function: Format Bytes Into GB/MB/KB/Bytes
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
	$file_sort = get_settings('download_sort');
	$files = $wpdb->get_results("SELECT * FROM $wpdb->downloads ORDER BY file_category ASC, {$file_sort['by']} {$file_sort['order']} LIMIT {$file_sort['perpage']}");
	if($files) {
		
	}
}


### Function: List Out All Files In Downloads Directory
function list_files($dir, $orginal_dir, $selected = '') {
	if (is_dir($dir)) {
	   if ($dh = opendir($dir)) {
		   while (($file = readdir($dh)) !== false) {
				if($file != '.' && $file != '..')	{
					if(is_dir($dir.'/'.$file)) {
						list_files($dir.'/'.$file, $orginal_dir, $selected);
					} else {
						$folder_file =str_replace($orginal_dir, '', $dir.'/'.$file);
						if($folder_file == $selected) {
							echo '<option value="'.$folder_file.'" selected="selected">'.$folder_file.'</option>';
						} else {
							echo '<option value="'.$folder_file.'">'.$folder_file.'</option>';
						}
					}
				}
		   }
		   closedir($dh);
	   }
	}
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