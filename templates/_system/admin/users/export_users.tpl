{breadcrumbs}[[Export Users]]{/breadcrumbs}
<h1><img src="{image}/icons/boxupload32.png" border="0" alt="" class="titleicon" />[[Export Users]]</h1>

<form method="post">
	<p>
	<table class="basetable">
		<input type="hidden" name="action" value="export">
		<thead>
			<tr>
				<th colspan="6">[[Export Filter]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="oddrow"><td>[[User ID]]: </td><td colspan="5">{search property="sid"}</td></tr>
			<tr class="evenrow"><td>[[Username]]: </td><td colspan="5">{search property="username"}</td></tr>
			<tr class="oddrow"><td>[[User Group]]:</td><td colspan="5">{search property="user_group" template="list_with_reload_user.tpl"}</td></tr>
			<tr class="evenrow"><td>[[Product]]:</td><td class="product-multilist" colspan="5">{search property="product" template="multilist.tpl"}</td></tr>
			<tr class="oddrow"><td>[[Registration Date]]: </td><td colspan="5">{search property="registration_date"}</td></tr>
			<tr class="evenrow"><td>[[Featured]]: </td><td colspan="5">{search property="featured"}</td></tr>
		</tbody>
		<tr id="clearTable"><td colspan="6">&nbsp;</td></tr>
		<thead>
			<tr>
				<th colspan="6">[[System User Properties to Export]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="oddrow">
				{foreach from=$userSystemProperties.system item=property_id name=system_properties}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property_id}]" value="1" id="system_checkbox_{$smarty.foreach.system_properties.iteration}" /> {$property_id}</td>
					{if $smarty.foreach.system_properties.iteration % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
				{/foreach}
			</tr>
				<tr class="{cycle values="evenrow,oddrow"}"><td colspan="6"><a href="#" onClick="check_all('system', '{$smarty.foreach.system_properties.total}');return false;">[[Select]]</a> / <a href="#" onClick="uncheck_all('system', '{$smarty.foreach.system_properties.total}');return false;">[[Deselect]]</a> [[All]]</td>
			</tr>
		</tbody>
		{foreach from=$userCommonProperties item=properties key=groupName}
		<tr id="clearTable"><td colspan="6">&nbsp;</td></tr>
		<thead>
			<tr>
				<th colspan="6">[[{$groupName} User Properties to Export]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="evenrow">
				{foreach from=$properties item=property name="properties"}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property.id}]" value="1" id="{$groupName}_checkbox_{$smarty.foreach.properties.iteration}" /> [[{$property.caption}]]</td>
					{if $smarty.foreach.properties.iteration % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
				{/foreach}
			</tr>
			<tr>
				<td colspan="6"><a href="#" onClick="check_all('{$groupName}', '{$smarty.foreach.properties.total}');return false;">[[Select]]</a> / <a href="#" onClick="uncheck_all('{$groupName}', '{$smarty.foreach.properties.total}');return false;">[[Deselect]]</a> [[All]]</td>
			</tr>
		</tbody>
		{/foreach}
		<tr id="clearTable">
			<td colspan="6" align="right">
                <div class="clr"><br/></div>
                <div class="floatRight">
                    <input type="submit" value="[[Export]]" class="greenButton" />
                </div>
            </td>
		</tr>
	</table>
</form>

<script >
$(function(){ldelim}
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	
	$("#registration_date_notless, #registration_date_notmore").datepicker({
		dateFormat: dFormat,
		showOn: 'both',
		yearRange: '-99:+99',
		buttonImage: '{image}icons/icon-calendar.png'
	});
	
});

function check_all(group, total)
{ 
	for (i = 1; i <= total; i++) {
		if (checkbox = document.getElementById(group + '_checkbox_' + i))
			checkbox.checked = true;
	}
}

function uncheck_all(group, total)
{
	for (i = 1; i <= total; i++) {
		if (checkbox = document.getElementById(group + '_checkbox_' + i))
			checkbox.checked = false;
	}
}
</script>
