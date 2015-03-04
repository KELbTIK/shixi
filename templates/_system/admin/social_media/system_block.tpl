{foreach from=$systemFields item=formField}
	<tr class="{cycle values = 'evenrow,oddrow'}">
		<td><label for="{$formField.id}">[[$formField.caption]]</label></td>
		<td class="required">{if $formField.is_required == 1}*{/if}</td>
		<td class="clear-border-left">
			{if $formField.is_system == 1}
				{if $formField.id == 'account_id' && !$authorized && $network != 'twitter'}
					<a href="{$GLOBALS.site_url}/social-media/?action=authorize&amp;soc_network={$network}">[[Authorize]]</a>
				{elseif $formField.id == 'account_id' && $authorized && $network != 'twitter'}
					<input type="hidden" name="account_id" value="{display property=$formField.id}" />
					{display property=$formField.id}&nbsp;&nbsp;
					<a href="{$GLOBALS.site_url}/social-media/?{$change_url}" {if $approveAccount || $expired}class="redButton"{else}class="grayButton"{/if}>[[Change / Grant permission]]</a>
				{else}
					{if $formField.id == 'groups'}
						{input property=$formField.id template="linkedin_group_list.tpl"}
					{else}
						{assign var=parentID value=false scope=global}
						{input property=$formField.id}
					{/if}
				{/if}
			{/if}
			{if $formField.id == 'api_key'}<small>[[To acquire an API key, visit the APIs Console. The API key is in the API Access pane's Simple API Access section]].</small>{/if}
			{if $formField.comment}<br/><small>[[{$formField.comment}]]</small>{/if}
		</td>
	</tr>
{/foreach}