{breadcrumbs}
	<a href="{$GLOBALS.site_url}/news-categories/">[[News Categories]]</a>
	&#187; <a href="{$GLOBALS.site_url}/news-categories/?action=edit&amp;category_sid={$category.sid}">[[{$category.name}]]</a>
	&#187; <a href="{$GLOBALS.site_url}/news-categories/?action=edit&amp;category_sid={$category.sid}">[[Edit Category]]</a>
	&#187; [[Edit Article]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Article]]</h1>
{include file='../classifieds/field_errors.tpl'}
<fieldset>
	<legend>&nbsp;[[Edit Article]]</legend>
	<form method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="category_id" value="{$category.sid}" />
		<input type="hidden" name="article_sid" value="{$article_sid}" />
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" id="submit" name="form_submit" value="save_article"/>
		<table>
			{foreach from=$form_fields item=form_field}
				{if $form_field.id == 'category_id'}
				<tr>
					<td valign="top" width="20%">[[Move To Category]]:</td>
					<td valign="top">&nbsp;{if $form_field.is_required} <span class="required">*</span>{/if}</td>
					<td valign="top">
						<select name="article_category">
							<option value="">[[Select Category]]</option>
							{foreach from=$all_categories item=current_category}
								{if $current_category.sid != $category.sid}
									<option value="{$current_category.sid}">[[{$current_category.name}]]</option>
								{/if}
							{/foreach}
						</select>
					</td>
				</tr>
				{elseif $form_field.id == 'image'}
					<tr>
						<td valign="top" width="20%">[[{$form_field.caption}]]</td>
						<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
						<td valign="top">{input property=$form_field.id template="picture_news.tpl"}</td>
					</tr>
				{else}
					<tr>
						<td valign="top" width="20%">[[{$form_field.caption}]]</td>
						<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
						<td valign="top">{input property=$form_field.id}</td>
					</tr>
				{/if}
			{/foreach}
			<tr>
				<td colspan="3" align="right"><div class="floatRight"><input type="submit" name="form_submit" value="[[Update]]" class="greenButton"/></div></td>
			</tr>
		</table>
	</form>
</fieldset>