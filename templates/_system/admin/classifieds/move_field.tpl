<div>[[Select a page to move this field to:]]</div>
<div>
	<select name="movePageID" id="movePageID">
		{foreach from=$pages item=page name=page_loop}
			{if $page.sid != $pageInfo.sid}<option value="{$page.sid}">{$page.page_name}</option>{/if}
		{/foreach}
	</select>
</div>