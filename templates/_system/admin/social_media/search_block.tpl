{foreach from=$listingFields item=listingFieldItem}
	<tr class="{cycle values = 'evenrow,oddrow'}">
		<td><label for="{$listingFieldItem.id}">[[$listingFieldItem.caption]]</label></td>
		<td class="required">{if $listingFieldItem.is_required == 1}*{/if}</td>
		<td class="clear-border-left">
			{if $listingFieldItem.is_system == 1}
				{assign var=parentID value=false scope=global}
				{input property=$listingFieldItem.id}
			{else}
				{if $listingFieldItem.type == 'location'}
					{foreach from=$listingFields item=listingField}
						{if $listingField.id == $listingFieldItem.id}
							{search property=$listingField.id fields=$listingField.fields}
						{/if}
					{/foreach}
				{else}
					{search property=$listingFieldItem.id}
				{/if}
			{/if}
			{if $listingFieldItem.comment}<br/><small>[[{$listingFieldItem.comment}]]</small>{/if}
		</td>
	</tr>
{/foreach}