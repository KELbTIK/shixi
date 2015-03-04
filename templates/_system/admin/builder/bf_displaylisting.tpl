<h1>[[Display {$listingTypeInfo.name} Page]]</h1>
<h2>[[Current Theme]]: <strong>{$currentTheme}</strong></h2>
<div id="displayListing">
{if $errors}
	{foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code == 'WRONG_LISTING_TYPE_ID_SPECIFIED'}
				[[Listing Type ID is not defined OR is not valid]]
			{/if}
		</p>
	{/foreach}
{else}
	<div id="listingsResults" class="left">
		<div class="listingInfo">
			{include file="bf_displaylisting_fieldsholders.tpl"}
			<div class="clr"><br/></div>
		</div>
	</div>
	<div class="clr"><br/></div>
	{if $listingTypeInfo.id eq 'Resume'}
		<p class="template-url"><a href="{$GLOBALS.site_url}/edit-templates/?module_name=classifieds&template_name=display_resume.tpl" title="">
			[[You can also create this form manually using the following template]]</a></p>
		<div class="clr"><br/></div>
	{elseif $listingTypeInfo.id eq 'Job'}
		<p class="template-url"><a href="{$GLOBALS.site_url}/edit-templates/?module_name=classifieds&template_name=display_job.tpl" title="">
			[[You can also create this form manually using the following template]]</a></p>
		<div class="clr"><br/></div>
	{/if}
{/if}
</div>

{include file="manage_panel.tpl"}



