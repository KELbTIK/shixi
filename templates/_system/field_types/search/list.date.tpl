<select name='{$id}[multi_like][]' class="searchList">
	<option value="">[[Any]] [[Date]]</option>
	{foreach from=$list_values item=list_value}
		<option value="{$list_value.id}" {foreach from=$value.multi_like item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >{tr mode="raw"} {$list_value.caption} {/tr|escape:'html'}</option>
	{/foreach}
</select>