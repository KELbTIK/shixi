{breadcrumbs}<a href="{$GLOBALS.site_url}/settings/">[[System Settings]]</a> &#187; [[View Error Log]]{/breadcrumbs}
<h1>[[View Error Log]]</h1>

<form method="post">
	<p>[[Last]]
		<select name="recordsNum" onchange="submit()">
		<option value="10" {if $recordsNum == 10}selected="selected"{/if}>10</option>
		<option value="20" {if $recordsNum == 20}selected="selected"{/if}>20</option>
		<option value="50" {if $recordsNum == 50}selected="selected"{/if}>50</option>
		<option value="100" {if $recordsNum == 100}selected="selected"{/if}>100</option>
		</select>
	 	[[records from log]]</p>
</form>

<div id="log">
	{foreach from=$errorLog item=error}
		<hr size="3" noshade>
		<h3 style="border-bottom:1px dashed #666; padding-bottom: 10px;">[[Date]]: {$error.date}</h3>
		{$error.errors}
		<br />
	{/foreach}
</div>