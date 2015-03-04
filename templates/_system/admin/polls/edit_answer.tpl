<script type="text/javascript">
	function windowMessage(){
		$("#messageBox").dialog( 'destroy' ).html('<textarea name="list" id="list"></textarea>');
		$("#messageBox").dialog({
			width: 500,
			height: 500,
			title: '[[Add multiple values]]',
				buttons: {
				Add: function() {
					document.getElementById('event').value = "add_multiple";
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
{capture name="areYouSure"}[[Are you sure?]]{/capture}
<div id="messageBox"></div>

{if $type_sid}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/manage-polls/">[[Manage Polls]]</a>
		&#187; <a href="{$GLOBALS.site_url}/manage-polls/?action_name=edit&sid={$field_info.sid}">[[Poll]]#{$field_info.sid}</a>
		&#187; <a href="{$GLOBALS.site_url}/edit_listing_type_field/?sid={$field_info.sid}">[[$field_info.caption]]</a>
		&#187; [[Edit List]]
	{/breadcrumbs}
{else}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/manage-polls/">[[Manage Polls]]</a>
		&#187; <a href="{$GLOBALS.site_url}/manage-polls/?action_name=edit&sid={$field_info.sid}">[[Poll]]#{$field_info.sid}</a>
		&#187; [[Edit Answers]]
	{/breadcrumbs}
{/if}
<h1><img src="{image}/icons/bargraph32.png" border="0" alt="" class="titleicon"/>[[Edit Answers]]</h1>

{if $error eq 'LIST_VALUE_IS_EMPTY'}
	<p class="error">[['Value' is empty]]</p>
{elseif $error eq 'LIST_VALUE_ALREADY_EXISTS'}
	<p class="error">[[This value is already used]]</p>
{/if}

{if $item_action == 'edit'}
	<fieldset>
		<legend>[[Edit Answer]]</legend>
		<form method="post" action="{$GLOBALS.site_url}/manage-polls/" id="add_values">
			<input type="hidden" name="action_name" value="edit_answer" />
			<input type="hidden" name="event" id='event' value="add" />
			<input type="hidden" name="sid" value="{$field_info.sid}" />
			<input type="hidden" name="item_sid" value="{$item_sid}" />
			<table>
				<tr>
					<td>[[Value]] </td>
					<td>
						<input name="list_item_value" class="textField" value="{$item_value}" /> <span class="required">*</span>
					</td>
					<td><input type="submit" value="[[Save]]" class="grayButton" /></td>
				</tr>
			</table>
		</form>
	</fieldset>
{else}
	<fieldset>
		<legend>[[Add a New Answer]]</legend>
		<form method="post" action="{$GLOBALS.site_url}/manage-polls/" id="add_values">
			<input type="hidden" name="action_name" value="edit_answer" />
			<input type="hidden" name="event" id='event' value="add" />
			<input type="hidden" name="sid" value="{$field_info.sid}" />
			<table>
				<tr>
					<td>[[Value]] </td>
					<td>
						<input name="list_item_value" class="textField" /> <span class="required">*</span>
						<textarea name="list_multiItem_value" style="display:none"></textarea><br/>
					</td>
					<td>
						<input type="submit" value="[[Add]]" class="grayButton" />
						<input type="button" value="[[Add multiple values]]" class="grayButton" onClick="windowMessage();"/>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
{/if}

<form method="post" action="" name="list_items_form" id="list_items_form">
	<input type="hidden" name="action_name" value="edit_answer" />
	<input type="hidden" name="event" id="action_name" value="" />
	<input type="hidden" name="sid" value="{$field_info.sid}">
	<div class="clr"><br/></div>
	{if !empty($list_items)}
		<input type="button" id="list_order_button" value="[[Save Order]]" class="grayButton" onclick="submitForm('save_order')">
		<input type="button" id="list_delete_button" value="[[Delete]]" class="deletebutton" onclick="if ( confirm('{$smarty.capture.areYouSure|escape:"javascript"}') ) submitForm('delete');">
	{/if}

	<br /><br />
	<table class="basetable" id="list_table">
		<thead class="headrow nodrag nodrop">
			<th><input type="checkbox" id="all_checkboxes_control"></th>
			<th>
				<a href="?action_name=edit_answer&event=edit&sid={$field_info.sid}&event=sort&field_sid={$field_sid}&sorting_field=value&sorting_order={if $sorting_order == 'ASC'}DESC{else}ASC{/if}">[[List Values]]</a>
				{if $sorting_order == 'ASC'}<img src="{image}b_down_arrow.gif">{else}<img src="{image}b_up_arrow.gif">{/if}
			</th>
			<th colspan="4" class="actions">[[Actions]]</th>
		</thead>
		{foreach from=$list_items item=list_value key=sid name=items_block}
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}" onmouseover="this.className='highlightrow'" onmouseout="this.className='{cycle values = 'evenrow,oddrow'}'">
				<td>
					<input type="checkbox" name="item_sid[{$sid}]" value="1" id="checkbox_{$smarty.foreach.items_block.iteration}">
					<input type="hidden" name="item_order[{$sid}]" value="1">
				</td>
				<td class="dragHandle">[[$list_value]]</td>
				<td><a href="?action_name=edit_answer&event=edit&sid={$field_info.sid}&amp;item_sid={$sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				<td><a href="?action_name=edit_answer&event=delete&sid={$field_info.sid}&amp;item_sid={$sid}" onclick="return confirm('{$smarty.capture.areYouSure|escape:"javascript"}')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
				<td>
					{if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}
						<a href="?action_name=edit_answer&event=move_down&sid={$field_info.sid}&amp;item_sid={$sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
					{/if}
				</td>
				<td>
					{if $smarty.foreach.items_block.iteration > 1}
						<a href="?action_name=edit_answer&event=move_up&sid={$field_info.sid}&amp;item_sid={$sid}"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
					{/if}
				</td>
			</tr>
		{/foreach}
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