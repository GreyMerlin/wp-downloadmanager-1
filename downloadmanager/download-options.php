<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.0 Plugin: WP-Downloads 1.00								|
|	Copyright (c) 2005 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Manage Your Downloads														|
|	- wp-content/plugins/downloads/downloads-options.php				|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Downloads
if(!current_user_can('manage_downloads')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('downloads/downloads-options.php');
$base_page = 'admin.php?page='.$base_name;


### If Form Is Submitted
if($_POST['Submit']) {
	$file_path = addslashes(trim($_POST['file_path']));	
	$file_categories_post = explode("\n", trim($_POST['file_categories']));
	if(!empty($file_categories_post)) {
		$file_categories = array();
		foreach($file_categories_post as $file_category) {
				$file_categories[] = trim($file_category);
		}
	}
	$update_file_queries = array();
	$update_file_text = array();
	$update_file_queries[] = update_option('download_path', $file_path);
	$update_file_queries[] = update_option('download_categories', $file_categories);
	$update_file_text[] = __('File Path');
	$update_file_text[] = __('File Categories');
	$i=0;
	$text = '';
	foreach($update_file_queries as $update_file_query) {
		if($update_file_query) {
			$text .= '<font color="green">'.$update_file_text[$i].' '.__('Updated').'</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Download Option Updated').'</font>';
	}
}


### Get File Categories
$file_categories = get_settings('download_categories');
$file_categories_display = '';
if(!empty($file_categories)) {
	foreach($file_categories as $file_category) {
		$file_categories_display .= $file_category."\n";
	}
}
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<div class="wrap"> 
	<h2><?php _e('Download Options'); ?></h2> 
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
		<fieldset class="options">
			<legend><?php _e('Download Options'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('File Path:'); ?></th>
					<td align="left"><input type="text" name="file_path" value="<?php echo stripslashes(get_settings('download_path')); ?>" size="50" /><br />The absolute path to the directory where all the files are stored.</td>
				</tr>
				<tr>
					<td valign="top">
						<strong><?php _e('File Categories'); ?>:</strong><br />
						Start each entry on a new line.
					</td>
					<td>
						<textarea cols="30" rows="10" name="file_categories"><?php echo $file_categories_display; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php _e('Update Options'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript:history.go(-1)" /> 
		</div>
	</form> 
</div> 