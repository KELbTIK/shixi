{breadcrumbs}<a href="{$GLOBALS.site_url}/flag-listing-settings/">[[Flag Listing Settings]]</a> &#187; [[Edit Flag]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperminus32.png" border="0" alt="" class="titleicon"/>[[Edit Flag]]</h1>

<form method="post">
	<input type="hidden" name="item_sid" value="{$current_setting.sid}">
	<input type="hidden" name="action" value="save">
    <input type="hidden" id="submit" name="submit" value="save">
	<fieldset id="form_fieldset"><legend>[[Edit Flag]]</legend>
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
			<td><input type="text" name="new_value" value="{$current_setting.value}"></td>
			{foreach from=$listing_types item=type}
				<td><input type="checkbox" name="flag_listing_types[]" value="{$type.sid}" {if in_array($type.sid, $current_setting.listing_type_sid)} checked="checked"{/if}></td>
			{/foreach}
			<td colspan="2">
                <div class="floatRight">
                    <input type="submit" id="apply" value="[[Apply]]" class="grayButton"/>
                    <input type="submit" value="[[Save]]" class="grayButton" />
                </div>
            </td>
		</tr>
	</table>
	
	</fieldset>
	
</form>

<script>
	$('#apply').click(
		function(){
			$('#submit').attr('value', 'apply');
		}
	);
</script>