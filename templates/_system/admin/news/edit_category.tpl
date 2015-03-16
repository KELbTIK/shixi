<script  type="text/javascript" src="{common_js}/pagination.js"></script>
{if $category.name != 'Archive'}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/news-categories/">[[News Categories]]</a> &#187; [[{$category.name}]] &#187; [[Edit Category]]{/breadcrumbs}
	<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Category]]</h1>
	{include file='../classifieds/field_errors.tpl'}
	{if $messages}
		{foreach from=$messages item=message key=number}
			{if $message == 'NEWS_SUCCESSFULLY_ADDED'}<p class="message">[[You have successfully added the news]]</p>{/if}
			{if $message == 'NEWS_CATEGORY_SUCCESSFULLY_SAVED'}<p class="message">[[You have successfully saved the news category]]</p>{/if}
			{if $message == 'NEWS_SUCCESSFULLY_DELETED'}<p class="message">[[You have successfully deleted the news]]</p>{/if}
			{if $message == 'NEWS_SUCCESSFULLY_SAVED'}<p class="message">[[You have successfully saved the news]]</p>{/if}
		{/foreach}
	{/if}
    <p><a href="{$GLOBALS.site_url}/manage-news/?action=add&category_sid={$category.sid}" class="grayButton">[[Add News]]</a></p>
	<form action="{$GLOBALS.site_url}/news-categories/" method=post>
		<fieldset>
			<legend>[[Edit Category]]</legend>
			<input type="hidden" name="action" value= "edit">
			<input type="hidden" name="category_sid" value="{$category.sid}" />
            <input type="hidden" id="submit" name="submit" value="save_category"/>
			<table class="fieldset">
				<tr class="">
					<td>[[Category Name]]</td>
					<td><input type="text" name="category_name" value="{$category.name}" /></td>
					<td></td>
					<td>
                        <div class="floatRight">
                            <input type="submit" id="apply" value="[[Apply]]" class="grayButton"/>
                            <input type="submit" value="[[Save]]" class="grayButton" />
                        </div>
                    </td>
				</tr>
			</table>
		</fieldset>
	</form>
{else}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/news-categories/">[[News Categories]]</a> &#187; {$category.name} &#187; [[View]]{/breadcrumbs}
	<h1><img src="{image}/icons/linedpaperlock32.png" border="0" alt="" class="titleicon"/>[[View Archive]]</h1>
	{if $messages}
		{foreach from=$messages item=message key=number}
			{if $message == 'NEWS_SUCCESSFULLY_DELETED'}<p class="message">[[You have successfully deleted the news]]</p>{/if}
			{if $message == 'NEWS_SUCCESSFULLY_SAVED'}<p class="message">[[You have successfully saved the news]]</p>{/if}
		{/foreach}
	{/if}
{/if}

<div class="clr"><br/></div>
<form method="post" action="{$GLOBALS.site_url}/manage-news/" name="resultsForm">
	<input type="hidden" name="category_sid" value="{$category.sid}" />
	<input type="hidden" name="action_name" id="action_name" value="">
	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
					{foreach from=$articles item=item name=news_block}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td><input type="checkbox" name="news[{$item.sid}]" value="1" id="checkbox_{$smarty.foreach.news_block.iteration}"></td>
						<td>{$item.sid}</td>
						<td>{$item.title}</td>
						<td>{tr type='date'}{$item.date}{/tr}</td>
						<td>{if empty($item.expiration_date)}[[Never Expire]]{else}{tr type='date'}{$item.expiration_date}{/tr}{/if}</td>

						{if $category.name != 'Archive'}
							<td>{if $item.active == 1}[[Active]]{else}[[Not Active]]{/if}</td>
						{/if}

						<td>{$item.link}</td>
						<td>
							{foreach from=$frontendLanguages item=language}
								{if $language.id == $item.language}{$language.caption}{/if}
							{/foreach}
						</td>
						<td><a href="{$GLOBALS.site_url}/manage-news/?action=edit&article_sid={$item.sid}&category_sid={$category.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
						<td><a href="{$GLOBALS.site_url}/manage-news/?action=delete&news[{$item.sid}]=1&category_sid={$category.sid}" onclick="return confirm('[[Are you sure you want to delete the selected News?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
					</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>
<script>
$('#apply').click(
    function(){
        $('#submit').attr('value', 'apply_category');
    }
);
</script>