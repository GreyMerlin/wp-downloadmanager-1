<?php
/*
Plugin Name: WP-DownloadManager Widget
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Adds a Download Widget to display most downloaded file and recent downloads on your sidebar. You will need to activate WP-DownloadManager first.
Version: 1.41
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
*/


/*  
	Copyright 2009  Lester Chan  (email : lesterchan@gmail.com)

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


### Function: Init WP-DownloadManager Widget
function widget_download_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	### Function: WP-DownloadManager Most Downloaded Widget
	function widget_download_most_downloaded($args) {
		extract($args);
		$options = get_option('widget_download_most_downloaded');
		$title = htmlspecialchars(stripslashes($options['title']));		
		if (function_exists('get_most_downloaded')) {
			echo $before_widget.$before_title.$title.$after_title;
			echo '<ul>'."\n";
			get_most_downloaded($options['limit'], $options['chars']);
			echo '</ul>'."\n";
			if(intval($options['link']) == 1) {
				$download_template_download_page_link = stripslashes(get_option('download_template_download_page_link'));
				$download_template_download_page_link = str_replace('%DOWNLOAD_PAGE_URL%', get_option('download_page_url'), $download_template_download_page_link);
				echo $download_template_download_page_link;
			}
			echo $after_widget;
		}		
	}

	### Function: WP-DownloadManager Recent Downloads Widget
	function widget_download_recent_downloads($args) {
		extract($args);
		$options = get_option('widget_download_recent_downloads');
		$title = htmlspecialchars(stripslashes($options['title']));		
		if (function_exists('get_recent_downloads')) {
			echo $before_widget.$before_title.$title.$after_title;
			echo '<ul>'."\n";
			get_recent_downloads($options['limit'], $options['chars']);
			echo '</ul>'."\n";
			if(intval($options['link']) == 1) {
				$download_template_download_page_link = stripslashes(get_option('download_template_download_page_link'));
				$download_template_download_page_link = str_replace('%DOWNLOAD_PAGE_URL%', get_option('download_page_url'), $download_template_download_page_link);
				echo $download_template_download_page_link;
			}
			echo $after_widget;
		}		
	}

	### Function: WP-DownloadManager Most Downloaded Widget Options
	function widget_download_most_downloaded_options() {
		$options = get_option('widget_download_most_downloaded');
		if (!is_array($options)) {
			$options = array('title' => __('Most Downloaded', 'wp-downloadmanager'), 'limit' => 10, 'chars' => 0, 'link' => 0);
		}
		if ($_POST['most_downloaded-submit']) {
			$options['title'] = strip_tags($_POST['most_downloaded-title']);
			$options['limit'] = intval($_POST['most_downloaded-limit']);
			$options['chars'] = intval($_POST['most_downloaded-chars']);
			$options['link'] = intval($_POST['most_downloaded-link']);
			update_option('widget_download_most_downloaded', $options);
		}
		echo '<p><label for="most_downloaded-title">';
		_e('Title', 'wp-downloadmanager');
		echo ': </label><input type="text" id="most_downloaded-title" name="most_downloaded-title" value="'.htmlspecialchars(stripslashes($options['title'])).'" /></p>'."\n";
		echo '<p><label for="most_downloaded-limit">';
		_e('Limit', 'wp-downloadmanager');
		echo ': </label><input type="text" id="most_downloaded-limit" name="most_downloaded-limit" value="'.intval($options['limit']).'" size="3" /></p>'."\n";
		echo '<p><label for="most_downloaded-chars">';
		_e('Post Title Length (Characters)', 'wp-downloadmanager');
		echo ': </label><input type="text" id="most_downloaded-chars" name="most_downloaded-chars" value="'.intval($options['chars']).'" size="5" />&nbsp;&nbsp;'."\n";
		_e('(<strong>0</strong> to disable)', 'wp-downloadmanager');
		echo '</p>'."\n";
		echo '<p><label for="most_downloaded-link">';
		_e('Display Link To Download Page?', 'wp-downloadmanager');
		echo '</label><select id="most_downloaded-link" name="most_downloaded-link" size="1">'."\n";
		echo '<option value="0"';
		selected('0', $options['link']);
		echo '>';
		_e('No', 'wp-downloadmanager');
		echo '</option>'."\n";
		echo '<option value="1"';
		selected('1', $options['link']);
		echo '>';
		_e('Yes', 'wp-downloadmanager');
		echo '</option>'."\n";
		echo '</select></p>'."\n";
		echo '<input type="hidden" id="most_downloaded-submit" name="most_downloaded-submit" value="1" />'."\n";
	}

	### Function: WP-DownloadManager Recend Downloads Widget Options
	function widget_download_recent_downloads_options() {
		$options = get_option('widget_download_recent_downloads');
		if (!is_array($options)) {
			$options = array('title' => __('Recent Downloads', 'wp-downloadmanager'), 'limit' => 10, 'chars' => 0, 'link' => 0);
		}
		if ($_POST['recent_downloads-submit']) {
			$options['title'] = strip_tags($_POST['recent_downloads-title']);
			$options['limit'] = intval($_POST['recent_downloads-limit']);
			$options['chars'] = intval($_POST['recent_downloads-chars']);
			$options['link'] = intval($_POST['recent_downloads-link']);
			update_option('widget_download_recent_downloads', $options);
		}
		echo '<p><label for="recent_downloads-title">';
		_e('Title', 'wp-downloadmanager');
		echo ': </label><input type="text" id="recent_downloads-title" name="recent_downloads-title" value="'.htmlspecialchars(stripslashes($options['title'])).'" /></p>'."\n";
		echo '<p><label for="recent_downloads-limit">';
		_e('Limit', 'wp-downloadmanager');
		echo ': </label><input type="text" id="recent_downloads-limit" name="recent_downloads-limit" value="'.intval($options['limit']).'" size="3" /></p>'."\n";
		echo '<p><label for="recent_downloads-chars">';
		_e('Post Title Length (Characters)', 'wp-downloadmanager');
		echo ': </label><input type="text" id="recent_downloads-chars" name="recent_downloads-chars" value="'.intval($options['chars']).'" size="5" />&nbsp;&nbsp;'."\n";
		_e('(<strong>0</strong> to disable)', 'wp-downloadmanager');
		echo '</p>'."\n";
		echo '<p><label for="recent_downloads-link">';
		_e('Display Link To Download Page?', 'wp-downloadmanager');
		echo '</label><select id="recent_downloads-link" name="recent_downloads-link" size="1">'."\n";
		echo '<option value="0"';
		selected('0', $options['link']);
		echo '>';
		_e('No', 'wp-downloadmanager');
		echo '</option>'."\n";
		echo '<option value="1"';
		selected('1', $options['link']);
		echo '>';
		_e('Yes', 'wp-downloadmanager');
		echo '</option>'."\n";
		echo '</select></p>'."\n";
		echo '<input type="hidden" id="recent_downloads-submit" name="recent_downloads-submit" value="1" />'."\n";
	}
	// Register Widgets
	register_sidebar_widget(array('Most Downloaded', 'wp-downloadmanager'), 'widget_download_most_downloaded');
	register_widget_control(array('Most Downloaded', 'wp-downloadmanager'), 'widget_download_most_downloaded_options', 400, 200);
	register_sidebar_widget(array('Recent Downloads', 'wp-downloadmanager'), 'widget_download_recent_downloads');
	register_widget_control(array('Recent Downloads', 'wp-downloadmanager'), 'widget_download_recent_downloads_options', 400, 200);
}


### Function: Load The WP-DownloadManager Widget
add_action('plugins_loaded', 'widget_download_init')
?>
