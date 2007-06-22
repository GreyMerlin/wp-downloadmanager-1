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
|	- Downloads Templates															|
|	- wp-content/plugins/downloadmanager/download-templates.php	|
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
	$download_template_header = trim($_POST['download_template_header']);
	$download_template_footer = trim($_POST['download_template_footer']);
	$download_template_category_header = trim($_POST['download_template_category_header']);
	$download_template_category_footer = trim($_POST['download_template_category_footer']);
	$download_template_listing = trim($_POST['download_template_listing']);
	$download_template_embedded = trim($_POST['download_template_embedded']);
	$download_template_most = trim($_POST['download_template_most']);
	$update_download_queries = array();
	$update_download_text = array();
	$update_download_queries[] = update_option('download_template_header', $download_template_header);
	$update_download_queries[] = update_option('download_template_footer', $download_template_footer);
	$update_download_queries[] = update_option('download_template_category_header', $download_template_category_header);
	$update_download_queries[] = update_option('download_template_category_footer', $download_template_category_footer);
	$update_download_queries[] = update_option('download_template_listing', $download_template_listing);
	$update_download_queries[] = update_option('download_template_embedded', $download_template_embedded);
	$update_download_queries[] = update_option('download_template_most', $download_template_most);
	$update_download_text[] = __('Download Page Header', 'wp-downloadmanager');
	$update_download_text[] = __('Download Page Footer', 'wp-downloadmanager');
	$update_download_text[] = __('Download Category Header Template', 'wp-downloadmanager');
	$update_download_text[] = __('Download Category Footer Template', 'wp-downloadmanager');
	$update_download_text[] = __('Download Listing Template', 'wp-downloadmanager');
	$update_download_text[] = __('Download Embedded Template', 'wp-downloadmanager');
	$update_download_text[] = __('Most DownloadedTemplate', 'wp-downloadmanager');
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
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<script type="text/javascript">
/* <![CDATA[*/
	function download_default_templates(template) {
		var default_template;
		switch(template) {
			case "header":
				default_template = "<p><?php _e('There are <strong>%TOTAL_FILES_COUNT% files</strong>, weighing <strong>%TOTAL_SIZE%</strong> with <strong>%TOTAL_HITS% hits</strong> in <strong>%FILE_CATEGORY_NAME%</strong>.</p><p>Displaying <strong>%RECORD_START%</strong> to <strong>%RECORD_END%</strong> of <strong>%TOTAL_FILES_COUNT%</strong> files.', 'wp-downloadmanager'); ?></p>";
				break;
			case "footer":
				default_template = "";
				break;
			case "category_header":
				default_template = "<h2><a href=\"%CATEGORY_URL%\" title=\"<?php _e('View all downloads in %FILE_CATEGORY_NAME%', 'wp-downloadmanager'); ?>\">%FILE_CATEGORY_NAME%</a></h2>";
				break;
			case "category_footer":
				default_template = "";
				break;
			case "listing":
				default_template = "<p><img src=\"<?php echo get_option('siteurl'); ?>/wp-content/plugins/downloadmanager/images/drive_go.gif\" alt=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" style=\"vertical-align: middle;\" />&nbsp;&nbsp;<strong><a href=\"%FILE_DOWNLOAD_URL%\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\">%FILE_NAME%</a></strong><br /><strong>&raquo; %FILE_SIZE% - %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?> - %FILE_DATE%</strong><br />%FILE_DESCRIPTION%</p>";
				break;
			case "embedded":
				default_template = "<p><img src=\"<?php echo get_option('siteurl'); ?>/wp-content/plugins/downloadmanager/images/drive_go.gif\" alt=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" style=\"vertical-align: middle;\" />&nbsp;&nbsp;<strong><a href=\"%FILE_DOWNLOAD_URL%\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\">%FILE_NAME%</a></strong> (%FILE_SIZE%, %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?>)</p>";
				break;
			case 'most':
				default_template = "<li><strong><a href=\"%FILE_DOWNLOAD_URL%\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\">%FILE_NAME%</a></strong> (%FILE_SIZE%, %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?>)</li>";
				break;
		}
		document.getElementById("download_template_" + template).value = default_template;
	}
