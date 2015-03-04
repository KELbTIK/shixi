<h1>{if $action == 'save'}[[Add new {$listing_type_id|strtolower} alert]]{elseif $action == 'edit'}[[Edit {$listing_type_id|strtolower} alert]]{/if}</h1>

{foreach from=$errors item=message key=error}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[Alert Name]]' [[is empty]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}

<form action="" method="post" id="search_form" onsubmit="disableSubmitButton('submitSave');">
	<input type="hidden" name="action" value="{$action|htmlspecialchars}" />
	<input type="hidden" name="listing_type[equal]" value="{$listing_type_id}" />
	{if $action == 'edit'}<input type="hidden" name="id_saved" value="{$id_saved}" />{/if}
	<fieldset>
		<div class="inputName">[[Alert Name]]:</div>
		<div class="inputField">{search property=name template='string.tpl'}</div>
	</fieldset>
	{include file="../builder/bf_searchform_fieldsholders.tpl"}
	<fieldset>
		<div class="inputName">[[Email frequency]]</div>
		<div class="inputField">
			{search property="email_frequency"}
		</div>
	</fieldset>
	<fieldset>
		<div class="inputName"><input type="button" value="[[Back]]" class="button" onclick="history.back()"/></div>
		<div class="inputField"><input type="submit" name="submit" value="[[Save]]" id="submitSave" class="button" /></div>
	</fieldset>
</form>
<div class="clr"><br/></div>