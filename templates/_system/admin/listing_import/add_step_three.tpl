{breadcrumbs}<a href="{$GLOBALS.site_url}/show-import/">[[XML Import]]</a> &#187; [[Save Data Source]]{/breadcrumbs}
<h1><img src="{image}/icons/recycle32.png" border="0" alt="" class="titleicon"/> [[External data source]]</h1>
<p>[[Final step for add parser]].</p>

{if $errors}
	{foreach from=$errors item=error}
		<p class="error">[[{$error}]]</p>
	{/foreach}
	<form method="post" action="{$GLOBALS.site_url}/add-import/">
		<input type="hidden" name="add_level" value="2"/>
		<input type="hidden" name="xml" value="{$xml}"/>
		<input type="hidden" name="parser_name" id="parser_name" value="{$form_name}"/>
		<input type="hidden" name="parser_url" id="parser_url" width="100%" value="{$form_url}"/>
		<input type="hidden" name="parser_user" id="parser_user" value="{$form_user}"/>
		<input type="hidden" name="parser_days" id="parser_days" value="{$form_days}"/>
        <div class="floatRight"><input type="submit" value="[[Back]]" class="grayButton" /></div>
	</form>
{else}	

{/if}