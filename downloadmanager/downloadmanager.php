<?php
/*
Plugin Name: WP-DownloadManager
Plugin URI: http://lesterchan.net/portfolio/programming.php
Description: Adds a simple download manager to your WordPress blog.
Version: 1.00
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
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
		add_submenu_page('downloadmanager/download-manager.php', __('Download Templates', 'wp-downloadmanager'), __('Download Templates', 'wp-downloadmanager'), 'manage_downloads', 'downloadmanager/download-templates.php');
		add_submenu_page('downloadmanager/download-manager.php', __('Uninstall WP-DownloadManager', 'wp-downloadmanager'), __('Uninstall WP-DownloadManager', 'wp-downloadmanager'), 'manage_downloads', 'downloadmanager/download-uninstall.php');
	}
}


### Function: Displays Download Manager Footer In WP-Admin
add_action('admin_footer', 'downloads_footer_admin');
function downloads_footer_admin() {
	// Javascript Code Courtesy Of WP-AddQuicktag (http://bueltge.de/wp-addquicktags-de-plugin/120/)
	echo '<script type="text/javascript">'."\n";
	echo "\t".'function insertDownload(where, myField) {'."\n";
	echo "\t\t".'var download_id = prompt("'.__('Enter File ID', 'wp-downloadmanager').'");'."\n";
	echo "\t\t".'while(isNaN(download_id)) {'."\n";
	echo "\t\t\t".'download_id = prompt("'.__('Error: File ID must be numeric', 'wp-downloadmanager').'\n\n'.__('Please enter File ID again', 'wp-downloadmanager').'");'."\n";
	echo "\t\t".'}'."\n";
	echo "\t\t".'if (download_id > 0) {'."\n";
	echo "\t\t\t".'if(where == "code") {'."\n";
	echo "\t\t\t\t".'edInsertContent(myField, "[download=" + download_id + "]");'."\n";
	echo "\t\t\t".'} else {'."\n";
	echo "\t\t\t\t".'return "[download=" + download_id + "]";'."\n";
	echo "\t\t\t".'}'."\n";
	echo "\t\t".'}'."\n";
	echo "\t".'}'."\n";
	echo "\t".'if(document.getElementById("ed_toolbar")){'."\n";
	echo "\t\t".'qt_toolbar = document.getElementById("ed_toolbar");'."\n";
	echo "\t\t".'edButtons[edButtons.length] = new edButton("ed_downloadmanager","'.__('Download', 'wp-downloadmanager').'", "", "","");'."\n";
	echo "\t\t".'var qt_button = qt_toolbar.lastChild;'."\n";
	echo "\t\t".'while (qt_button.nodeType != 1){'."\n";
	echo "\t\t\t".'qt_button = qt_button.previousSibling;'."\n";
	echo "\t\t".'}'."\n";
	echo "\t\t".'qt_button = qt_button.cloneNode(true);'."\n";
	echo "\t\t".'qt_button.value = "'.__('Download', 'wp-downloadmanager').'";'."\n";
	echo "\t\t".'qt_button.title = "'.__('Insert File Download', 'wp-downloadmanager').'";'."\n";
	echo "\t\t".'qt_button.onclick = function () { insertDownload(\'code\', edCanvas);}'."\n";
	echo "\t\t".'qt_button.id = "ed_downloadmanager";'."\n";
	echo "\t\t".'qt_toolbar.appendChild(qt_button);'."\n";
	echo "\t".'}'."\n";
	echo '</script>'."\n";
}


### Function: Add Quick Tag For Downloads In TinyMCE, Coutesy Of An-Archos (http://an-archos.com/anarchy-media-player)
add_filter('mce_plugins', 'download_mce_plugins', 5);
function download_mce_plugins($plugins) {    
	array_push($plugins, '-downloads', 'bold');    
	return $plugins;
}
add_filter('mce_buttons', 'download_mce_buttons', 5);
function download_mce_buttons($buttons) {
	array_push($buttons, 'separator', 'downloads');
	return $buttons;
}
add_action('tinymce_before_init','download_external_plugins');
function download_external_plugins() {	
	echo 'tinyMCE.loadPlugin("downloads", "'.get_option('siteurl').'/wp-content/plugins/downloadmanager/tinymce/plugins/downloads/");' . "\n"; 
	return;
}


### Function: Add Download Query Vars
add_filter('query_vars', 'download_query_vars');
function download_query_vars($public_query_vars) {
	$public_query_vars[] = "dl_id";
	return $public_query_vars;
}


### Function: Download htaccess ReWrite Rules   
add_action('init', 'download_rewrite'); 
function download_rewrite() { 
	add_rewrite_rule('download/([0-9]{1,})/?$', 'index.php?dl_id=$matches[1]');
}


### Function: Download File
add_action('template_redirect', 'download_file');
function download_file() {
	global $wpdb, $user_ID;
	$id = intval(get_query_var('dl_id'));
	if($id > 0) {
		$file_path = stripslashes(get_option('download_path'));
		$file_url = stripslashes(get_option('download_path_url'));
		$download_method = intval(get_option('download_method'));
		$file = $wpdb->get_row("SELECT file, file_permission FROM $wpdb->downloads WHERE file_id = $id");
		if(!$file) {
			header('HTTP/1.0 404 Not Found');
			die(__('Invalid File ID.', 'wp-downloadmanager'));
		}
		if(($file->file_permission == 1 && intval($user_ID) > 0) || $file->file_permission == 0) {
			$update_hits = $wpdb->query("UPDATE $wpdb->downloads SET file_hits = file_hits + 1 WHERE file_id = $id");
			$file_name = stripslashes($file->file);
			if(!is_remote_file($file_name)) {
				if(!is_file($file_path.$file_name)) {
					header('HTTP/1.0 404 Not Found');
					die(__('File does not exist.', 'wp-downloadmanager'));
				}
				if($download_method == 0) {
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
					header("Content-Disposition: attachment; filename=".basename($file_name).";");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize($file_path.$file_name));
					@readfile($file_path.$file_name);
				} else {
					header('Location: '.$file_url.$file_name);
				}
				exit();
			} else {
				if(ini_get('allow_url_fopen') && $download_method == 0) {
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream");
					header("Content-Type: application/download");
					header("Content-Disposition: attachment; filename=".basename($file_name).";");
					header("Content-Transfer-Encoding: binary");
					$file_size = remote_filesize($file_name);					
					if($file_size != __('unknown', 'wp-downloadmanager')) {
						header("Content-Length: ".$file_size);
					}
					@readfile($file_name);
				} else {
					header('Location: '.$file_name);					
				}
				exit();
			}
		} else {
			_e('You need to be a registered user to download this file.', 'wp-downloadmanager');
			exit();
		}
	}
}


### Function: Get Remote File Size
if(!function_exists('remote_filesize')) {
	function remote_filesize($uri) {
		$header_array = @get_headers($uri, 1);
		$file_size = $header_array['Content-Length'];
		if(!empty($file_size)) {
			return $file_size;
		} else {
			return __('unknown', 'wp-downloadmanager');
		}
	}
}


### Function: Format Bytes Into TB/GB/MB/KB/Bytes
function format_filesize($rawSize) {
	if($rawSize / 1099511627776 > 1) {
		return round($rawSize/1099511627776, 1) . ' TB';
	} elseif($rawSize / 1073741824 > 1) {
		return round($rawSize/1073741824, 1) . ' GB';
	} elseif($rawSize / 1048576 > 1) {
		return round($rawSize/1048576, 1) . ' MB';
	} elseif($rawSize / 1024 > 1) {
		return round($rawSize/1024, 1) . ' KB';
	} elseif($rawSize > 1) {
		return round($rawSize, 1) . ' bytes';
	} else {
		return __('unknown', 'wp-downloadmanager');
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


### Function: Is Remote File
function is_remote_file($file_name) {
	if(strpos($file_name, 'http://') === false && strpos($file_name, 'https://') === false  && strpos($file_name, 'ftp://') === false) {
		return false;
	}
	return true;
}


### Function: Snippet Text
if(!function_exists('snippet_chars')) {
	function snippet_chars($text, $length = 0) {
		$text = htmlspecialchars_decode($text);
		 if (strlen($text) > $length){       
			return htmlspecialchars(substr($text,0,$length)).'...';             
		 } else {
			return htmlspecialchars($text);
		 }
	}
}


### Function: HTML Special Chars Decode
if (!function_exists('htmlspecialchars_decode')) {
   function htmlspecialchars_decode($text) {
       return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
   }
}


### Function: Download URL
function download_file_url($file_id) {
	$file_id = intval($file_id);
	if(get_option('permalink_structure')) {
		$download_file_url = get_option('siteurl').'/download/'.$file_id.'/';
	} else {
		$download_file_url =  get_option('siteurl').'?dl_id='.$file_id;
	}
	return $download_file_url;
}


### Function: Download Category URL
function download_category_url($cat_id) {
	$download_page_url = get_option('download_page_url');
	if(strpos($download_page_url, '?') !== false) {
		$download_page_url = "$download_page_url&amp;dl_cat=$cat_id";
	} else {
		$download_page_url = "$download_page_url?dl_cat=$cat_id";
	}
	return $download_page_url;
}


### Function: Download Page Link
function download_page_link($page) {
	$current_url = $_SERVER['REQUEST_URI'];
	$curren_downloadpage = intval($_GET['dl_page']);
	$download_page_link = preg_replace('/dl_page=(\d+)/i', 'dl_page='.$page, $current_url);
	if($curren_downloadpage == 0) {
		if(strpos($current_url, '?') !== false) {
			$download_page_link = "$download_page_link&amp;dl_page=$page";
		} else {
			$download_page_link = "$download_page_link?dl_page=$page";
		}
	}
	return $download_page_link;
}


### Function: Place Download In Content
add_filter('the_content', 'place_download', '7');
add_filter('the_excerpt', 'place_download', '7');
function place_download($content){
	if(!is_feed()) {
		$content = preg_replace("/\[download=(\d+)\]/ise", "download_embedded('\\1')", $content);
	} else {
		$content = preg_replace("/\[download=(\d+)\]/i", __('Note: There is a file embedded within this post, please visit this post to download the file.', 'wp-downloadmanager'), $content);
	}
    return $content;
}


### Function: Place Download Page In Content
add_filter('the_content', 'place_downloadpage', '7');
function place_downloadpage($content){
	$content =preg_replace("/\[page_downloads\]/ise", "downloads_page()", $content); 
    return $content;
}


### Function: Download Embedded
function download_embedded($file_id) {
	global $wpdb, $user_ID;
	$file = $wpdb->get_row("SELECT * FROM $wpdb->downloads WHERE file_id = ".intval($file_id));
	if($file) {
		// Get Embedded
		$template_download_embedded = get_option('download_template_embedded');
		if(($file->file_permission == 1 && intval($user_ID) > 0) || $file->file_permission == 0) {
			$template_download_embedded = stripslashes($template_download_embedded[0]);
		} else {
			$template_download_embedded = stripslashes($template_download_embedded[1]);
		}
		$template_download_embedded = str_replace("%FILE_ID%", $file->file_id, $template_download_embedded);
		$template_download_embedded = str_replace("%FILE%", stripslashes($file->file), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_NAME%", stripslashes($file->file_name), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_DESCRIPTION%",  stripslashes($file->file_des), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_SIZE%",  format_filesize($file->file_size), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$cat_id]), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_DATE%",  gmdate(get_option('date_format'), $file->file_date), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_TIME%",  gmdate(get_option('time_format'), $file->file_date), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_HITS%", number_format($file->file_hits), $template_download_embedded);
		$template_download_embedded = str_replace("%FILE_DOWNLOAD_URL%", download_file_url($file->file_id), $template_download_embedded);
		return $template_download_embedded;
	}
}


### Function: Downloads Page
function downloads_page() {
	global $wpdb, $user_ID;
	// Variables
	$category = intval($_GET['dl_cat']);
	$page = intval($_GET['dl_page']);
	$download_categories = get_option('download_categories');
	$download_categories[0] = __('total', 'wp-downloadmanager');
	$category_stats = array();
	$total_stats = array('files' => 0, 'size' => 0, 'hits' => 0);
	$file_sort = get_option('download_sort');
	// If There Is Category Set
	$category_sql = '';
	if($category > 0) {
		$category_sql = "WHERE file_category = $category";
	}
	// Calculate Categories And Total Stats
	$categories = $wpdb->get_results("SELECT file_category, COUNT(file_id) as category_files, SUM(file_size) category_size, SUM(file_hits) as category_hits FROM $wpdb->downloads $category_sql GROUP BY file_category");
	if($categories) {
		foreach($categories as $cat) {
			$cat_id = intval($cat->file_category);
			$category_stats[$cat_id]['files'] = $cat->category_files;
			$category_stats[$cat_id]['hits'] = $cat->category_hits;
			$category_stats[$cat_id]['size'] = $cat->category_size;
			$total_stats['files'] +=$cat->category_files;
			$total_stats['hits'] += $cat->category_hits;
			$total_stats['size'] += $cat->category_size;
		}
	}
	// Checking $page and $offset
	if (empty($page) || $page == 0) { $page = 1; }
	if (empty($offset)) { $offset = 0; }
	// Determin $offset
	$offset = ($page-1) * $file_sort['perpage'];
	// Determine Max Number Of Downloads To Display On Page
	if(($offset + $file_sort['perpage']) > $total_stats['files']) { 
		$max_on_page = $total_stats['files']; 
	} else { 
		$max_on_page = ($offset + $file_sort['perpage']); 
	}
	// Determine Number Of Downloads To Display On Page
	if (($offset + 1) > ($total_stats['files'])) { 
		$display_on_page = $total_stats['files']; 
	} else { 
		$display_on_page = ($offset + 1); 
	}
	// Determing Total Amount Of Pages
	$total_pages = ceil($total_stats['files'] / $file_sort['perpage']);
	// Get Sorting Group
	$group_sql = '';
	if($file_sort['group'] == 1) {
		$group_sql = 'file_category ASC,';
	}
	// Get Files
	$files = $wpdb->get_results("SELECT * FROM $wpdb->downloads $category_sql ORDER BY $group_sql {$file_sort['by']} {$file_sort['order']} LIMIT $offset, {$file_sort['perpage']}");
	if($files) {
		// Get Download Page Header
		$template_download_header = stripslashes(get_option('download_template_header'));
		$template_download_header = str_replace("%TOTAL_FILES_COUNT%", number_format($total_stats['files']), $template_download_header);
		$template_download_header = str_replace("%TOTAL_HITS%", number_format($total_stats['hits']), $template_download_header);
		$template_download_header = str_replace("%TOTAL_SIZE%", format_filesize($total_stats['size']), $template_download_header);
		$template_download_header = str_replace("%RECORD_START%", number_format($display_on_page), $template_download_header);
		$template_download_header = str_replace("%RECORD_END%", number_format($max_on_page), $template_download_header);
		$template_download_header = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$category]), $template_download_header);
		$output = $template_download_header;
		// Loop Through Files
		$i = 1;
		$k = 1;
		$temp_cat_id = -1;
		foreach($files as $file) {
			$cat_id = intval($file->file_category);
			// Print Out Category Header
			if($temp_cat_id != $cat_id && $file_sort['group'] == 1) {
				// Get Download Category Header
				$template_download_category_header = stripslashes(get_option('download_template_category_header'));
				$template_download_category_header = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$cat_id]), $template_download_category_header);
				$template_download_category_header = str_replace("%CATEGORY_URL%", download_category_url($cat_id), $template_download_category_header);
				$template_download_category_header = str_replace("%CATEGORY_FILES_COUNT%", number_format($category_stats[$cat_id]['files']), $template_download_category_header);
				$template_download_category_header = str_replace("%CATEGORY_HITS%", number_format($category_stats[$cat_id]['hits']), $template_download_category_header);
				$template_download_category_header = str_replace("%CATEGORY_SIZE%", format_filesize($category_stats[$cat_id]['size']), $template_download_category_header);
				$output .= $template_download_category_header;
				$i = 1;
			}
			// Get Download Listing
			$template_download_listing = get_option('download_template_listing');
			if(($file->file_permission == 1 && intval($user_ID) > 0) || $file->file_permission == 0) {
				$template_download_listing = stripslashes($template_download_listing[0]);
			} else {
				$template_download_listing = stripslashes($template_download_listing[1]);
			}
			$template_download_listing = str_replace("%FILE_ID%", $file->file_id, $template_download_listing);
			$template_download_listing = str_replace("%FILE%", stripslashes($file->file), $template_download_listing);
			$template_download_listing = str_replace("%FILE_NAME%", stripslashes($file->file_name), $template_download_listing);
			$template_download_listing = str_replace("%FILE_DESCRIPTION%",  stripslashes($file->file_des), $template_download_listing);
			$template_download_listing = str_replace("%FILE_SIZE%",  format_filesize($file->file_size), $template_download_listing);
			$template_download_listing = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$cat_id]), $template_download_listing);
			$template_download_listing = str_replace("%FILE_DATE%",  gmdate(get_option('date_format'), $file->file_date), $template_download_listing);
			$template_download_listing = str_replace("%FILE_TIME%",  gmdate(get_option('time_format'), $file->file_date), $template_download_listing);
			$template_download_listing = str_replace("%FILE_HITS%", number_format($file->file_hits), $template_download_listing);
			$template_download_listing = str_replace("%FILE_DOWNLOAD_URL%", download_file_url($file->file_id), $template_download_listing);
			$output .= $template_download_listing;
			// Print Out Category Footer
			if(($i == $category_stats[$cat_id]['files'] || $k == sizeof($files)) && $file_sort['group'] == 1) {
				// Get Download Category Footer
				$template_download_category_footer = stripslashes(get_option('download_template_category_footer'));
				$template_download_category_footer = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$cat_id]), $template_download_category_footer);
				$template_download_category_footer = str_replace("%CATEGORY_URL%", download_category_url($cat_id), $template_download_category_footer);
				$template_download_category_footer = str_replace("%CATEGORY_FILES_COUNT%", number_format($category_stats[$cat_id]['files']), $template_download_category_footer);
				$template_download_category_footer = str_replace("%CATEGORY_HITS%", number_format($category_stats[$cat_id]['hits']), $template_download_category_footer);
				$template_download_category_footer = str_replace("%CATEGORY_SIZE%", format_filesize($category_stats[$cat_id]['size']), $template_download_category_footer);
				$output .= $template_download_category_footer;
			}
			// Assign Cat ID To Temp Cat ID
			$temp_cat_id = $cat_id;
			// Count Files
			$i++;
			$k++;
		}
		// Get Download Page Footer
		$template_download_footer = stripslashes(get_option('download_template_footer'));
		$template_download_footer = str_replace("%TOTAL_FILES_COUNT%", number_format($total_stats['files']), $template_download_footer);
		$template_download_footer = str_replace("%TOTAL_HITS%", number_format($total_stats['hits']), $template_download_footer);
		$template_download_footer = str_replace("%TOTAL_SIZE%", format_filesize($total_stats['size']), $template_download_footer);
		$template_download_footer = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$category]), $template_download_footer);
		$output .= $template_download_footer;
	}
	// Download Paging
	if($total_pages > 1) {
		// Output Previous Page
		$output .= "<p>\n";
		$output .= "<span style=\"float: left;\">\n";
		if($page > 1 && ((($page*$file_sort['perpage'])-($file_sort['perpage']-1)) <= $total_stats['files'])) {
			$output .= '<strong>&laquo;</strong> <a href="'.download_page_link($page-1).'" title="&laquo; '.__('Previous Page', 'wp-downloadmanager').'">'.__('Previous Page', 'wp-downloadmanager').'</a>';
		} else {
			$output .= '&nbsp;';
		}		
		$output .= "</span>\n";
		// Output Next Page
		$output .= "<span style=\"float: right;\">\n";
		if($page >= 1 && ((($page*$file_sort['perpage'])+1) <=  $total_stats['files'])) {
			$output .= '<a href="'.download_page_link($page+1).'" title="'.__('Next Page', 'wp-downloadmanager').' &raquo;">'.__('Next Page', 'wp-downloadmanager').'</a> <strong>&raquo;</strong>';
		} else {
			$output .= '&nbsp;';
		}
		$output .= "</span>\n";
		// Output Pages
		$output .= "</p>\n";
		$output .= "<br style=\"clear: both;\" />\n";
		$output .= "<p style=\"text-align: center;\">\n";
		$output .= __('Pages', 'wp-downloadmanager')." ($total_pages): ";
		if ($page >= 4) {
			$output .= '<strong><a href="'.download_page_link(1).'" title="'.__('Go to First Page', 'wp-downloadmanager').'">&laquo; '.__('First', 'wp-downloadmanager').'</a></strong> ... ';
		}
		if($page > 1) {
			$output .= ' <strong><a href="'.download_page_link($page-1).'" title="&laquo; '.__('Go to Page', 'wp-downloadmanager').' '.($page-1).'">&laquo;</a></strong> ';
		}
		for($i = $page - 2 ; $i  <= $page +2; $i++) {
			if ($i >= 1 && $i <= $total_pages) {
				if($i == $page) {
					$output .= "<strong>[$i]</strong> ";
				} else {
					$output .= '<a href="'.download_page_link($i).'" title="'.__('Page', 'wp-downloadmanager').' '.$i.'">'.$i.'</a> ';
				}
			}
		}
		if($page < $total_pages) {
			$output .= ' <strong><a href="'.download_page_link($page+1).'" title="'.__('Go to Page', 'wp-downloadmanager').' '.($page+1).' &raquo;">&raquo;</a></strong> ';
		}
		if (($page+2) < $total_pages) {
			$output .= ' ... <strong><a href="'.download_page_link($total_pages).'" title="'.__('Go to Last Page', 'wp-downloadmanager').'">'.__('Last', 'wp-downloadmanager').' &raquo;</a></strong>';
		}
		$output .= "</p>\n";
	}
	return $output;
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
	echo '<select id="file_timestamp_day" name="file_timestamp_day" size="1">'."\n";
	for($i = 1; $i <=31; $i++) {
		if($day == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>&nbsp;&nbsp;'."\n";
	$month2 = gmdate('n', $file_timestamp);
	echo '<select id="file_timestamp_month" name="file_timestamp_month" size="1">'."\n";
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
	echo '<select id="file_timestamp_year" name="file_timestamp_year" size="1">'."\n";
	for($i = 2000; $i <= gmdate('Y'); $i++) {
		if($year == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>&nbsp;@'."\n";
	$hour = gmdate('H', $file_timestamp);
	echo '<select id="file_timestamp_hour" name="file_timestamp_hour" size="1">'."\n";
	for($i = 0; $i < 24; $i++) {
		if($hour == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>&nbsp;:'."\n";
	$minute = gmdate('i', $file_timestamp);
	echo '<select id="file_timestamp_minute" name="file_timestamp_minute" size="1">'."\n";
	for($i = 0; $i < 60; $i++) {
		if($minute == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	
	echo '</select>&nbsp;:'."\n";
	$second = gmdate('s', $file_timestamp);
	echo '<select id="file_timestamp_second" name="file_timestamp_second" size="1">'."\n";
	for($i = 0; $i <= 60; $i++) {
		if($second == $i) {
			echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";	
		} else {
			echo "<option value=\"$i\">$i</option>\n";	
		}
	}
	echo '</select>'."\n";
}


### Function: Get Total Download Files
function get_download_files($display = true) {
	global $wpdb;
	$totalfiles = $wpdb->get_var("SELECT COUNT(file_id) FROM $wpdb->downloads");
	if($display) {
		echo number_format($totalfiles);
	} else {
		return number_format($totalfiles);
	}
}


### Function Get Total Download Size
function get_download_size($display = true) {
	global $wpdb;
	$totalsize = $wpdb->get_var("SELECT SUM(file_size) FROM $wpdb->downloads");
	if($display) {
		echo format_filesize($totalsize);
	} else {
		return format_filesize($totalsize);
	}
}


### Function: Get Total Download Hits
function get_download_hits($display = true) {
	global $wpdb;
	$totalhits = $wpdb->get_var("SELECT SUM(file_hits) FROM $wpdb->downloads");
	if($display) {
		echo number_format($totalhits);
	} else {
		return number_format($totalhits);
	}
}


### Function: Get Most Downloaded Files
if(!function_exists('get_most_downloaded')) {
	function get_most_downloaded($limit = 10, $chars = 0, $display = true) {
		global $wpdb, $user_ID;
		$output = '';
		$files = $wpdb->get_results("SELECT * FROM $wpdb->downloads ORDER BY file_hits DESC LIMIT $limit");
		if($files) {
			foreach($files as $file) {
				// Get Most Downloaded
				$template_download_most = get_option('download_template_most');
				if(($file->file_permission == 1 && intval($user_ID) > 0) || $file->file_permission == 0) {
					$template_download_most = stripslashes($template_download_most[0]);
				} else {
					$template_download_most = stripslashes($template_download_most[1]);
				}
				if($chars > 0) {
					$file_name = snippet_chars(stripslashes($file->file_name), $chars);
				} else {
					$file_name = stripslashes($file->file_name);
				}
				$template_download_most = str_replace("%FILE_ID%", $file->file_id, $template_download_most);
				$template_download_most = str_replace("%FILE%", stripslashes($file->file), $template_download_most);
				$template_download_most = str_replace("%FILE_NAME%", $file_name, $template_download_most);
				$template_download_most = str_replace("%FILE_DESCRIPTION%",  stripslashes($file->file_des), $template_download_most);
				$template_download_most = str_replace("%FILE_SIZE%",  format_filesize($file->file_size), $template_download_most);
				$template_download_most = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$cat_id]), $template_download_most);
				$template_download_most = str_replace("%FILE_DATE%",  gmdate(get_option('date_format'), $file->file_date), $template_download_most);
				$template_download_most = str_replace("%FILE_TIME%",  gmdate(get_option('time_format'), $file->file_date), $template_download_most);
				$template_download_most = str_replace("%FILE_HITS%", number_format($file->file_hits), $template_download_most);
				$template_download_most = str_replace("%FILE_DOWNLOAD_URL%", download_file_url($file->file_id), $template_download_most);
				$output .= $template_download_most;
			}
		} else {
			$output = '<li>'.__('N/A', 'wp-downloadmanager').'</li>'."\n";
		}
		if($display) {
			echo $output;
		} else {
			return $output;
		}
	}
}


### Function: Get Newest Downloads
if(!function_exists('get_newest_downloads')) {
	function get_newest_downloads($limit = 10, $chars = 0, $display = true) {
		global $wpdb, $user_ID;
		$output = '';
		$files = $wpdb->get_results("SELECT * FROM $wpdb->downloads ORDER BY file_date DESC LIMIT $limit");
		if($files) {
			foreach($files as $file) {
				// Get Newest Downloads
				$template_download_most = get_option('download_template_most');
				if(($file->file_permission == 1 && intval($user_ID) > 0) || $file->file_permission == 0) {
					$template_download_most = stripslashes($template_download_most[0]);
				} else {
					$template_download_most = stripslashes($template_download_most[1]);
				}
				if($chars > 0) {
					$file_name = snippet_chars(stripslashes($file->file_name), $chars);
				} else {
					$file_name = stripslashes($file->file_name);
				}
				$template_download_most = str_replace("%FILE_ID%", $file->file_id, $template_download_most);
				$template_download_most = str_replace("%FILE%", stripslashes($file->file), $template_download_most);
				$template_download_most = str_replace("%FILE_NAME%", $file_name, $template_download_most);
				$template_download_most = str_replace("%FILE_DESCRIPTION%",  stripslashes($file->file_des), $template_download_most);
				$template_download_most = str_replace("%FILE_SIZE%",  format_filesize($file->file_size), $template_download_most);
				$template_download_most = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$cat_id]), $template_download_most);
				$template_download_most = str_replace("%FILE_DATE%",  gmdate(get_option('date_format'), $file->file_date), $template_download_most);
				$template_download_most = str_replace("%FILE_TIME%",  gmdate(get_option('time_format'), $file->file_date), $template_download_most);
				$template_download_most = str_replace("%FILE_HITS%", number_format($file->file_hits), $template_download_most);
				$template_download_most = str_replace("%FILE_DOWNLOAD_URL%", download_file_url($file->file_id), $template_download_most);
				$output .= $template_download_most;
			}
		} else {
			$output = '<li>'.__('N/A', 'wp-downloadmanager').'</li>'."\n";
		}
		if($display) {
			echo $output;
		} else {
			return $output;
		}
	}
}


### Function: Get Downloads By Category ID
if(!function_exists('get_downloads_category')) {
	function get_downloads_category($cat_id = 1, $limit = 10, $chars = 0, $display = true) {
		global $wpdb, $user_ID;
		$cat_id = intval($cat_id);
		$output = '';
		$files = $wpdb->get_results("SELECT * FROM $wpdb->downloads WHERE file_category = $cat_id ORDER BY file_date DESC LIMIT $limit");
		if($files) {
			foreach($files as $file) {
				// Get Downloads By Category ID
				$template_download_most = get_option('download_template_most');
				if(($file->file_permission == 1 && intval($user_ID) > 0) || $file->file_permission == 0) {
					$template_download_most = stripslashes($template_download_most[0]);
				} else {
					$template_download_most = stripslashes($template_download_most[1]);
				}
				if($chars > 0) {
					$file_name = snippet_chars(stripslashes($file->file_name), $chars);
				} else {
					$file_name = stripslashes($file->file_name);
				}
				$template_download_most = str_replace("%FILE_ID%", $file->file_id, $template_download_most);
				$template_download_most = str_replace("%FILE%", stripslashes($file->file), $template_download_most);
				$template_download_most = str_replace("%FILE_NAME%", $file_name, $template_download_most);
				$template_download_most = str_replace("%FILE_DESCRIPTION%",  stripslashes($file->file_des), $template_download_most);
				$template_download_most = str_replace("%FILE_SIZE%",  format_filesize($file->file_size), $template_download_most);
				$template_download_most = str_replace("%FILE_CATEGORY_NAME%", stripslashes($download_categories[$cat_id]), $template_download_most);
				$template_download_most = str_replace("%FILE_DATE%",  gmdate(get_option('date_format'), $file->file_date), $template_download_most);
				$template_download_most = str_replace("%FILE_TIME%",  gmdate(get_option('time_format'), $file->file_date), $template_download_most);
				$template_download_most = str_replace("%FILE_HITS%", number_format($file->file_hits), $template_download_most);
				$template_download_most = str_replace("%FILE_DOWNLOAD_URL%", download_file_url($file->file_id), $template_download_most);
				$output .= $template_download_most;
			}
		} else {
			$output = '<li>'.__('N/A', 'wp-downloadmanager').'</li>'."\n";
		}
		if($display) {
			echo $output;
		} else {
			return $output;
		}
	}
}


### Function: Plug Into WP-Stats
if(strpos(get_option('stats_url'), $_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], 'stats-options.php') || strpos($_SERVER['REQUEST_URI'], 'stats/stats.php')) {
	add_filter('wp_stats_page_admin_plugins', 'downloadmanager_page_admin_general_stats');
	add_filter('wp_stats_page_admin_recent', 'downloadmanager_page_admin_recent_stats');
	add_filter('wp_stats_page_admin_most', 'downloadmanager_page_admin_most_stats');
	add_filter('wp_stats_page_plugins', 'downloadmanager_page_general_stats');
	add_filter('wp_stats_page_recent', 'downloadmanager_page_recent_stats');
	add_filter('wp_stats_page_most', 'downloadmanager_page_most_stats');
}


### Function: Add WP-DownloadManager General Stats To WP-Stats Page Options
function downloadmanager_page_admin_general_stats($content) {
	$stats_display = get_option('stats_display');
	if($stats_display['downloads'] == 1) {
		$content .= '<input type="checkbox" name="stats_display[]" value="downloads" checked="checked" />&nbsp;&nbsp;'.__('WP-DownloadManager', 'wp-downloadmanager').'<br />'."\n";
	} else {
		$content .= '<input type="checkbox" name="stats_display[]" value="downloads" />&nbsp;&nbsp;'.__('WP-DownloadManager', 'wp-downloadmanager').'<br />'."\n";
	}
	return $content;
}


### Function: Add WP-DownloadManager Top Recent Stats To WP-Stats Page Options
function downloadmanager_page_admin_recent_stats($content) {
	$stats_display = get_option('stats_display');
	$stats_mostlimit = intval(get_option('stats_mostlimit'));
	if($stats_display['recent_downloads'] == 1) {
		$content .= '<input type="checkbox" name="stats_display[]" value="recent_downloads" checked="checked" />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Recent Downloads', 'wp-downloadmanager').'<br />'."\n";
	} else {
		$content .= '<input type="checkbox" name="stats_display[]" value="recent_downloads" />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Recent Downloads', 'wp-downloadmanager').'<br />'."\n";
	}
	return $content;
}


### Function: Add WP-DownloadManager Top Most/Highest Stats To WP-Stats Page Options
function downloadmanager_page_admin_most_stats($content) {
	$stats_display = get_option('stats_display');
	$stats_mostlimit = intval(get_option('stats_mostlimit'));
	if($stats_display['downloaded_most'] == 1) {
		$content .= '<input type="checkbox" name="stats_display[]" value="downloaded_most" checked="checked" />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Downloaded Files', 'wp-downloadmanager').'<br />'."\n";
	} else {
		$content .= '<input type="checkbox" name="stats_display[]" value="downloaded_most" />&nbsp;&nbsp;'.$stats_mostlimit.' '.__('Most Downloaded Files', 'wp-downloadmanager').'<br />'."\n";
	}
	return $content;
}


### Function: Add WP-DownloadManager General Stats To WP-Stats Page
function downloadmanager_page_general_stats($content) {
	global $wpdb;
	$stats_display = get_option('stats_display');
	if($stats_display['downloads'] == 1) {
		$download_stats = $wpdb->get_row("SELECT COUNT(file_id) as total_files, SUM(file_size) total_size, SUM(file_hits) as total_hits FROM $wpdb->downloads");
		$content .= '<p><strong>'.__('WP-DownloadManager', 'wp-downloadmanager').'</strong></p>'."\n";
		$content .= '<ul>'."\n";
		$content .= '<li><strong>'.number_format($download_stats->total_files).'</strong> '.__('files were added.', 'wp-downloadmanager').'</li>'."\n";
		$content .= '<li><strong>'.format_filesize($download_stats->total_size).'</strong> '.__('worth of files.', 'wp-downloadmanager').'</li>'."\n";
		$content .= '<li><strong>'.number_format($download_stats->total_hits).'</strong> '.__('hits were generated.', 'wp-downloadmanager').'</li>'."\n";
		$content .= '</ul>'."\n";
	}
	return $content;
}


### Function: Add WP-DownloadManager Top Recent Stats To WP-Stats Page
function downloadmanager_page_recent_stats($content) {
	$stats_display = get_option('stats_display');
	$stats_mostlimit = intval(get_option('stats_mostlimit'));
	if($stats_display['recent_downloads'] == 1) {
		$content .= '<p><strong>'.$stats_mostlimit.' '.__('Recent Downloads', 'wp-downloadmanager').'</strong></p>'."\n";
		$content .= '<ul>'."\n";
		$content .= get_newest_downloads($stats_mostlimit, 0, false);
		$content .= '</ul>'."\n";
	}
	return $content;
}


### Function: Add WP-DownloadManager Top Most/Highest Stats To WP-Stats Page
function downloadmanager_page_most_stats($content) {
	$stats_display = get_option('stats_display');
	$stats_mostlimit = intval(get_option('stats_mostlimit'));
	if($stats_display['downloaded_most'] == 1) {
		$content .= '<p><strong>'.$stats_mostlimit.' '.__('Most Downloaded Files', 'wp-downloadmanager').'</strong></p>'."\n";
		$content .= '<ul>'."\n";
		$content .= get_most_downloaded($stats_mostlimit, 0, false);
		$content .= '</ul>'."\n";
	}
	return $content;
}


### Function: Create Downloads Table
add_action('activate_downloadmanager/downloadmanager.php', 'create_download_table');
function create_download_table() {
	global $wpdb;
	include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
	// Create WP-Downloads Table
	$create_table = "CREATE TABLE $wpdb->downloads (".
							"file_id int(10) NOT NULL auto_increment,".
							"file tinytext NOT NULL,".
							"file_name text character set utf8 NOT NULL,".
							"file_des text character set utf8 NOT NULL,".
							"file_size varchar(20) NOT NULL default '',".
							"file_category int(2) NOT NULL default '0',".
							"file_date varchar(20) NOT NULL default '',".
							"file_hits int(10) NOT NULL default '0',".
							"file_permission TINYINT(2) NOT NULL default '0',".
							"PRIMARY KEY  (file_id));";
	maybe_create_table($wpdb->downloads, $create_table);
	// To Be Deleted When Released
	maybe_add_column($wpdb->downloads, 'file_permission', "ALTER TABLE $wpdb->downloads ADD file_permission TINYINT(2) NOT NULL DEFAULT '0'");
	$wpdb->query("ALTER TABLE $wpdb->downloads CHANGE file file TINYTEXT");
	delete_option('widget_download_newest_downloads');
	// WP-Downloads Options
	add_option('download_path', ABSPATH.'wp-content/files', 'Download Path');
	add_option('download_path_url', get_option('siteurl').'/wp-content/files', 'Download Path URL');
	add_option('download_page_url', get_option('siteurl').'/downloads/', 'Download Page URL');
	add_option('download_method', 0, 'Download Type');
	add_option('download_categories', array('General'), 'Download Categories');
	add_option('download_sort', array('by' => 'file_name', 'order' => 'asc', 'perpage' => 20, 'group' => 1), 'Download Sorting Options');
	add_option('download_template_header', '<p>'.__('There are <strong>%TOTAL_FILES_COUNT% files</strong>, weighing <strong>%TOTAL_SIZE%</strong> with <strong>%TOTAL_HITS% hits</strong> in <strong>%FILE_CATEGORY_NAME%</strong>.</p><p>Displaying <strong>%RECORD_START%</strong> to <strong>%RECORD_END%</strong> of <strong>%TOTAL_FILES_COUNT%</strong> files.', 'wp-downloadmanager').'</p>', 'Download Page Header Template');
	add_option('download_template_footer', '', 'Download Page Footer Template');
	add_option('download_template_category_header', '<h2><a href="%CATEGORY_URL%" title="'.__('View all downloads in %FILE_CATEGORY_NAME%', 'wp-downloadmanager').'">%FILE_CATEGORY_NAME%</a></h2>', 'Download Category Header Template');
	add_option('download_template_category_footer', '', 'Download Category FooterTemplate');
	add_option('download_template_listing', array('<p><img src="'.get_option('siteurl').'/wp-content/plugins/downloadmanager/images/drive_go.gif" alt="'.__('Download: %FILE_NAME%', 'wp-downloadmanager').'" title="'.__('Download: %FILE_NAME%', 'wp-downloadmanager').'" style="vertical-align: middle;" />&nbsp;&nbsp;<strong><a href="%FILE_DOWNLOAD_URL%" title="'.__('Download: %FILE_NAME%', 'wp-downloadmanager').'">%FILE_NAME%</a></strong><br /><strong>&raquo; %FILE_SIZE% - %FILE_HITS% '.__('hits', 'wp-downloadmanager').' - %FILE_DATE%</strong><br />%FILE_DESCRIPTION%</p>', '<p><img src="'.get_option('siteurl').'/wp-content/plugins/downloadmanager/images/drive_go.gif" alt="" title="" style="vertical-align: middle;" />&nbsp;&nbsp;<strong>%FILE_NAME%</strong><br /><strong>&raquo; %FILE_SIZE% - %FILE_HITS% '.__('hits', 'wp-downloadmanager').' - %FILE_DATE%</strong><br /><i>'.__('You need to be a registered user to download this file.', 'wp-downloadmanager').'</i><br />%FILE_DESCRIPTION%</p>'), 'Download Listing Template');
	add_option('download_template_embedded', array('<p><img src="'.get_option('siteurl').'/wp-content/plugins/downloadmanager/images/drive_go.gif" alt="'.__('Download: %FILE_NAME%', 'wp-downloadmanager').'" title="'.__('Download: %FILE_NAME%', 'wp-downloadmanager').'" style="vertical-align: middle;" />&nbsp;&nbsp;<strong><a href="%FILE_DOWNLOAD_URL%" title="'.__('Download: %FILE_NAME%', 'wp-downloadmanager').'">%FILE_NAME%</a></strong> (%FILE_SIZE%, %FILE_HITS% '.__('hits', 'wp-downloadmanager').')</p>', '<p><img src="'.get_option('siteurl').'/wp-content/plugins/downloadmanager/images/drive_go.gif" alt="" title="" style="vertical-align: middle;" />&nbsp;&nbsp;<strong>%FILE_NAME%</strong> (%FILE_SIZE%, %FILE_HITS% '.__('hits', 'wp-downloadmanager').')<br /><i>'.__('You need to be a registered user to download this file.', 'wp-downloadmanager').'</i></p>'), 'Download Embedded Template');
	add_option('download_template_most', array('<li><strong><a href="%FILE_DOWNLOAD_URL%" title="'.__('Download: %FILE_NAME%', 'wp-downloadmanager').'">%FILE_NAME%</a></strong> (%FILE_SIZE%, %FILE_HITS% '.__('hits', 'wp-downloadmanager').')</li>', '<li><strong>%FILE_NAME%</strong> (%FILE_SIZE%, %FILE_HITS% '.__('hits', 'wp-downloadmanager').')<br /><i>'.__('You need to be a registered user to download this file.', 'wp-downloadmanager').'</i></li>'), 'Most Download Template');
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