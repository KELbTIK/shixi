<div class="browse">
<a href="{$GLOBALS.site_url}{$user_page_uri}">[[$TITLE]]</a>
{foreach from=$browse_navigation_elements item=element name="nav_elements"}
{title}{tr metadata=$element.metadata mode="raw"}{$element.caption}{/tr}{/title}
{keywords}{tr metadata=$element.metadata mode="raw"}{$element.caption}{/tr}{/keywords}
{description}{tr metadata=$element.metadata mode="raw"}{$element.caption}{/tr}{/description}
 / 
  {if $smarty.foreach.nav_elements.last} 	
  	{tr metadata=$element.metadata}{$element.caption}{/tr} 	
  {else}
  	<a href="{$GLOBALS.site_url}{$element.uri}">{tr metadata=$element.metadata}{$element.caption}{/tr}</a>
  {/if}
{/foreach}
</div>

{include file="error.tpl"}
{if empty($listings)}
	{if !empty($browseItems)}
		<div class="treeContentDiv" >{$browseItems}</div>
	{else}
		<div class="browse" style="text-align: center; margin-left: auto; margin-right: auto;">
			[[There are no postings meeting the criteria you specified]]
		</div>
	{/if}
{else}
{include file="search_results_jobs.tpl"}
{/if}

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
