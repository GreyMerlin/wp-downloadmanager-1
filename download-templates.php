<?php
/*
+----------------------------------------------------------------------+
|																									|
|	WordPress 2.5 Plugin: WP-DownloadManager 1.30								|
|	Copyright (c) 2008 Lester "GaMerZ" Chan											|
|																									|
|	File Written By:																			|
|	- Lester "GaMerZ" Chan																	|
|	- http://lesterchan.net																	|
|																									|
|	File Information:																			|
|	- Downloads Templates																	|
|	- wp-content/plugins/wp-downloadmanager/download-templates.php	|
|																									|
+----------------------------------------------------------------------+
*/


### Check Whether User Can Manage Downloads
if(!current_user_can('manage_downloads')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('wp-downloadmanager/download-manager.php');
$base_page = 'admin.php?page='.$base_name;


### If Form Is Submitted
if($_POST['Submit']) {
	$download_template_listing = array();
	$download_template_embedded = array();
	$download_template_most = array();
	$download_template_header = trim($_POST['download_template_header']);
	$download_template_footer = trim($_POST['download_template_footer']);
	$download_template_pagingheader = trim($_POST['download_template_pagingheader']);
	$download_template_pagingfooter = trim($_POST['download_template_pagingfooter']);
	$download_template_category_header = trim($_POST['download_template_category_header']);
	$download_template_category_footer = trim($_POST['download_template_category_footer']);
	$download_template_listing[] = trim($_POST['download_template_listing']);
	$download_template_listing[] = trim($_POST['download_template_listing_2']);
	$download_template_embedded[] = trim($_POST['download_template_embedded']);
	$download_template_embedded[] = trim($_POST['download_template_embedded_2']);
	$download_template_most[] = trim($_POST['download_template_most']);
	$download_template_most[] = trim($_POST['download_template_most_2']);
	$update_download_queries = array();
	$update_download_text = array();
	$update_download_queries[] = update_option('download_template_header', $download_template_header);
	$update_download_queries[] = update_option('download_template_footer', $download_template_footer);
	$update_download_queries[] = update_option('download_template_pagingheader', $download_template_pagingheader);
	$update_download_queries[] = update_option('download_template_pagingfooter', $download_template_pagingfooter);
	$update_download_queries[] = update_option('download_template_category_header', $download_template_category_header);
	$update_download_queries[] = update_option('download_template_category_footer', $download_template_category_footer);
	$update_download_queries[] = update_option('download_template_listing', $download_template_listing);
	$update_download_queries[] = update_option('download_template_embedded', $download_template_embedded);
	$update_download_queries[] = update_option('download_template_most', $download_template_most);
	$update_download_text[] = __('Download Page Header', 'wp-downloadmanager');
	$update_download_text[] = __('Download Page Footer', 'wp-downloadmanager');
	$update_download_text[] = __('Download Page Paging Header', 'wp-downloadmanager');
	$update_download_text[] = __('Download Page Paging Footer', 'wp-downloadmanager');
	$update_download_text[] = __('Download Category Header Template', 'wp-downloadmanager');
	$update_download_text[] = __('Download Category Footer Template', 'wp-downloadmanager');
	$update_download_text[] = __('Download Listing Template', 'wp-downloadmanager');
	$update_download_text[] = __('Download Embedded Template', 'wp-downloadmanager');
	$update_download_text[] = __('Most Downloaded Template', 'wp-downloadmanager');
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


### Get Arrayed Templates
$download_template_embedded = get_option('download_template_embedded');
$download_template_listing = get_option('download_template_listing');
$download_template_most = get_option('download_template_most');
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
			case "pagingheader":
				default_template = "";
				break;
			case "pagingfooter":
				default_template = "";
				break;
			case "category_header":
				default_template = "<h2 id=\"downloadcat-%CATEGORY_ID%\"><a href=\"%CATEGORY_URL%\" title=\"<?php _e('View all downloads in %FILE_CATEGORY_NAME%', 'wp-downloadmanager'); ?>\">%FILE_CATEGORY_NAME%</a></h2>";
				break;
			case "category_footer":
				default_template = "";
				break;
			case "listing":
				default_template = "<p><img src=\"<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-downloadmanager/images/drive_go.gif\" alt=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" style=\"vertical-align: middle;\" />&nbsp;&nbsp;<strong><a href=\"%FILE_DOWNLOAD_URL%\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\">%FILE_NAME%</a></strong><br /><strong>&raquo; %FILE_SIZE% - %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?> - %FILE_DATE%</strong><br />%FILE_DESCRIPTION%</p>";
				break;
			case "embedded":
				default_template = "<p><img src=\"<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-downloadmanager/images/drive_go.gif\" alt=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\" style=\"vertical-align: middle;\" />&nbsp;&nbsp;<strong><a href=\"%FILE_DOWNLOAD_URL%\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\">%FILE_NAME%</a></strong> (%FILE_SIZE%, %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?>)</p>";
				break;
			case "listing_2":
				default_template = "<p><img src=\"<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-downloadmanager/images/drive_go.gif\" alt=\"\" title=\"\" style=\"vertical-align: middle;\" />&nbsp;&nbsp;<strong>%FILE_NAME%</strong><br /><i><?php _e('You need to be a registered user to download this file.', 'wp-downloadmanager'); ?></i><br /><strong>&raquo; %FILE_SIZE% - %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?> - %FILE_DATE%</strong><br />%FILE_DESCRIPTION%</p>";
				break;
			case "embedded_2":
				default_template = "<p><img src=\"<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-downloadmanager/images/drive_go.gif\" alt=\"\" title=\"\" style=\"vertical-align: middle;\" />&nbsp;&nbsp;<strong>%FILE_NAME%</strong> (%FILE_SIZE%, %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?>)<br /><i><?php _e('You need to be a registered user to download this file.', 'wp-downloadmanager'); ?></i></p>";
				break;
			case 'most':
				default_template = "<li><strong><a href=\"%FILE_DOWNLOAD_URL%\" title=\"<?php _e('Download: %FILE_NAME%', 'wp-downloadmanager'); ?>\">%FILE_NAME%</a></strong> (%FILE_SIZE%, %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?>)</li>";
				break;
			case 'most_2':
				default_template = "<li><strong>%FILE_NAME%</strong> (%FILE_SIZE%, %FILE_HITS% <?php _e('hits', 'wp-downloadmanager'); ?>)<br /><i><?php _e('You need to be a registered user to download this file.', 'wp-downloadmanager'); ?></i></li>";
				break;
		}
		document.getElementById("download_template_" + template).value = default_template;
	}
