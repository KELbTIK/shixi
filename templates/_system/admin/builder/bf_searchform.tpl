<h1>[[Search {$listingTypeInfo.name} Form]]</h1>
<h2>[[Current Theme]]: <strong>{$currentTheme}</strong></h2>
<div class="clr"></div>
<div id="adMargin">
	{include file="bf_searchform_fieldsholders.tpl"}
</div>
<div class="clr"></div>
{if $listingTypeInfo.id eq 'Resume'}
	<p class="template-url"><a href="{$GLOBALS.site_url}/edit-templates/?module_name=classifieds&template_name=search_form_resumes.tpl" title="">
		[[You can also create this form manually using the following template]]</a></p>
	<div class="clr"><br/></div>
{elseif $listingTypeInfo.id eq 'Job'}
	<p class="template-url"><a href="{$GLOBALS.site_url}/edit-templates/?module_name=classifieds&template_name=search_form.tpl" title="">
		[[You can also create this form manually using the following template]]</a></p>
	<div class="clr"><br/></div>
{/if}
{include file="manage_panel.tpl"}