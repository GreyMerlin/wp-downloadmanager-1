<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-Downloads 1.00								|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
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
$base_name = plugin_basename('downloadmanager/download-manager.php');
$base_page = 'admin.php?page='.$base_name;


### If Form Is Submitted
if($_POST['Submit']) {
	$download_path = addslashes(trim($_POST['download_path']));	
	$download_categories_post = explode("\n", trim($_POST['download_categories']));
	$download_sort_by = strip_tags(trim($_POST['download_sort_by']));
	$download_sort_order = strip_tags(trim($_POST['download_sort_order']));
	$download_sort_perpage = intval($_POST['download_sort_perpage']);
	$download_sort = array('by' => $download_sort_by, 'order' => $download_sort_order, 'perpage' => $download_sort_perpage);
	if(!empty($download_categories_post)) {
		$download_categories = array();
		foreach($download_categories_post as $download_category) {
				$download_categories[] = trim($download_category);
		}
	}
	$download_template_category_header = trim($_POST['download_template_category_header']);
	$download_template_category_footer = trim($_POST['download_template_category_footer']);
	$download_template_listing = trim($_POST['download_template_listing']);
	$download_template_embedded = trim($_POST['download_template_embedded']);
	$update_download_queries = array();
	$update_download_text = array();
	$update_download_queries[] = update_option('download_path', $download_path);
	$update_download_queries[] = update_option('download_categories', $download_categories);
	$update_download_queries[] = update_option('download_sort', $download_sort);
	$update_download_queries[] = update_option('download_template_category_header', $download_template_category_header);
	$update_download_queries[] = update_option('download_template_category_footer', $download_template_category_footer);
	$update_download_queries[] = update_option('download_template_listing', $download_template_listing);
	$update_download_queries[] = update_option('download_template_embedded', $download_template_embedded);
	$update_download_text[] = __('Download Path', 'wp-downloadmanager');
	$update_download_text[] = __('Download Categories', 'wp-downloadmanager');
	$update_download_text[] = __('Download Sorting', 'wp-downloadmanager');
	$update_download_text[] = __('Download Category Header Template ', 'wp-downloadmanager');
	$update_download_text[] = __('Download Category Footer Template ', 'wp-downloadmanager');
	$update_download_text[] = __('Download Listing Template', 'wp-downloadmanager');
	$update_download_text[] = __('Download Embedded Template', 'wp-downloadmanager');
	$i=0;
	$text = '';
	foreach($update_download_queries as $update_download_query) {
		if($update_download_query) {
			$text .= '<font color="green">'.$update_download_text[$i].' '.__('Updated', 'wp-downloadmanager').'</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Download Option Updated', 'wp-downloadmanager').'</font>';
	}
}


### Get File Categories
$download_categories = get_settings('download_categories');
$download_categories_display = '';
if(!empty($download_categories)) {
	foreach($download_categories as $download_category) {
		$download_categories_display .= $download_category."\n";
	}
}


### Get File Sorting
$download_sort = get_settings('download_sort');
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<script type="text/javascript">
/* <![CDATA[*/
	function download_default_templates(template) {
		var default_template;
		switch(template) {
			case "category_header":
				default_template = "asd";
				break;
			case "category_footer":
				default_template = "asd2";
				break;
			case "listing":
				default_template = "asd4";
				break;
			case "embedded":
				default_template = "asd4";
				break;
		}
		document.getElementById("download_template_" + template).value = default_template;
	}
