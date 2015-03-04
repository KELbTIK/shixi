{breadcrumbs}[[Manage Languages]]{/breadcrumbs}
<h1><img src="{image}/icons/exchange32.png" border="0" alt="" class="titleicon"/>[[Manage Languages]]</h1>

{if $errors}
	{include file="errors.tpl" errors=$errors}
{/if}

<p><a href="{$GLOBALS.site_url}/add-language/" class="grayButton">[[Add a New Language]]</a></p>

<table>
	<thead>
		<tr>
			<th>[[Language Caption]]</th>
			<th>[[Active for Frontend]]</th>
			<th>[[Active for Backend]]</th>
			<th colspan=4 class="actions">[[Actions]]</th>
		</tr>
	</thead>
	{foreach from=$langs item=lang}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>{$lang.caption}</td>
			<td>{if $lang.activeFrontend}[[Yes]]{else}[[No]]{/if}</td>
			<td>{if $lang.activeBackend}[[Yes]]{else}[[No]]{/if}</td>
			<td><a href="{$GLOBALS.site_url}/edit-language/?languageId={$lang.id}" title="Edit" class="editbutton">[[Edit]]</a></td>
			<td>
				{if !$lang.is_default}
					<a href="{$GLOBALS.site_url}/manage-languages/?languageId={$lang.id}&action=delete_language" onclick='return confirm("[[Do you want to delete {$lang.caption} language?]]")' title="[[Delete]]" class="deletebutton">[[Delete]]</a>
				{/if}
			</td>
			<td><a href="{$GLOBALS.site_url}/manage-phrases/?language={$lang.id}&action=search_phrases" class="grayButton">[[Translate Phrases]]</a></td>
			<td>
				{if $lang.is_default}
					<b>[[Default]]</b>
				{elseif $lang.activeFrontend && $lang.activeBackend}
					<a href="{$GLOBALS.site_url}/manage-languages/?languageId={$lang.id}&action=set_default_language" class="grayButton">[[Make Default]]</a>
				{/if}
			</td>
		</tr>
	{/foreach}
</table>