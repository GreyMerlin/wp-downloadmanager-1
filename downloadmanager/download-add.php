<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-DownloadManager 1.00						|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Add File Download																|
|	- wp-content/plugins/downloadmanager/download-add.php			|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Downloads
if(!current_user_can('manage_downloads')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('downloadmanager/download-manager.php');
$base_page = 'admin.php?page='.$base_name;
$file_path = get_option('download_path');
$file_categories = get_option('download_categories');


### Form Processing 
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		// Add File
		case __('Add File', 'wp-downloadmanager'):
			if(intval($_POST['file_upload']) == 1) {
				if($_FILES['file']['size'] > get_max_upload_size()) {
					$text = '<font color="red">'.sprintf(__('File Size Too Large. Maximum Size Is %s', 'wp-downloadmanager'), format_size(get_max_upload_size())).'</font>';
					break;
				} else {
					if(is_uploaded_file($_FILES['file']['tmp_name'])) {
						if($_POST['file_upload_to'] == '/') {
							$file_upload_to = '/';
						} else {
							$file_upload_to = $_POST['file_upload_to'].'/';
						}
						if(move_uploaded_file($_FILES['file']['tmp_name'], $file_path.$file_upload_to.basename($_FILES['file']['name']))) {
							$file = $file_upload_to.basename($_FILES['file']['name']);
						} else {
							$text = '<font color="red">'.__('Error In Uploading File', 'wp-downloadmanager').'</font>';
							break;
						}
					} else {
						$text = '<font color="red">'.__('Error In Uploading File', 'wp-downloadmanager').'</font>';
						break;
					}
				}
			} else {
				$file = addslashes(trim($_POST['file']));
			}
			if(is_file($file_path.$file)) {
				$file_name= addslashes(trim($_POST['file_name']));
				if(empty($file_name)) {
					$file_name = $file;
				}
				$file_des = addslashes(trim($_POST['file_des']));
				$file_category = intval($_POST['file_cat']); 
				$file_hits = intval($_POST['file_hits']);
				$file_size = filesize($file_path.$file);
				$file_date = current_time('timestamp');
				$addfile = $wpdb->query("INSERT INTO $wpdb->downloads VALUES (0, '$file', '$file_name', '$file_des', '$file_size', $file_category, '$file_date', $file_hits)");
				if(!$addfile) {
					$text = '<font color="red">'.sprintf(__('Error In Adding File \'%s (%s)\'', 'wp-downloadmanager'), $file_name, $file).'</font>';
				} else {
					$text = '<font color="green">'.sprintf(__('File \'%s (%s)\' Added Successfully', 'wp-downloadmanager'), $file_name, $file).'</font>';
				}
			} else {
					$text = '<font color="red">'.sprintf(__('Invaild File \'%s\'', 'wp-downloadmanager'), $file).'</font>';
			}
			break;
	}
}
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
<!-- Add A File -->
<div class="wrap">
	<h2><?php _e('Add A File', 'wp-downloadmanager'); ?></h2>
	<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_max_upload_size(); ?>" />
		<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr>
				<td valign="top"><strong><?php _e('File:', 'wp-downloadmanager') ?></strong></td>
				<td>
					<input type="radio" name="file_upload" value="0" checked="checked" />&nbsp;&nbsp;<?php _e('Browse File:', 'wp-downloadmanager'); ?>&nbsp;
					<select name="file" size="1">
						<?php print_list_files($file_path, $file_path); ?>
					</select><br />
					<small><?php printf(__('Please upload the file to \'%s\' directory first.', 'wp-downloadmanager'), $file_path); ?></small>
					<br /><br />
					<input type="radio" name="file_upload" value="1" />&nbsp;&nbsp;<?php _e('Upload File:', 'wp-downloadmanager'); ?>&nbsp;
					<input type="file" name="file" size="25" />&nbsp;&nbsp;<?php _e('to', 'wp-downloadmanager'); ?>&nbsp;&nbsp;
					<select name="file_upload_to" size="1">
						<?php print_list_folders($file_path, $file_path); ?>
					</select><br />
					<small><?php printf(__('Maximum file size is %s.', 'wp-downloadmanager'), format_size(get_max_upload_size())); ?></small>
				</td>
			</tr>
			<tr>
				<td><strong><?php _e('File Name:', 'wp-downloadmanager'); ?></strong></td>
				<td><input type="text" size="50" maxlength="200" name="file_name" /></td>
			</tr>
			<tr>
				<td valign="top"><strong><?php _e('File Description:', 'wp-downloadmanager'); ?></strong></td>
				<td><textarea rows="5" cols="50" name="file_des"></textarea></td>
			</tr>
			<tr>
				<td><strong><?php _e('File Category:', 'wp-downloadmanager'); ?></strong></td>
				<td>
					<select name="file_cat" size="1">
						<?php
							for($i=0; $i<sizeof($file_categories); $i++) {
								echo '<option value="'.$i.'">'.$file_categories[$i].'</option>'."\n";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><strong><?php _e('Starting File Hits:', 'wp-downloadmanager') ?></strong></td>
				<td><input type="text" size="6" maxlength="10" name="file_hits" value="0" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Add File', 'wp-downloadmanager'); ?>"  class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-downloadmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
			</tr>
		</table>
	</form>
</div>