<input type='hidden' name="{if $complexField}{$complexField}[{$id}][{$complexStep}][tree]{else}{$id}[tree]{/if}" id='tree_{$id}_selected' class="{if $complexField}complexField{/if}" value="" />


<div class="tree_select_box l1" id="tree_{$id}">
	<div id="div_content_{$id}" class="bd">
		[[Please wait ...]]<img src='{$GLOBALS.site_url}/system/ext/jquery/progbar.gif' alt="[[Please wait ...]]" />
	</div>
</div>

<script type="text/javascript">
var link = "{$GLOBALS.user_site_url}/tree-options/?userTree={$userTree}";
//var tree_{$id}_select = new Array({foreach from=$value item=sel key=k}{if $k>0},{/if}{$sel}{/foreach});
var selectedStr_{$id} = '{foreach from=$value item=sel key=k}{if $k>0},{/if}{$sel}{/foreach}';
	
makeRequest_{$id}({$sid}, "{$id}",0, $("#tree_{$id}"));
$("#tree_{$id}_selected").attr("value", selectedStr_{$id});
	
{literal}
function makeRequest_{/literal}{$id}{literal}(treeFieldSID, fieldName, parent, elem){
	$.get(link, {id:treeFieldSID, name:fieldName, parentSID:parent, check: selectedStr_{/literal}{$id}{literal}},
		function(data){
			elem.html(data);
		}
	);
}{/literal}
	
function goTroughSelectedElements_{$id}(level, childLevel){ldelim}
	$("#select_tree_{$id}_level_"+level+" option:selected").each(function(){ldelim}
		if($(this).val()){ldelim}
			makeRequest_{$id}({$sid},"{$id}",$(this).val(),$("#tree_{$id}_level_"+childLevel));
		{rdelim}else{ldelim}
			$("#tree_{$id}_level_"+(parseInt(level)+1)).html('');
		{rdelim}
	{rdelim});
{rdelim}
	
function saveTreeElement_{$id}(level){ldelim}
	var i=0;
	for (i=1;i<=level;i++){ldelim}
		$("#select_tree_{$id}_level_"+i+" option:selected").each(function(){ldelim}
			if($(this).val()){ldelim}
				selectedStr_{$id} = $(this).val();
			{rdelim}else{ldelim}
				if (i==1){ldelim}
					selectedStr_{$id} = "";
				{rdelim}
			{rdelim}
		{rdelim});
	{rdelim}
	$("#tree_{$id}_selected").attr("value", selectedStr_{$id} );
{rdelim}
</script>