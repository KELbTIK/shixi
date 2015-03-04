{breadcrumbs}<a href="{$GLOBALS.site_url}/show-import/">[[XML Import]]</a> &#187; [[Result Execute Data Source]]{/breadcrumbs}
<h1>[[External data source]]</h1>
<p>[[Result job from import]].</p>

{if count($errors) > 0}
	{foreach from=$errors item=error}
		<p class="error">[[{$error}]]</p>
	{/foreach}
{else}
	<p>[[Import completed]].</p>
	{foreach from=$result.errors item=error}
		<p class="error">[[Error]]: [[Backend!]][[{$error}]]</p>
	{/foreach}
{/if}