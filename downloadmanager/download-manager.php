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
|	- Manage Your Downloads														|
|	- wp-content/plugins/downloadmanager/download-manager.php	|
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
$mode = trim($_GET['mode']);
$file_id = intval($_GET['id']);
$file_path = get_option('download_path');
$file_categories = get_option('download_categories');


### Form Processing 
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		// Edit File
		case __('Edit File', 'wp-downloadmanager'):
			$file_id  = intval($_POST['file_id']);
			$file = addslashes(trim($_POST['file']));
			if(is_file($file_path.$file)) {
				$file_name= addslashes(trim($_POST['file_name']));
				if(empty($file_name)) {
					$file_name = $file;
				}
				$file_des = addslashes(trim($_POST['file_des']));
				$file_category = intval($_POST['file_cat']); 
				$file_size = filesize($file_path.$file);
				$file_hits = intval($_POST['file_hits']);
				$edit_filetimestamp = intval($_POST['edit_filetimestamp']);
				$reset_filehits = intval($_POST['reset_filehits']);
				$hits_sql = '';
				if($reset_filehits == 1) {
					$hits_sql = ', file_hits = 0';
				} else {
					$hits_sql = ", file_hits = $file_hits";
				}
				$timestamp_sql = '';
				if($edit_filetimestamp == 1) {
					$file_timestamp_day = intval($_POST['file_timestamp_day']);
					$file_timestamp_month = intval($_POST['file_timestamp_month']);
					$file_timestamp_year = intval($_POST['file_timestamp_year']);
					$file_timestamp_hour = intval($_POST['file_timestamp_hour']);
					$file_timestamp_minute = intval($_POST['file_timestamp_minute']);
					$file_timestamp_second = intval($_POST['file_timestamp_second']);
					$timestamp_sql = ", file_date = '".gmmktime($file_timestamp_hour, $file_timestamp_minute, $file_timestamp_second, $file_timestamp_month, $file_timestamp_day, $file_timestamp_year)."'";
				}
				$editfile = $wpdb->query("UPDATE $wpdb->downloads SET file = '$file', file_name = '$file_name', file_des = '$file_des', file_size = '$file_size', file_category = $file_category $timestamp_sql $hits_sql WHERE file_id = $file_id;");
				if(!$editfile) {
					$text = '<font color="red">'.sprintf(__('Error In Editing File \'%s (%s)\'', 'wp-downloadmanager'), $file_name, $file).'</font>';
				} else {
					$text = '<font color="green">'.sprintf(__('File \'%s (%s)\' Edited Successfully', 'wp-downloadmanager'), $file_name, $file).'</font>';
				}
			} else {
					$text = '<font color="red">'.sprintf(__('Invaild File \'%s\'', 'wp-downloadmanager'), $file).'</font>';
			}
			break;
		// Delete File
		case __('Delete File', 'wp-downloadmanager');
			$file_id  = intval($_POST['file_id']);
			$file = trim($_POST['file']);
			$file_name = trim($_POST['file_name']);
			$unlinkfile = intval($_POST['unlinkfile']);
			if($unlinkfile == 1) {
				if(!unlink($file_path.$file)) {
					$text = '<font color="red">'.sprintf(__('Error In Deleting File \'%s (%s)\' From Server', 'wp-downloadmanager'), $file_name, $file).'</font>';
				} else {
					$text = '<font color="green">'.sprintf(__('File \'%s (%s)\' Deleted From Server Successfully', 'wp-downloadmanager'), $file_name, $file).'</font>';
				}
			}
			$deletefile = $wpdb->query("DELETE FROM $wpdb->downloads WHERE file_id = $file_id");
			if(!$deletefile) {
				$text .= '<br /><font color="red">'.sprintf(__('Error In Deleting File \'%s (%s)\'', 'wp-downloadmanager'), $file_name, $file).'</font>';
			} else {
				$text .= '<br /><font color="green">'.sprintf(__('File \'%s (%s)\' Deleted Successfully', 'wp-downloadmanager'), $file_name, $file).'</font>';
			}
			break;
	}
}


