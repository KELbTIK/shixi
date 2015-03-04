{if $listing.id && $value.saved_file_name}
	<a href="?filename={$value.saved_file_name|escape:'url'}&amp;listing_id={$listing.id|escape:'url'}">{$value.file_name|escape:'html'}</a>
{else}
	<a href="{$value.file_url|escape:'url'}">{$value.file_name|escape:'html'}</a>
{/if}