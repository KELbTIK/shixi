<div class="location">[[Add Listing]]</div>
<p class="page_title">[[Add Listing]]</p>
{foreach from=$listing_types_info item=listing_type_info}
<p><a href="?listing_type_id={$listing_type_info.id}">[[{$listing_type_info.name}]]</a></p>
{/foreach}