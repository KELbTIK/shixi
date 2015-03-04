{breadcrumbs}[[Static Content]]{/breadcrumbs}
<h1><img src="{image}/icons/note32.png" border="0" alt="" class="titleicon" />[[Static Content]]</h1>

{if $error}
	<p class="error">[[{$error}]]</p>
{/if}

<form method="post" onsubmit="disableSubmitButton('submitAdd');" >
	<fieldset>
		<legend>[[Add a New Static Content]]</legend>
		<input type= "hidden" name= "action" value= "add" />
		<table>
			<tr>
				<td>[[ID]]</td>
				<td><input type="text" name="page_id" value="" /></td>
			</tr>
			<tr>
				<td>[[Name]]</td>
				<td><input type="text" name="name" value="" onFocus="JavaScript: if (!this.value) this.value=pageid.value;" /></td>
			</tr>
			<tr>
				<td>[[Language]]</td>
				<td>
					<select name="lang">
						{foreach from=$languages item=language}
							<option value="{$language.id}"{if $language.id == $language.is_default} selected="selected"{/if}>{$language.caption}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="floatRight">
						<input type="submit" value="[[Add]]" class="greenButton" id="submitAdd" />
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
</form>

<div class="clr"><br/></div>

<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Name]]</th>
			<th>[[Language]]</th>
			<th colspan="2" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$pages item=page key=sid name=foreach}
			<tr class={cycle values="'oddrow', 'evenrow'"}>
				<td>{$page.id|escape:"htmlall"}</td>
				<td>{$page.name|escape:"htmlall"}&nbsp;</td>
				<td>
					{foreach from=$languages item=language}
						{if $language.id == $page.lang}{$language.caption}{/if}
					{/foreach}
				</td>
				<td><a href="?action=edit&amp;page_sid={$sid}&amp;pageid={$page.id}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				{if ! $page.isDefault}
					<td><a href="?action=delete&amp;page_sid={$sid}" onclick="return confirm('[[Are you sure you want to delete this Static Content]]?')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
				{/if}
			</tr>
		{/foreach}
	</tbody>
</table>