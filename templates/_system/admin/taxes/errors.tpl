{if $action == 'edit'}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-taxes/">[[Tax Rules]]</a> &#187; [[Edit Tax Rule]]{/breadcrumbs}
	<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Edit Tax Rule]]</h1>
{/if}
{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE' or $error eq 'NOT_VALID_STRING_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not valid]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_STRING_ID_VALUE'}
		<p class="error">[[Use at least one A-Z letter value in the '{$field_caption}' field]]</p>
	{elseif $error eq 'OUT_OF_RANGE'}
		<p class="error">'[[{$field_caption}]]' [[value is out of range]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an float value]]</p>
	{elseif $error eq 'WRONG_TAX_ID_SPECIFIED'}
		<p class="error">[[There is no tax in the system with the specified ID]]</p>
	{elseif $error eq "NOT_UNIQUE_COUNTRY_AND_STATE"}
		<p class="error">[[The tax with a Country and State you’re trying to use is already created. You can create only one tax for particular Country and State.]]</p>
	{else}
		<p class="error">'[[{$field_caption}]]' {$error}</p>
	{/if}
{/foreach}
