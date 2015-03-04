{breadcrumbs}[[Export Listings]]{/breadcrumbs}
<h1><img src="{image}/icons/boxupload32.png" border="0" alt="" class="titleicon" /> [[Export Listings]]</h1>

<form method="post">
	<table class="basetable">
		<input type="hidden" name="action" value="export" />
		<thead>
			<tr>
				<th colspan="6">[[Export Filter]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="oddrow"><td>[[Listing ID]]: </td><td colspan="5">{search property="sid"}</td></tr>
			<tr class="evenrow"><td>[[Listing Type]]: </td><td colspan="5">{search property="listing_type" template="list_with_reload.tpl"}</td></tr>
			<tr class="oddrow"><td>[[Activation Date]]:</td><td colspan="5">{search property="activation_date"}</td></tr>
			<tr class="evenrow"><td>[[Expiration Date]]:</td>	<td colspan="5">{search property="expiration_date"}</td></tr>
			<tr class="oddrow"><td>[[Username]]: </td><td colspan="5">{search property="username"}</td></tr>
			<tr class="evenrow"><td>[[Featured]]: </td><td colspan="5">{search property="featured"}</td></tr>
		</tbody>
		<tr id="clearTable"><td colspan="6">&nbsp;</td></tr>
		<thead>
			<tr>
				<th colspan="6">[[System Listing Properties To Export]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="oddrow">
				{foreach from=$properties_id.system item=property_id name=system_properties}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property_id}]" value="1" id="system_checkbox_{$smarty.foreach.system_properties.iteration}" /> {$property_id}</td>
					{if $smarty.foreach.system_properties.iteration % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
				{/foreach}
			</tr>
				<tr class="{cycle values="evenrow,oddrow"}"><td colspan="6"><a href="#" onClick="check_all_system();return false;">[[Select]]</a> / <a href="#" onClick="uncheck_all_system();return false;">[[Deselect]]</a> [[All]]</td>
			</tr>
		</tbody>
		<tr id="clearTable"><td colspan="6">&nbsp;</td></tr>
		<thead>
			<tr>
				<th colspan="6">[[Common Listing Properties To Export]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="evenrow">
				{foreach from=$properties_id.common item=property name=common_properties}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property.id}]" value="1" id="common_checkbox_{$smarty.foreach.common_properties.iteration}" /> [[{$property.caption}]]</td>
					{if $smarty.foreach.common_properties.iteration % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
				{/foreach}
			</tr>
			<tr class="{cycle values="evenrow,oddrow"}">
				<td colspan="6"><a href="#" onClick="check_all_common();return false;">[[Select]]</a> / <a href="#" onClick="uncheck_all_common();return false;">[[Deselect]]</a> [[All]]</td>
			</tr>
			{if count($properties_id.extra) > 0}
			<tr id="clearTable"><td colspan="6">&nbsp;</td></tr>
			<tr class="headrow">
				<td colspan="6">[[Extra Listing Properties To Export]]</td>
			</tr>
			<tr class="oddrow">
				{foreach from=$properties_id.extra item=property name=extra_properties}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property.id}]" value="1" id="extra_checkbox_{$smarty.foreach.extra_properties.iteration}" /> [[{$property.caption}]]</td>
					{if $smarty.foreach.extra_properties.iteration % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
				{/foreach}
			</tr>
			<tr>
				<td colspan="6"><a href="#" onClick="check_all_extra();return false;">[[Select]]</a> / <a href="#" onClick="uncheck_all_extra();return false;">[[Deselect]]</a> [[All]]</td>
			</tr>
			{/if}
			<tr id="clearTable">
				<td colspan="6">
                    <div class="clr"><br/></div>
                    <div class="floatRight"><input type="submit" value="[[Export]]" class="grayButton" /></div>
                </td>
			</tr>
		</tbody>
	</table>
</form>
<br/>
<script language="Javascript">
	$(function() {

		var dFormat = '{$GLOBALS.current_language_data.date_format}';
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");

		$("#activation_date_notless, #activation_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});
		$("#expiration_date_notless, #expiration_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});

	});


	var system_total={$smarty.foreach.system_properties.total};
	var common_total={$smarty.foreach.common_properties.total};
	var extra_total={if $smarty.foreach.extra_properties.total}{$smarty.foreach.extra_properties.total}{else}0{/if};

	function check_all_system() 	{ set_checkbox_to(true, 'system', system_total); }
	function uncheck_all_system() 	{ set_checkbox_to(false, 'system', system_total); }

	function check_all_common() 	{ set_checkbox_to(true, 'common', common_total); }
	function uncheck_all_common() 	{ set_checkbox_to(false, 'common', common_total); }

	function check_all_extra() 		{ set_checkbox_to(true, 'extra', extra_total); }
	function uncheck_all_extra() 	{ set_checkbox_to(false, 'extra', extra_total); }

	function set_checkbox_to(flag, type, total)
	{
		for (i = 1; i <= total; i++)
			if (checkbox = document.getElementById(type + '_checkbox_' + i))
				checkbox.checked = flag;
	}


</script>
