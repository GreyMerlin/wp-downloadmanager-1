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
|	- wp-content/plugins/downloads/downloads-manager.php			|
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
$file_path = get_settings('download_path');
$file_categories = get_settings('download_categories');


### Form Processing 
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		// Add File
		case __('Add File', 'wp-downloadmanager'):
			$file = addslashes(trim($_POST['file']));
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
		// Edit Timestamp Options
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
?>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
		<!-- Edit A File -->
		<div class="wrap">
			<h2><?php _e('Edit A File'); ?></h2>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
				<input type="hidden" name="file_id" value="<?php echo intval($file->file_id); ?>" />
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th align="left" valign="top"><?php _e('File') ?></th>
						<td>
							<select name="file" size="1">
								<?php list_files($file_path, $file_path, stripslashes($file->file)); ?>
							</select><br />
							<small>Please upload the file to '<?php echo $file_path; ?>' directory first.</small>
						</td>
					</tr>
					<tr>
						<th align="left"><?php _e('File Name') ?></th>
						<td><input type="text" size="50" maxlength="200" name="file_name" value="<?php echo htmlspecialchars(stripslashes($file->file_name)); ?>" /></td>
					</tr>
					<tr>
						<th align="left"><?php _e('File Description') ?></th>
						<td><input type="text" size="50" maxlength="200" name="file_des" value="<?php echo htmlspecialchars(stripslashes($file->file_des)); ?>" /></td>
					</tr>
					<tr>
						<th align="left"><?php _e('File Category') ?></th>
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
						<th align="left"><?php _e('File Size') ?></th>
						<td><?php echo format_size($file->file_size); ?></td>
					</tr>
					<tr>
						<th align="left" valign="top"><?php _e('File Hits') ?></th>
						<td><input type="text" size="6" maxlength="10" name="file_hits" value="<?php echo $file->file_hits; ?>" /><br /><?php echo number_format($file->file_hits); ?> hits<br /><br /><input type="checkbox" name="reset_filehits" value="1" />&nbsp;Reset File Hits</td>
					</tr>
					<tr>
						<th align="left" valign="top"><?php _e('File Date') ?></th>
						<td>Existing Timestamp: <?php echo gmdate('jS F Y @ H:i:s', $file->file_date); ?><br /><?php file_timestamp($file->file_date); ?><br /><input type="checkbox" name="edit_filetimestamp" value="1" />&nbsp;Edit Timestamp</td>
					</tr>					
					<tr>
						<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Edit File'); ?>"  class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
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
			<h2><?php _e('Delete A File'); ?></h2>
			<form action="<?php echo $base_page; ?>" method="post">
				<input type="hidden" name="file_id" value="<?php echo intval($file->file_id); ?>" />
				<input type="hidden" name="file" value="<?php echo stripslashes($file->file); ?>" />
				<input type="hidden" name="file_name" value="<?php echo htmlspecialchars(stripslashes($file->file_name)); ?>" />
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th align="left" valign="top"><?php _e('File', 'wp-downloadmanager'); ?></th>
						<td><?php echo stripslashes($file->file); ?></td>
					</tr>
					<tr>
						<th align="left"><?php _e('File Name', 'wp-downloadmanager'); ?></th>
						<td><?php echo stripslashes($file->file_name); ?></td>
					</tr>
					<tr>
						<th align="left"><?php _e('File Description', 'wp-downloadmanager'); ?></th>
						<td><?php echo stripslashes($file->file_des); ?></td>
					</tr>
					<tr>
						<th align="left"><?php _e('File Category', 'wp-downloadmanager'); ?></th>
						<td><?php echo $file_categories[intval($file->file_category)]; ?></td>
					</tr>
					<tr>
						<th align="left"><?php _e('File Size', 'wp-downloadmanager'); ?></th>
						<td><?php echo format_size($file->file_size); ?></td>
					</tr>
					<tr>
						<th align="left" valign="top"><?php _e('File Hits', 'wp-downloadmanager'); ?></th>
						<td><?php echo number_format($file->file_hits); ?> hits</td>
					</tr>
					<tr>
						<th align="left" valign="top"><?php _e('File Date', 'wp-downloadmanager'); ?></th>
						<td><?php echo gmdate('jS F Y @ H:i:s', $file->file_date); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="checkbox" name="unlinkfile" value="1" />&nbsp;Delete File From Server?</td>
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
		<script type="text/javascript">
		/* <![CDATA[*/
			// Function: Toggle Show/Hide
			function toggle(name) {
				if (document.getElementById(name).style.display == "block") {
					document.getElementById(name).style.display = "none";
				} else {
					document.getElementById(name).style.display = "block";
				}
			}
		/* ]]> */
		</script>
		<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.stripslashes($text).'</p></div>'; } ?>
		<!-- Add A File -->
		<div class="wrap">
			<!-- <p><strong><a href="#Add_File" onclick="toggle('add_file_form'); return false;">Click Here To Add A File</a></strong></p> -->
			<div id="add_file_form">
				<h2><?php _e('Add A File', 'wp-downloadmanager'); ?></h2>
				<form action="<?php echo $base_page; ?>" method="post">
					<table width="100%"  border="0" cellspacing="3" cellpadding="3">
						<tr>
							<th align="left" valign="top"><?php _e('File', 'wp-downloadmanager') ?></th>
							<td>
								<select name="file" size="1">
									<?php list_files($file_path, $file_path); ?>
								</select><br />
								<small>Please upload the file to '<?php echo $file_path; ?>' directory first.</small>
							</td>
						</tr>
						<tr>
							<th align="left"><?php _e('File Name', 'wp-downloadmanager'); ?></th>
							<td><input type="text" size="50" maxlength="200" name="file_name" /></td>
						</tr>
						<tr>
							<th align="left"><?php _e('File Description', 'wp-downloadmanager'); ?></th>
							<td><input type="text" size="50" maxlength="200" name="file_des" /></td>
						</tr>
						<tr>
							<th align="left"><?php _e('File Category', 'wp-downloadmanager'); ?></th>
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
							<th align="left"><?php _e('Starting File Hits', 'wp-downloadmanager') ?></th>
							<td><input type="text" size="6" maxlength="10" name="file_hits" value="0" /></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Add File', 'wp-downloadmanager'); ?>"  class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-downloadmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<!-- Manage Downloads -->
		<div class="wrap">
			<h2><?php _e('Manage Downloads'); ?></h2>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr>
				<th width="3%" scope="col"><?php _e('ID', 'wp-downloadmanager'); ?></th>
				<th width="50%" scope="col"><?php _e('File', 'wp-downloadmanager'); ?></th>
				<th width="10%" scope="col"><?php _e('Location', 'wp-downloadmanager'); ?></th>
				<th width="10%" scope="col"><?php _e('Category', 'wp-downloadmanager'); ?></th>
				<th width="17%" scope="col"><?php _e('Date Added', 'wp-downloadmanager'); ?></th>
				<th width="10%" scope="col" colspan="2"><?php _e('Action', 'wp-downloadmanager'); ?></th>
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
						$file_date = gmdate("d.m.Y", $file->file_date);
						$file_time = gmdate("H:i", $file->file_date);
						$file_hits = intval($file->file_hits);
						$file_name_actual = explode('/', $file_name);
						$file_name_actual = $file_name_actual[sizeof($file_name_actual)-1];
						$file_location = str_replace($file_name_actual, '', $file_name);
						$total_filesize += $file_size;
						$total_filehits += $file_hits;
						$total_bandwidth += $file_size*$file_hits;
						if($i%2 == 0) {
							$style = 'style=\'background-color: #eee\'';
						}  else {
							$style = 'style=\'background-color: none\'';
						}
						echo "<tr $style>\n";
						echo "<td valign=\"top\">$file_id</td>\n";
						echo "<td><strong>$file_nicename</strong><br /><strong>&raquo;</strong> <i>$file_name_actual - ".format_size($file_size)." - ".number_format($file_hits)." hits</i><br /><strong>&raquo;</strong> $file_des</td>\n";
						echo '<td>'.$file_location.'</td>'."\n";
						echo '<td>'.$file_categories[$file_cat].'</td>'."\n";
						echo "<td>$file_time<br />$file_date</td>\n";
						echo "<td><a href=\"$base_page&amp;mode=edit&amp;id=$file_id\" class=\"edit\">".__('Edit')."</a></td>\n";
						echo "<td><a href=\"$base_page&amp;mode=delete&amp;id=$file_id\" class=\"delete\">".__('Delete')."</a></td>\n";
						echo '</tr>';
						$i++;		
					}
				} else {
					echo '<tr><td colspan="7" align="center"><strong>'.__('No Files Found', 'wp-downloadmanager').'</strong></td></tr>';
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