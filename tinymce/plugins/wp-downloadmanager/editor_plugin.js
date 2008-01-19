tinyMCE.importPluginLanguagePack('wp-downloadmanager');
var TinyMCE_DownloadsPlugin = {
	getInfo : function() {
		return {
			longname : 'WP-DownloadManager',
			author : 'Lester Chan',
			authorurl : 'http://lesterchan.net',
			infourl : 'http://lesterchan.net/portfolio/programming.php',
			version : "1.30"
		};
	},
	getControlHTML : function(cn) {
		switch (cn) {
			case "wp-downloadmanager":
				return tinyMCE.getButtonHTML(cn, 'lang_downloads_insert', '{$pluginurl}/images/download.gif', 'mceDownloadInsert');
		}
		return "";
	},
	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mceDownloadInsert":
				tinyMCE.execInstanceCommand(editor_id, "mceInsertContent", false, insertDownload('visual', ''));
			return true;
		}
		return false;
	}
};
tinyMCE.addPlugin("wp-downloadmanager", TinyMCE_DownloadsPlugin);