/* ]]> */
</script>
<div class="wrap"> 
	<h2><?php _e('Download Options', 'wp-downloadmanager'); ?></h2> 
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
		<fieldset class="options">
			<legend><?php _e('Download Options', 'wp-downloadmanager'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Download Path:', 'wp-downloadmanager'); ?></th>
					<td align="left"><input type="text" name="download_path" value="<?php echo stripslashes(get_settings('download_path')); ?>" size="50" /><br /><?php _e('The absolute path to the directory where all the files are stored (without trailing slash).', 'wp-downloadmanager'); ?></td>
				</tr>
				<tr>
					<td valign="top">
						<strong><?php _e('Download Categories:', 'wp-downloadmanager'); ?></strong><br /><?php _e('Start each entry on a new line.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<textarea cols="30" rows="10" name="download_categories"><?php echo $download_categories_display; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Download Listing Options', 'wp-downloadmanager'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Sort Downloads By:', 'wp-downloadmanager'); ?></th>
					<td align="left">
						<select name="download_sort_by" size="1">
							<option value="file_id"<?php selected('file_id',$download_sort['by']); ?>><?php _e('File ID', 'wp-downloadmanager'); ?></option>
							<option value="file"<?php selected('file', $download_sort['by']); ?>><?php _e('File', 'wp-downloadmanager'); ?></option>
							<option value="file_name"<?php selected('file_name', $download_sort['by']); ?>><?php _e('File Name', 'wp-downloadmanager'); ?></option>
							<option value="file_size"<?php selected('file_size', $download_sort['by']); ?>><?php _e('File Size', 'wp-downloadmanager'); ?></option>
							<option value="file_date"<?php selected('file_date', $download_sort['by']); ?>><?php _e('File Date', 'wp-downloadmanager'); ?></option>
							<option value="file_hits"<?php selected('file_hits', $download_sort['by']); ?>><?php _e('File Hits', 'wp-downloadmanager'); ?></option>
						</select>
					</td>
				</tr>
				 <tr valign="top">
					<th align="left" width="30%"><?php _e('Sort Order Of Downloads:', 'wp-downloadmanager'); ?></th>
					<td align="left">
						<select name="download_sort_order" size="1">
							<option value="asc"<?php selected('asc', $download_sort['order']); ?>><?php _e('Ascending', 'wp-downloadmanager'); ?></option>
							<option value="desc"<?php selected('desc', $download_sort['order']); ?>><?php _e('Descending', 'wp-downloadmanager'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th align="left" width="30%"><?php _e('No. Of Downloads Per Page:', 'wp-downloadmanager'); ?></th>
					<td align="left"><input type="text" name="download_sort_perpage" value="<?php echo intval($download_sort['perpage']); ?>" size="5" /></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Template Variables', 'wp-downloadmanager'); ?></legend>
			<table width="100%"  border="0" cellspacing="0" cellpadding="5">
				<tr>
					<td>
						<strong>%FILE_ID%</strong><br />
						<?php _e('Display the file\'s ID', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE%</strong><br />
						<?php _e('Display the file\'s filename', 'wp-downloadmanager'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong>%FILE_NAME%</strong><br />
						<?php _e('Display the file\'s name', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_DESCRIPTION%</strong><br />
						<?php _e('Display the file\'s description', 'wp-downloadmanager'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong>%FILE_SIZE%</strong><br />
						<?php _e('Display the file\'s size in bytes/KB/MB/GB/TB', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_CATEGORY%</strong><br />
						<?php _e('Display the files\'s category.', 'wp-downloadmanager'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong>%FILE_DATE%</strong><br />
						<?php _e('Displays the file\'s date and time', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_HITS%</strong><br />
						<?php _e('Display the number of times the file has been downloaded', 'wp-downloadmanager'); ?>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Download Templates', 'wp-downloadmanager'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				 <tr valign="top">
					<td width="30%" align="left">
						<strong><?php _e('Download Category Header:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />	
						- %FILE_CATEGORY%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('category_header');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_category_header" name="download_template_category_header"><?php echo htmlspecialchars(stripslashes(get_option('download_template_category_header'))); ?></textarea></td>
				</tr>
				<tr valign="top">
					<td width="30%" align="left">
						<strong><?php _e('Download Category Footer:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />	
						- %FILE_CATEGORY%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('category_footer');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_category_footer" name="download_template_category_footer"><?php echo htmlspecialchars(stripslashes(get_option('download_template_category_footer'))); ?></textarea></td>
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<strong><?php _e('Download Listing:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
						- %FILE_ID%<br />
						- %FILE%<br />
						- %FILE_NAME%<br />
						- %FILE_DESCRIPTION%<br />
						- %FILE_SIZE%<br />
						- %FILE_CATEGORY%<br />
						- %FILE_DATE%<br />
						- %FILE_HITS%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('listing');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_listing" name="download_template_listing"><?php echo htmlspecialchars(stripslashes(get_option('download_template_listing'))); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<strong><?php _e('Download Embedded File', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
						- %FILE_ID%<br />
						- %FILE%<br />
						- %FILE_NAME%<br />
						- %FILE_DESCRIPTION%<br />
						- %FILE_SIZE%<br />
						- %FILE_CATEGORY%<br />
						- %FILE_DATE%<br />
						- %FILE_HITS%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('embedded');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_embedded" name="download_template_embedded"><?php echo htmlspecialchars(stripslashes(get_option('download_template_embedded'))); ?></textarea></td> 
				</tr>
			</table>
		</fieldset>
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php _e('Update Options', 'wp-downloadmanager'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-downloadmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /> 
		</div>
	</form> 
</div> 