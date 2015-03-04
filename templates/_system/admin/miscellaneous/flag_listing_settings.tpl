{breadcrumbs}[[Flag Listing Settings]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperminus32.png" border="0" alt="" class="titleicon"/>[[Flag Listing Settings]]</h1>

<form method="post">
	<input type="hidden" name="action" value="save">
	<fieldset id="form_fieldset">
		<legend>[[Add a New Flag]]</legend>
		<table>
			<thead>
				<tr>
					<th>[[Flag Reason]]</th>
					{foreach from=$listing_types item=type}
						<th>[[{$type.name}]]</th>
					{/foreach}
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tr>
				<td><input type="text" name="new_value" /></td>
				{foreach from=$listing_types item=type}
					<td><input type="checkbox" name="flag_listing_types[]" value="{$type.sid}"></td>
				{/foreach}
				<td colspan="2"><input type="submit" name="save" value="[[Add]]" class="grayButton" /></td>
			</tr>
		</table>
	</fieldset>
</form>
<br />

{foreach item=error from=$errors}
	<p class="error">[[{$error}]]</p>
{/foreach}
<br />

<table>
	<thead>
		<tr>
			<th>[[Flag Reason]]</th>
			<th>[[Listing Types]]</th>
			<th class="actions">[[Actions]]</th>
		</tr>
	</thead>
		{foreach from=$settings item=item key=key}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>[[{$item.value}]]</td>
				<td>
				{* SHOW LIST OF LISTING TYPES FOR THIS FLAG *}
				{foreach from=$item.listing_type_sid item=type name=typesCheck}
					{assign var=listing_type value=$listing_types.$type}
					[[{$listing_type.name}]]{if !$smarty.foreach.typesCheck.last}, {/if}
				{/foreach}
				</td>
				<td>
					<a href="{$GLOBALS.site_url}/flag-listing-settings/?item_sid={$item.sid}&amp;action=edit" title="[[Edit]]" class="editbutton">[[Edit]]</a>&nbsp;
					<a href="{$GLOBALS.site_url}/flag-listing-settings/?item_sid={$item.sid}&amp;action=delete" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
				</td>
			</tr>
		{/foreach}
</table>