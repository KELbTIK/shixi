{if is_array($filesInfo)}
	{if $listing.id && $filesInfo.$complexStep.saved_file_name}
		<a href="?filename={$filesInfo.$complexStep.saved_file_name|escape:'url'}&amp;listing_id={$listing.id|escape:'url'}&amp;complex_field={$filesInfo.$complexStep.file_id}">{$filesInfo.$complexStep.file_name|escape:'html'}</a>
	{else}
		<a href="{$filesInfo.$complexStep.file_url|escape:'url'}">{$filesInfo.$complexStep.file_name|escape:'html'}</a>
	{/if}
{else}
	{if $listing.id && $value.saved_file_name}
		<a href="?filename={$value.saved_file_name|escape:'url'}&amp;listing_id={$listing.id|escape:'url'}">{$value.file_name|escape:'html'}</a>
	{else}
		<a href="{$value.file_url|escape:'url'}">{$value.file_name|escape:'html'}</a>
	{/if}
{/if}