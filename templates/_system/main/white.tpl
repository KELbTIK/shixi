<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	    <title>{$GLOBALS.settings.site_title}{if $TITLE ne ""} :: {$TITLE} {/if}</title>
	</head>
	<body>
		{module name='flash_messages' function='display'}
		{$MAIN_CONTENT}
	</body>
</html>