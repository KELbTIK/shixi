{breadcrumbs}
	<a href="{$GLOBALS.site_url}/news-categories/">[[News Categories]]</a>  &#187; <a href="{$GLOBALS.site_url}/news-categories/?action=edit&category_sid={$category.sid}">{$category.name}</a> &#187; <a href="{$GLOBALS.site_url}/news-categories/?action=edit&category_sid={$category.sid}">[[Edit Category]]</a> &#187; [[Add News]]
{/breadcrumbs}

<h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>[[Add News]]</h1>

{include file='../classifieds/field_errors.tpl'}
<fieldset>
	<legend>[[Add News]]</legend>
		<form method="post" action="" enctype="multipart/form-data">
			<input type="hidden" name="category_id" value="{$category_id}" />
			{if $article_sid}<input type="hidden" name="sid" value="{$article_sid}" />{/if}
			<input type="hidden" name="action" value="add" />
			<table>
				{foreach from=$form_fields item=form_field}
					<tr>
						<td width="20%;">[[{$form_field.caption}]]</td>
						<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
						<td>{input property=$form_field.id}</td>
					</tr>
				{/foreach}
				<tr>
					<td colspan="3" align="right"><div class="floatRight"><input type="submit" name="form_submit" value="[[Add]]" class="grayButton"/></div></td>
				</tr>
			</table>
		</form>
</fieldset>

