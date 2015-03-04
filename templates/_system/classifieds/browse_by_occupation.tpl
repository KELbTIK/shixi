<div class="treeContentDiv" style="width:95%; margin: 0 10px 0 10px;" >{$browseItems}</div>

<script type="text/javascript">
{literal}
	function openLevel(id) {
		 $("#browse_tree_li_"+id).children("ul").each(function(ul){
             if ($(this).css('display') == 'block') {
             	$(this).hide();
             	$("#browse_tree_arrow_"+id).removeClass().addClass("arrow").addClass("collapsed");
             }
             else { 
             	$(this).show();
             	$("#browse_tree_arrow_"+id).removeClass().addClass("arrow").addClass("expanded");
             }
	     });
	}
{/literal}
</script>