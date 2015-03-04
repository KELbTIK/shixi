<select name='{$id}[equal]' onchange="reloadWithParameter(this.value)">
	<option value="">[[Any]] [[{$caption|escape:"html"}]]</option>
	{foreach from=$list_values item=list_value}
		<option value='{$list_value.id}' {if $selected_listing_type_id === $list_value.id}selected="selected"{/if} >[[{$list_value.caption|escape:"html"}]]</option>
	{/foreach}
</select>
<script language="Javascript">
function reloadWithParameter(param)
{
	window.location = "?listing_type_id="+param;
}
</script>