/* ]]> */
</script>
<div class="wrap"> 
	<h2><?php _e('Download Templates', 'wp-downloadmanager'); ?></h2> 
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
	<fieldset class="options">
			<legend><?php _e('Template Variables', 'wp-downloadmanager'); ?></legend>
			<table width="100%"  border="0" cellspacing="0" cellpadding="5">
				<tr>
					<td>
						<strong>%FILE_ID%</strong><br />
						<?php _e('Display the file\'s ID.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE%</strong><br />
						<?php _e('Display the file\'s filename.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%CATEGORY_FILES_COUNT%</strong><br />
						<?php _e('Display the total number of files in the category.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%TOTAL_FILES_COUNT%</strong><br />
						<?php _e('Display the total number of files.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%RECORD_START%</strong><br />
						<?php _e('Display the start number of the record.', 'wp-downloadmanager'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong>%FILE_NAME%</strong><br />
						<?php _e('Display the file\'s name.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_DESCRIPTION%</strong><br />
						<?php _e('Display the file\'s description.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%CATEGORY_HITS%</strong><br />
						<?php _e('Display the total number of file hits in the category.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%TOTAL_HITS%</strong><br />
						<?php _e('Displays the total file hits.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%RECORD_END%</strong><br />
						<?php _e('Display the end number of the record.', 'wp-downloadmanager'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong>%FILE_SIZE%</strong><br />
						<?php _e('Display the file\'s size in bytes/KB/MB/GB/TB.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_CATEGORY_NAME%</strong><br />
						<?php _e('Display the files\'s category name.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%CATEGORY_SIZE%</strong><br />
						<?php _e('Display the total size of all the files in the category.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%TOTAL_SIZE%</strong><br />
						<?php _e('Display the total file size.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_HITS%</strong><br />
						<?php _e('Display the number of times the file has been downloaded.', 'wp-downloadmanager'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong>%FILE_DATE%</strong><br />
						<?php _e('Displays the file\'s date.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_TIME%</strong><br />
						<?php _e('Displays the file\'s time.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%CATEGORY_URL%</strong><br />
						<?php _e('Displays the url to the category.', 'wp-downloadmanager'); ?>
					</td>
					<td>
						<strong>%FILE_DOWNLOAD_URL%</strong><br />
						<?php _e('Displays the file\'s download url.', 'wp-downloadmanager'); ?>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Download Templates', 'wp-downloadmanager'); ?></legend>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
				<tr valign="top">
					<td width="30%" align="left">
						<strong><?php _e('Download Page Header:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />	
						- %TOTAL_FILES_COUNT%<br />
						- %TOTAL_HITS%<br />
						- %TOTAL_SIZE%<br />
						- %RECORD_START%<br />
						- %RECORD_END%<br />
						- %FILE_CATEGORY_NAME%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('header');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_header" name="download_template_header"><?php echo htmlspecialchars(stripslashes(get_option('download_template_header'))); ?></textarea></td>
				 </tr>
				<tr valign="top">
					<td width="30%" align="left">
						<strong><?php _e('Download Page Footer:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
						- %TOTAL_FILES_COUNT%<br />
						- %TOTAL_HITS%<br />
						- %TOTAL_SIZE%<br />
						- %FILE_CATEGORY_NAME%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('footer');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_footer" name="download_template_footer"><?php echo htmlspecialchars(stripslashes(get_option('download_template_footer'))); ?></textarea></td>
				 </tr>
				 <tr valign="top">
					<td width="30%" align="left">
						<strong><?php _e('Download Category Header:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />	
						- %FILE_CATEGORY_NAME%<br />
						- %CATEGORY_URL%<br />
						- %CATEGORY_FILES_COUNT%<br />
						- %CATEGORY_HITS%<br />
						- %CATEGORY_SIZE%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('category_header');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_category_header" name="download_template_category_header"><?php echo htmlspecialchars(stripslashes(get_option('download_template_category_header'))); ?></textarea></td>
				</tr>
				<tr valign="top">
					<td width="30%" align="left">
						<strong><?php _e('Download Category Footer:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />	
						- %FILE_CATEGORY_NAME%<br />
						- %CATEGORY_URL%<br />
						- %CATEGORY_FILES_COUNT%<br />
						- %CATEGORY_HITS%<br />
						- %CATEGORY_SIZE%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('category_footer');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_category_footer" name="download_template_category_footer"><?php echo htmlspecialchars(stripslashes(get_option('download_template_category_footer'))); ?></textarea></td>
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<strong><?php _e('Download Listing:', 'wp-downloadmanager'); ?></strong><br />
						<?php _e('Displayed when listing files in the downloads page.', 'wp-downloadmanager'); ?><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
						- %FILE_ID%<br />
						- %FILE%<br />
						- %FILE_NAME%<br />
						- %FILE_DESCRIPTION%<br />
						- %FILE_SIZE%<br />
						- %FILE_CATEGORY_NAME%<br />
						- %FILE_DATE%<br />
						- %FILE_TIME%<br />
						- %FILE_HITS%<br />
						- %FILE_DOWNLOAD_URL%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('listing');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_listing" name="download_template_listing"><?php echo htmlspecialchars(stripslashes(get_option('download_template_listing'))); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<strong><?php _e('Download Embedded File', 'wp-downloadmanager'); ?></strong><br />
						<?php _e('Displayed when you embedded a file within a post or a page.', 'wp-downloadmanager'); ?><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
						- %FILE_ID%<br />
						- %FILE%<br />
						- %FILE_NAME%<br />
						- %FILE_DESCRIPTION%<br />
						- %FILE_SIZE%<br />
						- %FILE_CATEGORY_NAME%<br />
						- %FILE_DATE%<br />
						- %FILE_DATE%<br />
						- %FILE_HITS%<br />
						- %FILE_DOWNLOAD_URL%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('embedded');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_embedded" name="download_template_embedded"><?php echo htmlspecialchars(stripslashes(get_option('download_template_embedded'))); ?></textarea></td> 
				</tr>
				<tr valign="top"> 
					<td width="30%" align="left">
						<strong><?php _e('Most Downloaded', 'wp-downloadmanager'); ?></strong><br />
						<?php _e('Displayed when listing most downloaded files.', 'wp-downloadmanager'); ?><br />
						<strong><?php _e('Newest Downloads', 'wp-downloadmanager'); ?></strong><br />
						<?php _e('Displayed when listing newest downloads.', 'wp-downloadmanager'); ?><br />
						<strong><?php _e('Downloads By Category', 'wp-downloadmanager'); ?></strong><br />
						<?php _e('Displayed when listing downloads by category.', 'wp-downloadmanager'); ?><br /><br />
						<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
						- %FILE_ID%<br />
						- %FILE%<br />
						- %FILE_NAME%<br />
						- %FILE_DESCRIPTION%<br />
						- %FILE_SIZE%<br />
						- %FILE_CATEGORY_NAME%<br />
						- %FILE_DATE%<br />
						- %FILE_DATE%<br />
						- %FILE_HITS%<br />
						- %FILE_DOWNLOAD_URL%<br /><br />
						<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="javascript: download_default_templates('most');" class="button" />
					</td>
					<td align="left"><textarea cols="80" rows="12" id="download_template_most" name="download_template_most"><?php echo htmlspecialchars(stripslashes(get_option('download_template_most'))); ?></textarea></td> 
				</tr>
			</table>
		</fieldset>
		<div align="center">
			<input type="submit" name="Submit" class="button" value="<?php _e('Update Templates', 'wp-downloadmanager'); ?>" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-downloadmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /> 
		</div>
	</form> 
</div> 