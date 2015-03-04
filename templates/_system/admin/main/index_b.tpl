<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	    <title>SmartJobBoard [[Admin Panel]] {if $TITLE ne ""} :: [[{$TITLE}]] {/if}</title>
		<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
		<link rel="StyleSheet" type="text/css" href="{image src="./css/formbuilder.css"}" />
		<link rel="StyleSheet" type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/css/jquery-ui.css"  />
		<link rel="stylesheet" type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/css/jquery.multiselect.css" />
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery-ui.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.bgiframe.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.form.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.highlight.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.validate.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.autocomplete.pack.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
		{capture name="displayJobProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}
        <script type="text/javascript">
			$.ui.dialog.prototype.options.bgiframe = true;
			function popUpWindow(data, widthWin, heightWin, title) {ldelim}
				reloadPage = false;
				$("#messageBox").dialog('destroy').html('{$smarty.capture.displayJobProgressBar|escape:'quotes'}');
				$("#messageBox").dialog({ldelim}
					width: widthWin,
					height: heightWin,
					modal: true,
					title: title
				{rdelim}).dialog( 'open' );
				$("#messageBox").html($("#"+data).html());
				return false;
			{rdelim}
		</script>
		<script language="JavaScript" type="text/javascript" src="{image src="./js/form_builder.js"}"></script>
	</head>
	<body>
		<div id="messageBox"></div>
		<div id="statusBox"></div>
		<div class="indexDiv">
			{module name='flash_messages' function='display'}
			{$MAIN_CONTENT}
		</div>
	</body>
</html>
