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

{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$type_sid}">[[Edit User Group]] </a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$type_sid}">[[Edit User Profile Fields]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile-field/?sid={$field_info.sid}&amp;user_group_sid={$type_sid}">[[{$field_info.caption}]]</a> &#187; [[Edit List]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" class="titleicon" /> [[Edit List]]</h1>

{if $error eq 'LIST_VALUE_IS_EMPTY'}
	<p class="error">[['Value' is empty]]</p>
{elseif $error eq 'LIST_VALUE_ALREADY_EXISTS'}
	<p class="error">[[This value is already used]]</p>
{/if}

<fieldset>
	<legend>[[Add a New List Value]]</legend>
	<table>
		<form method="post" action="" id="add_values">
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="field_sid" value="{$field_sid}" />
			<tr>
				<td>[[Value]] </td>
				<td><input type="text" name="list_item_value" /><textarea name="list_multiItem_value" style="display:none"></textarea> <span class="required">*</span></td>
				<td>
					<input type="submit" value="[[Add]]" class="grayButton">
					<input type="button" value="[[Add multiple values]]" class="grayButton" onClick="windowMessage();"/>
				</td>
			</tr>
		</form>
	</table>
</fieldset>
<div class="clr"><br/></div>
<form method="post" action="" name="list_items_form" id="list_items_form">
	<input type="hidden" name="action" id="action_name" value="" />
	<input type="hidden" name="field_sid" value="{$field_sid}" />
	{if $list_items}
		<input type="button" id="list_order_button" value="[[Save Order]]" class="grayButton" onclick="submitForm('save_order')" />
		<input type="button" id="list_delete_button" value="[[Delete]]" class="deletebutton" onclick="if ( confirm('[[Are you sure to delete selected item(s)?]]') ) submitForm('delete');" />
	{/if}
	<div class="clr"><br/></div>

	<table id="list_table">
		<thead>
			<tr>
				<th style="width:1px"><input type="checkbox" id="all_checkboxes_control" /></th>
				<th>[[List Values]]</th>
				<th colspan="4" class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$list_items item=list_value key=sid name=items_block}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>
					<input type="checkbox" name="item_sid[{$sid}]" value="1" id="checkbox_{$smarty.foreach.items_block.iteration}" />
					<input type="hidden" name="item_order[{$sid}]" value="1" />
				</td>
				<td class="dragHandle">{$list_value}</td>
				<td><a href="{$GLOBALS.site_url}/edit-user-profile-field/edit-list-item/?field_sid={$field_sid}&amp;item_sid={$sid}" title="[[Edit]]" class="editbutton">[[Edit]]</td>
				<td><a href="?field_sid={$field_sid}&amp;action=delete&amp;item_sid={$sid}" onclick="return confirm('[[Are you sure you want to delete this?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</td>
				<td>
					{if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}
						<a href="?field_sid={$field_sid}&amp;item_sid={$sid}&amp;action=move_down"><img src="{image}b_down_arrow.gif" border="0" /></a>
					{/if}
				</td>
				<td>
					{if $smarty.foreach.items_block.iteration > 1}
						<a href="?field_sid={$field_sid}&amp;item_sid={$sid}&amp;action=move_up"><img src="{image}b_up_arrow.gif" border="0" /></a>
					{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</form>
<div id="messageBox"></div>

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