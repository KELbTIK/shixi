<div class="browse">
	<a href="{$GLOBALS.site_url}{$user_page_uri}">[[$TITLE]]</a>
	{foreach from=$browse_navigation_elements item=element name="nav_elements"}
	{title}{tr metadata=$element.metadata mode="raw"}{$element.caption}{/tr|escape:'html'}{/title}
	{keywords}{tr metadata=$element.metadata mode="raw"}{$element.caption}{/tr|escape:'html'}{/keywords}
	{description}{tr metadata=$element.metadata mode="raw"}{$element.caption}{/tr|escape:'html'}{/description}
	/
	{if $smarty.foreach.nav_elements.last}
		{tr metadata=$element.metadata}{$element.caption}{/tr|escape:'html'}
	  {else}
		<a href="{$GLOBALS.site_url}{$element.uri|escape:'url'}">{tr metadata=$element.metadata}{$element.caption}{/tr|escape:'html'}</a>
	{/if}
	{/foreach}
</div>
<script type="text/javascript">
    {literal}
    $(document).ready(function(){
        if(window.location.href.indexOf("view=map") > -1) {
            $(".browse").addClass("browse_right");
        }
    });
    {/literal}
</script>
{include file="error.tpl"}
{if empty($listings)}
	<div class="noRefine">
		<table width="90%" cellpadding="7" cellspacing="5" id="browse-items">
			<tr valign=top>
				{assign var="columnCount" value="5"}
				{foreach from=$browseItems key=elementId item=elementCount name=browseItems}
					<td>
						<a href="{$sitePageUri}{$elementId|replace:"/":"-or-"|escape:"url"}/" class="browseItems">{tr}{$elementId}{/tr|escape:'html'}
							{if $GLOBALS.settings.enableBrowseByCounter}({$elementCount}){/if}
						</a>
					</td>
					{if $smarty.foreach.browseItems.iteration % $columnCount == 0}</tr><tr>{/if}
				{foreachelse}
					<td>[[There are no listings with requested parameters in the system.]]</td>
				{/foreach}
			</tr>
		</table>
	</div>
{elseif $listing_type == 'Resume'}
	{include file="search_results_resumes.tpl"}
{else}
	{include file="search_results_jobs.tpl"}
{/if}
