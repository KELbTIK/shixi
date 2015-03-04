<option value="">{if $requestType == 'zipCodeSearch'}[[Any State]]{else}[[Select State]]{/if}</option>
{foreach from=$list_values item=list_value}
	<option value='{$list_value.caption|escape:"html"}'>{tr mode="raw"}[[{$list_value.caption}]]{/tr|escape:"html"}</option>
{/foreach}
