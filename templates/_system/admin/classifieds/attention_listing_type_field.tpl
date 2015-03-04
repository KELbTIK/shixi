{if $errors}
	{foreach item="error" from=$errors}
	<p class="error">[[{$error}]]</p>
	{/foreach}
{else}
	{breadcrumbs}
		{if $listing_type_id == 'Job/Resume'}
		<a href="{$GLOBALS.site_url}/listing-fields/">[[Listing Fields]]</a> &#187; [[Listing Field Added]]
			{else}
		<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$listing_type_sid}">[[{$listing_type_id}]]</a> &#187;  [[Listing Field Added]]
		{/if}
	{/breadcrumbs}

	<p class="message">[[You have successfully added a new listings field]].</p>
	<p>[[The field you created will be automatically added to the {$listing_type_id} posting form]].</p>
	<p>[[It will be also added to the list of inactive fields of the Form Builder for search and display pages]].</p>
	<p>
		[[If you want this field to display on the search form and listing details page please use Form Builder and drag the new field to the place you need]]:
	</p>
	{include file='../builder/form_builder_table.tpl'}
{/if}