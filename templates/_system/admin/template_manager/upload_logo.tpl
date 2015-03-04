{breadcrumbs}[[Upload logo for theme]] {$theme}{/breadcrumbs}
<h1><img src="{image}/icons/wand32.png" border="0" alt="" class="titleicon"/>[[Upload logo for theme]] {$theme}</h1>

{foreach from=$errors item=error}
	<p class="error">
		{if $error == "NOT_ALLOWED_IN_DEMO"}
			[[Logo is not uploadable in demo]]
		{else}
			[[{$error}]]
		{/if}
	</p>
{/foreach}
{if $message}<p class="message">[[{$message}]]</p>{/if}

<p><img src="{$GLOBALS.user_site_url}/templates/{$theme}/main/images/logo.png" /></p>

<form method="post" enctype="multipart/form-data" action="">
	<input type="hidden" name="action" value="save" />
	<table>
			<tr>
				<td>[[File]]</td>
				<td><input type="file" name="logo" /><small>([[max.]] {$uploadMaxFilesize} M)</small></td>
			</tr>
			<tr>
				<td>[[Alternative text]]</td>
				<td><input type="text" name="logoAlternativeText" value="[[{$logoAlternativeText}]]" /></td>
			</tr>
			<tr>
				<td colspan="2">
                    <div class="floatRight"><input type="submit" name="submit" value="[[Save]]" class="grayButton" /></div>
				</td>
			</tr>
	</table>
</form>