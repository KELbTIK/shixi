{if $type_sid}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">[[{$type_info.name}]]</a>  &#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">{$field_info.caption|escape:"html"}</a> &#187; [[Edit List]]{/breadcrumbs}
{else}
	{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-fields/">[[Listing Fields]]</a>  &#187; <a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$field_sid}">[[{$field_info.caption|escape:"html"}]]</a> &#187; [[Edit List]]{/breadcrumbs}
{/if}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" class="titleicon" /> [[Edit List]]</h1>

<script type="text/javascript">
	function windowMessage(){
		$("#messageBox").dialog( 'destroy' ).html('<textarea name="list" id="list"></textarea>');
		$("#messageBox").dialog({
			width: 500,
			height: 500,
			title: '[[Add multiple values]]',
				buttons: {
				Add: function() {
					document.getElementById('add_values').action.value = "add_multiple";
					document.getElementById('add_values').list_multiItem_value.value = document.getElementById('list').value;
					document.getElementById('add_values').submit();
				},
				Cancel: function(){
					$(this).dialog('close');
				}
			}
		}).dialog( 'open' );
		return false;
	}
</script>

{if $error eq 'LIST_VALUE_IS_EMPTY'}
	<p class="error">[['Value' is empty]]</p>
{elseif $error eq 'LIST_VALUE_ALREADY_EXISTS'}
	<p class="error">[[This value is already used]]</p>
{/if}

<fieldset>
	<legend>[[Add a New List Value]]</legend>
	<form method="post" action="" id="add_values">
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="field_sid" value="{$field_sid}" />
		<table>
			<tr>
				<td>[[Value]]</td>
				<td>
					<input name="list_item_value" class="textField" /> <span class="required">*</span>
					<textarea name="list_multiItem_value" style="display:none"></textarea><br/>
				</td>
				<td>
					<div class="floatRight">
						<input type="submit" value="[[Add]]" class="grayButton" />
						<input type="button" value="[[Add multiple values]]" class="grayButton" onClick="windowMessage();"/>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<form method="post" action="" name="list_items_form" id="list_items_form">
	<input type="hidden" name="action" id="action_name" value="" />
	<input type="hidden" name="field_sid" value="{$field_sid}" />
	<div class="clr"><br/></div>
	{if $list_items}
		<input type="button" id="list_order_button" value="[[Save Order]]" class="grayButton" onclick="submitForm('save_order')">
		<input type="button" id="list_delete_button" value="[[Delete]]" class="deletebutton" onclick="if ( confirm('[[Are you sure to delete selected item(s)?]]') ) submitForm('delete');">
	{/if}
	<div class="clr"><br/></div>
	<table id="list_table">
		<thead class="headrow nodrag nodrop">
			<th><input type="checkbox" id="all_checkboxes_control" /></th>
			<th>
				<a href="?&action=sort&field_sid={$field_sid}&sorting_field=value&sorting_order={if $sorting_order == 'ASC'}DESC{else}ASC{/if}">[[List Values]]</a>
				{if $sorting_order == 'ASC'}<img src="{image}b_down_arrow.gif">{else}<img src="{image}b_up_arrow.gif">{/if}
			</th>
			<th colspan="4" class="actions">[[Actions]]</th>
		</thead>
		<tbody>
			{foreach from=$list_items item=list_value key=sid name=items_block}
			<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
				<td>
					<input type="checkbox" name="item_sid[{$sid}]" value="1" id="checkbox_{$smarty.foreach.items_block.iteration}" />
					<input type="hidden" name="item_order[{$sid}]" value="1" />
				</td>
				<td class="dragHandle">{$list_value|escape:"html"}</td>
				<td><a href="{$GLOBALS.site_url}/edit-listing-field/edit-list-item/?field_sid={$field_sid}&amp;item_sid={$sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				<td><a href="?field_sid={$field_sid}&amp;action=delete&amp;item_sid={$sid}" onclick="return confirm('[[Are you sure?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
				<td>
					{if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}
						<a href="?field_sid={$field_sid}&amp;item_sid={$sid}&amp;action=move_down"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
					{/if} 
				</td>
				<td>
					{if $smarty.foreach.items_block.iteration > 1}
						<a href="?field_sid={$field_sid}&amp;item_sid={$sid}&amp;action=move_up"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
					{/if} 
				</td>
			</tr>
			{/foreach}
		</tbody>
</table>
</form>

<script type="text/javascript" src="{$GLOBALS.site_url}/../system/ext/jquery/jquery.tablednd.js"></script>
<script type="text/javascript">
	$( function() {
		var total={$smarty.foreach.items_block.total};

		function set_checkbox(param) {
			for (i = 1; i <= total; i++) {
				if (checkbox = document.getElementById('checkbox_' + i))
					checkbox.checked = param;
			}
		}

		$("#all_checkboxes_control").click(function() {
			if ( this.checked == false)
				set_checkbox(false);
			else
				set_checkbox(true);
		});

		// Drag'n'Drop table
		$("#list_table").tableDnD({
			onDragClass: "myDragClass",
			dragHandle: "dragHandle"
		});
	});

	function submitForm(action) {
		document.getElementById('action_name').value = action;
		var form = document.list_items_form;
		form.submit();
	}
</script>