### Determines Which Mode It Is
switch($mode) {
	// Edit A File
	case 'edit':
		$file = $wpdb->get_row("SELECT * FROM $wpdb->downloads WHERE file_id = $file_id");
?>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
		<!-- Edit A File -->
		<div class="wrap">
			<h2><?php _e('Edit A File', 'wp-downloadmanager'); ?></h2>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
				<input type="hidden" name="file_id" value="<?php echo intval($file->file_id); ?>" />
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<td valign="top"><strong><?php _e('File:', 'wp-downloadmanager') ?></strong></td>
						<td>
							<select name="file" size="1">
								<?php print_list_files($file_path, $file_path, stripslashes($file->file)); ?>
							</select><br />
							<small><?php printf(__('Please upload the file to \'%s\' directory first.', 'wp-downloadmanager'), $file_path); ?></small>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e('File Name:', 'wp-downloadmanager'); ?></strong></td>
						<td><input type="text" size="50" maxlength="200" name="file_name" value="<?php echo htmlspecialchars(stripslashes($file->file_name)); ?>" /></td>
					</tr>
					<tr>
						<td valign="top"><strong><?php _e('File Description:', 'wp-downloadmanager'); ?></strong></td>
						<td><textarea rows="5" cols="50" name="file_des"><?php echo htmlspecialchars(stripslashes($file->file_des)); ?></textarea></td>
					</tr>
					<tr>
						<td><strong><?php _e('File Category:', 'wp-downloadmanager'); ?></strong></td>
						<td>
							<select name="file_cat" size="1">
								<?php
									for($i = 0; $i<sizeof($file_categories); $i++) {
										if($i == intval($file->file_category)) {
											echo '<option value="'.$i.'" selected="selected">'.$file_categories[$i].'</option>'."\n";
										} else {
											echo '<option value="'.$i.'">'.$file_categories[$i].'</option>'."\n";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e('File Size:', 'wp-downloadmanager') ?></strong></td>
						<td><?php echo format_size($file->file_size); ?></td>
					</tr>
					<tr>
						<td valign="top"><strong><?php _e('File Hits:', 'wp-downloadmanager') ?></strong></td>
						<td><?php echo number_format($file->file_hits); ?> <?php _e('hits', 'wp-downloadmanager') ?><br /><input type="text" size="6" maxlength="10" name="file_hits" value="<?php echo $file->file_hits; ?>" /><br /><input type="checkbox" name="reset_filehits" value="1" />&nbsp;<?php _e('Reset File Hits', 'wp-downloadmanager') ?></td>
					</tr>
					<tr>
						<td valign="top"><strong><?php _e('File Date:', 'wp-downloadmanager') ?></strong></td>
						<td><?php _e('Existing Timestamp:', 'wp-downloadmanager') ?> <?php echo gmdate(get_option('date_format').' @ '.get_option('time_format'), $file->file_date); ?><br /><?php file_timestamp($file->file_date); ?><br /><input type="checkbox" name="edit_filetimestamp" value="1" />&nbsp;<?php _e('Edit Timestamp', 'wp-downloadmanager') ?></td>
					</tr>					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Edit File', 'wp-downloadmanager'); ?>"  class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-downloadmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Delete A File
	case 'delete':
		$file = $wpdb->get_row("SELECT * FROM $wpdb->downloads WHERE file_id = $file_id");
?>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
		<!-- Delete A File -->
		<div class="wrap">
			<h2><?php _e('Delete A File', 'wp-downloadmanager'); ?></h2>
			<form action="<?php echo $base_page; ?>" method="post">
				<input type="hidden" name="file_id" value="<?php echo intval($file->file_id); ?>" />
				<input type="hidden" name="file" value="<?php echo stripslashes($file->file); ?>" />
				<input type="hidden" name="file_name" value="<?php echo htmlspecialchars(stripslashes($file->file_name)); ?>" />
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<td valign="top"><strong><?php _e('File:', 'wp-downloadmanager') ?></strong></td>
						<td><?php echo stripslashes($file->file); ?></td>
					</tr>
					<tr>
						<td><strong><?php _e('File Name:', 'wp-downloadmanager'); ?></strong></td>
						<td><?php echo stripslashes($file->file_name); ?></td>
					</tr>
					<tr>
						<td valign="top"><strong><?php _e('File Description:', 'wp-downloadmanager'); ?></strong></td>
						<td><?php echo stripslashes($file->file_des); ?></td>
					</tr>
					<tr>
						<td><strong><?php _e('File Category:', 'wp-downloadmanager'); ?></strong></td>
						<td><?php echo $file_categories[intval($file->file_category)]; ?></td>
					</tr>
					<tr>
						<td><strong><?php _e('File Size:', 'wp-downloadmanager'); ?></strong></td>
						<td><?php echo format_size($file->file_size); ?></td>
					</tr>
					<tr>
						<td><strong><?php _e('File Hits', 'wp-downloadmanager'); ?></strong></td>
						<td><?php echo number_format($file->file_hits); ?> <?php _e('hits', 'wp-downloadmanager'); ?></td>
					</tr>
					<tr>
						<td><strong><?php _e('File Date', 'wp-downloadmanager'); ?></strong></td>
						<td><?php echo gmdate(get_option('date_format').' @ '.get_option('time_format'), $file->file_date); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="checkbox" name="unlinkfile" value="1" />&nbsp;<?php _e('Delete File From Server?', 'wp-downloadmanager'); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Delete File', 'wp-downloadmanager'); ?>" class="button"  onclick="return confirm('You Are About To The Delete This File \'<?php echo stripslashes(strip_tags($file->file_name)); ?> (<?php echo stripslashes($file->file); ?>)\'.\nThis Action Is Not Reversible.\n\n Choose \'Cancel\' to stop, \'OK\' to delete.')"/>&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-downloadmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
					</tr>
				</table>
			</form>
		</div>
<?php
		break;
	// Main Page
	default:
		$files = $wpdb->get_results("SELECT * FROM $wpdb->downloads ORDER BY file_name ASC");
?>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
		<!-- Manage Downloads -->
		<div class="wrap">
			<h2><?php _e('Manage Downloads'); ?></h2>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr class="thead">
				<th width="3%"><?php _e('ID', 'wp-downloadmanager'); ?></th>
				<th width="42%"><?php _e('File', 'wp-downloadmanager'); ?></th>
				<th width="10%"><?php _e('Size', 'wp-downloadmanager'); ?></th>
				<th width="10%"><?php _e('Hits', 'wp-downloadmanager'); ?></th>
				<th width="10%"><?php _e('Category', 'wp-downloadmanager'); ?></th>
				<th width="15%"><?php _e('Time/Date Added', 'wp-downloadmanager'); ?></th>
				<th width="10%" colspan="2"><?php _e('Action', 'wp-downloadmanager'); ?></th>
			</tr>
			<?php
				if($files) {
					$total_filesize = 0;
					$total_filehits = 0;
					$total_bandwidth = 0;
					$i = 0;
					foreach($files as $file) {
						$file_id = intval($file->file_id);
						$file_name = stripslashes($file->file);
						$file_nicename = stripslashes($file->file_name);
						$file_des = stripslashes($file->file_des);
						$file_size = $file->file_size;
						$file_cat = intval($file->file_category);
						$file_date = gmdate(get_option('date_format'), $file->file_date);
						$file_time = gmdate(get_option('time_format'), $file->file_date);
						$file_hits = intval($file->file_hits);
						$file_name_actual = basename($file_name);
						$total_filesize += $file_size;
						$total_filehits += $file_hits;
						$total_bandwidth += $file_size*$file_hits;
						if($i%2 == 0) {
							$style = 'style="background-color: none;"';
						}  else {
							$style = 'style="background-color: #eee;"';
						}
						echo "<tr $style>\n";
						echo "<td valign=\"top\">$file_id</td>\n";
						echo "<td>$file_nicename<br /><strong>&raquo;</strong> <i>$file_name</i></td>\n";
						echo '<td style="text-align: center;">'.format_size($file_size).'</td>'."\n";
						echo '<td style="text-align: center;">'.$file_hits.'</td>'."\n";
						echo '<td style="text-align: center;">'.$file_categories[$file_cat].'</td>'."\n";
						echo "<td>$file_time<br />$file_date</td>\n";
						echo "<td style=\"text-align: center;\"><a href=\"$base_page&amp;mode=edit&amp;id=$file_id\" class=\"edit\">".__('Edit', 'wp-downloadmanager')."</a></td>\n";
						echo "<td style=\"text-align: center;\"><a href=\"$base_page&amp;mode=delete&amp;id=$file_id\" class=\"delete\">".__('Delete', 'wp-downloadmanager')."</a></td>\n";
						echo '</tr>';
						$i++;		
					}
				} else {
					echo '<tr><td colspan="9" align="center"><strong>'.__('No Files Found', 'wp-downloadmanager').'</strong></td></tr>';
				}
			?>
			</table>
		</div>

		<!-- Download Stats -->
		<div class="wrap">
		<h2><?php _e('Download Stats'); ?></h2>
			<table border="0" cellspacing="3" cellpadding="3">
				<tr>
					<th align="left"><?php _e('Total Files:', 'wp-downloadmanager'); ?></th>
					<td align="left"><?php echo $i-1; ?></td>
				</tr>
				<tr>
					<th align="left"><?php _e('Total Size:', 'wp-downloadmanager'); ?></th>
					<td align="left"><?php echo format_size($total_filesize); ?></td>
				</tr>
				<tr>
					<th align="left"><?php _e('Total Hits:', 'wp-downloadmanager'); ?></th>
					<td align="left"><?php echo number_format($total_filehits); ?></td>
				</tr>
					<tr>
					<th align="left"><?php _e('Total Bandwidth:', 'wp-downloadmanager'); ?></th>
					<td align="left"><?php echo format_size($total_bandwidth); ?></td>
				</tr>
			</table>
		</div>
<?php
} // End switch($mode)
?>