<tr class="head">
	<td colspan="3">[[Posting Settings]]</td>
</tr>
{foreach from=$postingFields item=postingField}
	<tr class="{cycle values = 'evenrow,oddrow'} posting-settings">
		<td><label for="{$postingField.id}">[[$postingField.caption]]</label></td>
		<td class="required">{if $postingField.is_required == 1}*{/if}</td>
		<td class="clear-border-left">
			{if $postingField.is_system == 1}
				{assign var=parentID value=false scope=global}
				{if $postingField.id == "post_template"}
					{input property=$postingField.id template="textarea.tpl"}
				{else}
					{input property=$postingField.id}
				{/if}
			{else}
				{if $postingField.type == 'location'}
					{foreach from=$listingFields item=listingField}
						{if $listingField.id == $postingField.id}
							{search property=$postingField.id fields=$listingField.fields}
						{/if}
					{/foreach}
				{else}
					{search property=$postingField.id}
				{/if}
			{/if}
			{if $postingField.id == 'update_every'}&nbsp;[[listings]]{/if}
			{if $postingField.id == 'posting_limit'}&nbsp;[[per day]]{/if}
			{if $postingField.comment}<br/><small>[[{$postingField.comment}]]</small>{/if}
		</td>
	</tr>
{/foreach}
<tr class="head">
	<td colspan="3">[[If you want a certain field to appear in $networkName posts - copy its code and paste to the "Post template" input field.]]</td>
</tr>
{foreach from=$listingFields item=listingField}
	{if $listingField.type == 'location'}
		{foreach from=$listingField.fields item=locationField}
			<tr  class="{cycle values = 'evenrow,oddrow'}">
				<td colspan="2">[[$locationField.caption]]</td>
				<td class="clear-border-left">&#123;$listing.{$listingField.id}.{$locationField.id}&#125;</td>
			</tr>
		{/foreach}
	{else}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td colspan="2">[[$listingField.caption]]</td>
			<td class="clear-border-left">&#123;$listing.{$listingField.id}&#125;</td>
		</tr>
	{/if}
{/foreach}