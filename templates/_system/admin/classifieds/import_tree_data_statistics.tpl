{if $field.listing_type_sid}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">[[$type_info.name]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">[[$field.caption]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-tree/?field_sid={$field_sid}">[[Edit Tree]]</a>
		&#187; [[Import Tree Data]]
	{/breadcrumbs}

{else}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/listing-fields/">[[Listing Fields]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$field_sid}">[[$field.caption]]</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-tree/?field_sid={$field_sid}">[[Edit Tree]]</a>
		&#187; [[Import Tree Data]]
	{/breadcrumbs}
{/if}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon"/>[[Import Tree Data]]</h1>
[[Number of imported items]]: {$count}