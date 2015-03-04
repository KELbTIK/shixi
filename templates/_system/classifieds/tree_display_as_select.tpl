<div id="tree_{$name}_level_{$level}">
	{if $levelName}
		<div class="tree-as-sb-cptn">[[$levelName]]:</div>
	{/if}
	<div class="tree-as-sb-vals">
		<select name="select_tree_{$name}_level_{$level}" id="select_tree_{$name}_level_{$level}">
			<option value="">[[Select {$name} {$levelName}]]</option>
			{foreach from=$tree_values item=treeItem}
			<option value="{$treeItem.sid}" {if in_array($treeItem.sid,$checked)}selected="selected"{/if}>{$treeItem.caption}</option>
			{/foreach}
		</select>
	</div>
	<div class="clr"></div>
	<div id="tree_{$name}_level_{$level+1}"></div>
</div>

<script type="text/javascript">
	goTroughSelectedElements_{$name}("{$level}", "{$level+1}");
	$("#select_tree_{$name}_level_{$level}").change(function(){ldelim}
		goTroughSelectedElements_{$name}("{$level}", "{$level+1}");
		saveTreeElement_{$name}("{$level}");
	{rdelim});
</script>