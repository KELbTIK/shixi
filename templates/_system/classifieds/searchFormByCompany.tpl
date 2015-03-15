<form action="{$GLOBALS.site_url}/browse-by-company/" id="search_form" class="form-horizontal">
	<input type="hidden" name="action" value="search" />
	<div class="form-group has-feedback">
		<div class="bcName">[[Company Name]]</div>
		<div class="bcField">{search property=CompanyName  template="string.like.tpl"}</div>
	</div>
	{foreach from=$userGroupFields item=field}
		{if $field.id == 'Location'}
			{search property=$field.id fields=$field.fields template="locationCompany.tpl"}
		{/if}
	{/foreach}
	<div class="form-group has-feedback">
		<div class="bcName">&nbsp;</div>
		<div class="bcField"><input type="submit" class="button btn btn-primary" value="[[Find]]" /></div>
	</div>
</form>