/* ]]> */
</script>
<div class="wrap"> 
	<h2><?php _e('Download Templates', 'wp-downloadmanager'); ?></h2> 
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<h3><?php _e('Template Variables', 'wp-downloadmanager'); ?></h3>
	<table class="widefat">
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
		<tr class="alternate">
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
		<tr class="alternate">
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
			<td>
				<strong>%CATEGORY_ID%</strong><br />
				<?php _e('Display the category ID.', 'wp-downloadmanager'); ?>
			</td>
		</tr>
	</table>
	<h3><?php _e('Download Page Templates', 'wp-downloadmanager'); ?></h3>
	<table class="form-table">
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('header');" class="button" />
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('footer');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_footer" name="download_template_footer"><?php echo htmlspecialchars(stripslashes(get_option('download_template_footer'))); ?></textarea></td>
		 </tr>
		 <tr valign="top">
			<td width="30%" align="left">
				<strong><?php _e('Download Page Paging Header:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
				<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
				- N/A<br /><br />
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('pagingheader');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_pagingheader" name="download_template_pagingheader"><?php echo htmlspecialchars(stripslashes(get_option('download_template_pagingheader'))); ?></textarea></td>
		 </tr>
		 <tr valign="top">
			<td width="30%" align="left">
				<strong><?php _e('Download Page Paging Footer:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
				<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />
				- N/A<br /><br />
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('pagingfooter');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_pagingfooter" name="download_template_pagingfooter"><?php echo htmlspecialchars(stripslashes(get_option('download_template_pagingfooter'))); ?></textarea></td>
		 </tr>
	</table>
	<h3><?php _e('Download Category Templates', 'wp-downloadmanager'); ?></h3>
	<table class="form-table">
		 <tr valign="top">
			<td width="30%" align="left">
				<strong><?php _e('Download Category Header:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
				<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />	
				- %FILE_CATEGORY_NAME%<br />
				- %CATEGORY_ID%<br />
				- %CATEGORY_URL%<br />
				- %CATEGORY_FILES_COUNT%<br />
				- %CATEGORY_HITS%<br />
				- %CATEGORY_SIZE%<br /><br />
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('category_header');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_category_header" name="download_template_category_header"><?php echo htmlspecialchars(stripslashes(get_option('download_template_category_header'))); ?></textarea></td>
		</tr>
		<tr valign="top">
			<td width="30%" align="left">
				<strong><?php _e('Download Category Footer:', 'wp-downloadmanager'); ?></strong><br /><br /><br />
				<?php _e('Allowed Variables:', 'wp-downloadmanager'); ?><br />	
				- %FILE_CATEGORY_NAME%<br />
				- %CATEGORY_ID%<br />
				- %CATEGORY_URL%<br />
				- %CATEGORY_FILES_COUNT%<br />
				- %CATEGORY_HITS%<br />
				- %CATEGORY_SIZE%<br /><br />
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('category_footer');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_category_footer" name="download_template_category_footer"><?php echo htmlspecialchars(stripslashes(get_option('download_template_category_footer'))); ?></textarea></td>
		</tr>
	</table>
	<h3><?php _e('Download Templates (With Permission)', 'wp-downloadmanager'); ?></h3>
	<table class="form-table">
		<tr valign="top"> 
			<td width="30%" align="left">
				<strong><?php _e('Download Listing:', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing files in the downloads page and users have permission to download the file.', 'wp-downloadmanager'); ?><br /><br />
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('listing');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_listing" name="download_template_listing"><?php echo htmlspecialchars(stripslashes($download_template_listing[0])); ?></textarea></td> 
		</tr>
		<tr valign="top"> 
			<td width="30%" align="left">
				<strong><?php _e('Download Embedded File', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when you embedded a file within a post or a page and users have permission to download the file.', 'wp-downloadmanager'); ?><br /><br />
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('embedded');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_embedded" name="download_template_embedded"><?php echo htmlspecialchars(stripslashes($download_template_embedded[0])); ?></textarea></td> 
		</tr>
	</table>
	<h3><?php _e('Download Templates (Without Permission)', 'wp-downloadmanager'); ?></h3>
	<table class="form-table">
		<tr valign="top"> 
			<td width="30%" align="left">
				<strong><?php _e('Download Listing:', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing files in the downloads page and users <strong>DO NOT</strong> have permission to download the file.', 'wp-downloadmanager'); ?><br /><br />
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('listing_2');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_listing_2" name="download_template_listing_2"><?php echo htmlspecialchars(stripslashes($download_template_listing[1])); ?></textarea></td> 
		</tr>
		<tr valign="top"> 
			<td width="30%" align="left">
				<strong><?php _e('Download Embedded File', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when you embedded a file within a post or a page and users <strong>DO NOT</strong> have permission to download the file.', 'wp-downloadmanager'); ?><br /><br />
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('embedded_2');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_embedded_2" name="download_template_embedded_2"><?php echo htmlspecialchars(stripslashes($download_template_embedded[1])); ?></textarea></td> 
		</tr>
	</table>
	<h3><?php _e('Download Stats Templates (With Permission)', 'wp-downloadmanager'); ?></h3>
	<table class="form-table">
		<tr valign="top"> 
			<td width="30%" align="left">
				<strong><?php _e('Most Downloaded', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing most downloaded files.', 'wp-downloadmanager'); ?><br />
				<strong><?php _e('Recent Downloads', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing recent downloads.', 'wp-downloadmanager'); ?><br />
				<strong><?php _e('Downloads By Category', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing downloads by category.', 'wp-downloadmanager'); ?><br /><br />
				<?php _e('Displayed when users have permission to download the file.', 'wp-downloadmanager'); ?><br /><br />
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('most');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_most" name="download_template_most"><?php echo htmlspecialchars(stripslashes($download_template_most[0])); ?></textarea></td> 
		</tr>
	</table>
	<h3><?php _e('Download Stats Templates (Without Permission)', 'wp-downloadmanager'); ?></h3>
	<table class="form-table">
		<tr valign="top"> 
			<td width="30%" align="left">						
				<strong><?php _e('Most Downloaded', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing most downloaded files.', 'wp-downloadmanager'); ?><br />
				<strong><?php _e('Recent Downloads', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing recent downloads.', 'wp-downloadmanager'); ?><br />
				<strong><?php _e('Downloads By Category', 'wp-downloadmanager'); ?></strong><br />
				<?php _e('Displayed when listing downloads by category.', 'wp-downloadmanager'); ?><br /><br />
				<?php _e('Displayed when users <strong>DO NOT</strong> have permission to download the file.', 'wp-downloadmanager'); ?><br /><br />
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
				<input type="button" name="RestoreDefault" value="<?php _e('Restore Default Template', 'wp-downloadmanager'); ?>" onclick="download_default_templates('most_2');" class="button" />
			</td>
			<td align="left"><textarea cols="80" rows="12" id="download_template_most_2" name="download_template_most_2"><?php echo htmlspecialchars(stripslashes($download_template_most[1])); ?></textarea></td> 
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" class="button" value="<?php _e('Save Changes', 'wp-downloadmanager'); ?>" />
	</p>
	</form> 
</div> 