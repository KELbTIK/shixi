<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	    <title>{$GLOBALS.settings.site_title} {if $TITLE ne ""} :: [[{$TITLE}]] {/if}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
		<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/form.css" />
		{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
		{literal}<style type="text/css">body {background: white !important;}</style>{/literal}
	</head>
	<body>
		{module name='flash_messages' function='display'}
		{$MAIN_CONTENT}
	</body>
</html>