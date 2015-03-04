<h1>[[Add Listing]]</h1>
{foreach from=$listing_types_info item=listing_type_info}
<p><a href="?listing_package_id={$listing_package_id}&listing_type_id={$listing_type_info.id}">[[{$listing_type_info.name}]]</a></p>
{/foreach}