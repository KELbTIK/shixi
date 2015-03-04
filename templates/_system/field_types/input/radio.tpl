{foreach from=$list_values item=list_value}
<input type="radio" name="{$id}" {if $list_value.id == $value}checked="checked"{/if} value="{$list_value.id}" onclick="addAnswer(this.value)" />&nbsp;[[{$list_value.caption}]]<br/>
{/foreach}

{literal}
<script type="text/javascript">
<!--
function addAnswer(val) {
	if (val == 'boolean') {
		$("#boolean").css("display", "block");
		$("#answers").css("display", "none");
	}
	else if (val != 'string') {
		$("#boolean").css("display", "none");
		$("#answers").css("display", "block");
	}
	else if (val == 'string') {
		$("#boolean").css("display", "none");
		$("#answers").css("display", "none");
	}
}
//-->
</script>
{/literal}