<form action="{$GLOBALS.site_url}/browse-by-company/" id="search_form" >
	<input type="hidden" name="action" value="search" />
	<fieldset>
		<div class="bcName">[[Company Name]]</div>
		<div class="bcField">{search property=CompanyName  template="string.like.tpl"}</div>
	</fieldset>
	{foreach from=$userGroupFields item=field}
		{if $field.id == 'Location'}
			{search property=$field.id fields=$field.fields template="locationCompany.tpl"}
		{/if}
	{/foreach}
	<fieldset>
		<div class="bcName">&nbsp;</div>
		<div class="bcField"><input type="submit" class="button" value="[[Find]]" /></div>
	</fieldset>
</form>