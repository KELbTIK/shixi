{breadcrumbs}[[News Categories]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/>[[News Categories]]</h1>
{include file='../classifieds/field_errors.tpl'}
{if $messages}
	{foreach from=$messages item=message key=number}
		{if $message == 'NEWS_CATEGORY_SUCCESSFULLY_ADDED'}<p class="message">[[You have successfully added the news category]]</p>{/if}
		{if $message == 'NEWS_SETTINGS_SUCCESSFULLY_SAVED'}<p class="message">[[Your changes were successfully saved]]</p>{/if}
		{if $message == 'NEWS_CATEGORY_SUCCESSFULLY_DELETED'}<p class="message">[[You have successfully deleted the news category]]</p>{/if}
	{/foreach}
{/if}
<p><a href="{$GLOBALS.site_url}/news-categories/?action=edit&category_sid={$archive_category.sid}" class="grayButton">[[View Archive]]</a></p>
<form action="{$GLOBALS.site_url}/news-categories/">
	<input type="hidden" name="action" value="save_display_setting" />
	<table>
        <thead>
            <tr>
                <td colspan="2">
                    <b>[[News Display]]</b>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr class="{cycle values = 'evenrow,oddrow'} id="clearTable">
				<td>[[Display News Block]]</td>
				<td align=center>
					<select name="settings[show_news_on_main_page]">
						<option value="0"{if $show_news_on_main_page == 0} selected="selected"{/if}>[[disable]]</option>
						<option value="1"{if $show_news_on_main_page == 1} selected="selected"{/if}>[[enable]]</option>
					</select>
				</td>
            </tr>
            <tr class="{cycle values = 'evenrow,oddrow'} id="clearTable">
                <td>[[Number of News to Display on Homepage]]</td>
                <td><input type="text" name="settings[number_news_on_main_page]" value="{$number_news_on_main_page}" /></td>
            </tr>
            <tr class="{cycle values = 'evenrow,oddrow'}">
                <td>[[Display Mode]]</td>
                <td>
                    <select name="settings[main_page_news_display_mode]">
                        <option {if $main_page_news_display_mode == 'rotation'}selected="selected"{/if} value="rotation">[[rotation]]</option>
                        <option {if $main_page_news_display_mode == 'latest'}selected="selected"{/if} value="latest">[[latest news]]</option>
                    </select>
                </td>
            </tr>
            <tr class="{cycle values = 'evenrow,oddrow'}">
                <td colspan="2" style="text-align: right;">
                    <span class="greenButtonEnd"><input type="submit" name="news_switch" value="[[Apply]]" class="greenButton" /></span>
                </td>
            </tr>
        </tbody>
	</table>
</form>

<div class="clr"><br /></div>

<form action="{$GLOBALS.site_url}/news-categories/" method=post>
	<fieldset>
		<legend>[[Add a New Category]]</legend>
		<input type= "hidden" name="action" value= "add">
		<table class="fieldset">
			<tr class="">
				<td>[[Category Name]]</td>
				<td><input type="text" name="category_name" value=""> <input type="submit" value="[[Add]]" class="grayButton" /></td>
			</tr>
		</table>
	</fieldset>
</form>

<div class="clr"><br /></div>

<form method="post" action="{$GLOBALS.site_url}/news-categories/" name="resultsForm">
	<input type="hidden" name="action" id="action" value="">
	<table>
		<thead>
			<tr>
				<th>[[Category Name]]</th>
				<th>[[Number of News]]</th>
				<th colspan="2" class="actions">[[Actions]]</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbodY>
			{foreach from=$categories item=item name=categories}
				{if $item.name != 'archive' && $item.name != 'Archive'}
				<tr class="{cycle values = 'evenrow,oddrow' advance=false}" onmouseover="this.className='highlightrow'" onmouseout="this.className='{cycle values = 'evenrow,oddrow'}'">
					<td>[[{$item.name}]]</td>
					<td>{$item.count}</td>
					<td><a href="{$GLOBALS.site_url}/news-categories/?action=edit&category_sid={$item.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
					<td><a href="{$GLOBALS.site_url}/news-categories/?action=delete&category_sid={$item.sid}" onclick="return confirm('[[All News of this Category will be deleted as well. Delete this Category?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
					<td>
						{if $smarty.foreach.categories.iteration < $smarty.foreach.categories.total}
							<a href="?category_sid={$item.sid}&amp;action=move_down"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
						{/if} 
					</td>
					<td>
						{if $smarty.foreach.categories.iteration > 1}
							<a href="?category_sid={$item.sid}&amp;action=move_up"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
						{/if} 
					</td>
				</tr>
				{/if}
			{/foreach}
		</tbodY>
	</table>
</form>

<script>
	function submitForm(action) {
		document.getElementById('action').value = action;
		var form = document.resultsForm;
		form.submit();
	}
</script>