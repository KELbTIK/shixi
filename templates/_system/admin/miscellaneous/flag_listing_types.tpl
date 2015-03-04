<h1><img src="{image}/icons/linedpaperminus32.png" border="0" alt="" class="titleicon"/>[[Flag Listing Settings]]</h1>
<table>
	<thead>
	    <tr>
	    	<th>[[Listing Type]]</th>
	    	<th class="actions">[[Actions]]</th>
	    </tr>
    </thead>
    {foreach from=$listing_types item=type key=key}
	    <tr class="{cycle values = 'evenrow,oddrow'}">
	    	<td>[[{$type.name}]]</td>
	    	<td><a href="{$GLOBALS.site_url}/flag-listing-settings/?listing_type_id={$type.id}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
	    </tr>
    {/foreach}
</table>