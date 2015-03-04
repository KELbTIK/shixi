{if $complex}
	{if $listing_type_sid}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$listing_type_sid}">[[$listing_type_info.name]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$listing_field_info.sid}">[[$listing_field_info.caption]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?field_sid={$listing_field_info.sid}">[[Edit Fields]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?sid={$field_sid}&field_sid={$listing_field_info.sid}&action=edit">[[$field_info.caption]]</a>
			&#187; [[Edit Tree]]
		{/breadcrumbs}
	{else}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-fields/">[[Listing Fields]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$listing_field_info.sid}">[[$listing_field_info.caption]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?field_sid={$listing_field_info.sid}">[[Edit Fields]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?sid={$field_sid}&field_sid={$listing_field_info.sid}&action=edit">[[$field_info.caption]]</a>
			&#187; [[Edit Tree]]
		 {/breadcrumbs}
	{/if}
{else}
	{if $type_sid}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">[[$type_info.name]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">[[$field_info.caption]]</a>
			&#187; [[Edit Tree]]
		{/breadcrumbs}
	{else}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-fields/">[[Common Fields]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$field_sid}">[[$field_info.caption]]</a>
			&#187; [[Edit Tree]]
		{/breadcrumbs}
	{/if}
{/if}

<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon" /> [[Edit Tree]]</h1>

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
					document.getElementById('add_values').tree_multiItem_value.value = document.getElementById('list').value;
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

{foreach from=$field_errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{/if}
{/foreach}

{if $node_info.sid}
	<fieldset class="inlineField">
		{assign var=previous_level value=$current_level-1}
		<legend>&nbsp;[[Edit]] {if $field_info.levels_captions.$previous_level ne ''}{$field_info.levels_captions.$previous_level}{else}[[Tree Value]]{/if}</legend>
		<form method="post" action="">
			<input type="hidden" name="action" value="[[save]]"/>
			<input type="hidden" name="field_sid" value="{$field_sid}"/>
			<input type="hidden" name="node_sid" value="{$node_sid}"/>
			<table>
				<tr>
					<td>[[Value]] </td>
					<td><input type="text" name="tree_item_value" value="{$node_info.caption|escape}"/> <span class="required">*</span></td>
				</tr>
				<tr>
				<td rowspan="3" valign="top">[[Move]]</td>
					<td><input type="radio" name="order" value="begin" id="move_order_begin"><label for="move_order_begin">[[to the beginning]]</label></td>
				</tr>
				<tr>
					<td><input type="radio" name="order" value="end" id="move_order_end"><label for="move_order_end">[[to the end]]</label></td>
				</tr>
				<tr>
					<td>
						<nobr><input type="radio" name="order" value="after" id="move_order_after"><label for="move_order_after">[[after]]</label>
							<select name="after_tree_item_sid" onclick="document.getElementById('move_order_after').checked = true">{foreach from=$tree_parent_items item=tree_value key=sid}
							   {if $sid != $node_sid}
							   <option value="{$sid}">{$tree_value}</option>
							   {/if}
							   {/foreach}
							</select>
						</nobr>
					</td>
				</tr>	
				<tr>
					<td colspan="2">
						<div class="floatRight"><input type="submit" value="[[Save]]" class="grayButton" /></div>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
{/if}

{if $node_info.node_path|count < 5}
	<fieldset  class="inlineField">
		<legend>[[Add a New {if $field_info.levels_captions.$current_level ne ''}{$field_info.levels_captions.$current_level}{else}Tree Value{/if}]]</legend>
		<form method="post" action="" id="add_values">
			<input type="hidden" name="action" value="add"/>
			<input type="hidden" name="field_sid" value="{$field_sid}"/>
			<input type="hidden" name="node_sid" value="{$node_sid}"/>
			<table>
				<tr>
					<td>[[Value]] </td>
					<td><input type="text" name="tree_item_value"/> <span class="required">*</span></td>
					<textarea name="tree_multiItem_value" style="display:none"></textarea>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="radio" name="order" value="begin" id="order_begin"><label for="order_begin">[[to the beginning]]</label></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="radio" name="order" value="end" checked id="order_end"><label for="order_end">[[to the end]]</label></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="radio" name="order" value="after" id="order_after"><label for="order_after">[[after]]</label>
						<select name="after_tree_item_sid" onclick="document.getElementById('order_after').checked = true">{foreach from=$tree_items item=tree_value key=sid}
						   <option value="{$sid}">{$tree_value}</option> {/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="floatRight">
							<input type="submit" value="[[Add]]" class="grayButton" />
							<input type="button" value="[[Add multiple values]]" class="grayButton" onClick="windowMessage();" />
						</div>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
{/if}

<div class="clr"></div>
{if $node_info.node_path|count < 5}
	<p><a href="{$GLOBALS.site_url}/import-tree-data/?field_sid={$field_sid}" class="grayButton">[[Import data from file]]</a></p>
{/if}
<p>
    <strong>
        {foreach from=$node_info.node_path item=node key=level name=node_path_block}
            {if $smarty.foreach.node_path_block.iteration > 1} / {/if}
            {if $smarty.foreach.node_path_block.iteration < $smarty.foreach.node_path_block.total}
                <a href="?field_sid={$field_sid}&amp;node_sid={$node.sid}">{$node.caption}</a>
            {else}
                {$node.caption}
            {/if}
        {/foreach}
    </strong>
</p>

{if $node_info.node_path|count < 5}
	<form method="post" action="" name="tree_items_form" id="tree_items_form">
		<input type="hidden" name="action" id="action_name" value="">
		<input type="hidden" name="field_sid" value="{$field_sid}">
		<input type="hidden" name="node_sid" value="{$node_sid}">
		<div class="clr"></div>
		{if $tree_items}
			<input type="button" id="list_order_button" value="[[Save Order]]" class="grayButton" onclick="submitForm('save_order');" />
			<input type="button" id="list_delete_button" value="[[Delete]]" class="deletebutton" onclick="if (confirm('[[Are you sure to delete selected item(s)?]]')) submitForm('delete');" />
		{/if}
		<div class="clr"><br/></div>
		<table id="tree_table">
			<thead>
				<tr>
					<th><input type="checkbox" id="all_checkboxes_control"></th>
					<th>
						<a href="?&action=sort&field_sid={$field_sid}&node_sid={$node_sid}&sorting_order={if $sorting_order == 'ASC'}DESC{else}ASC{/if}">[[Tree Values]]</a>
						{if $sorting_order == 'ASC'}<img src="{image}b_down_arrow.gif">{else}<img src="{image}b_up_arrow.gif">{/if}
					</th>
					<th colspan="4" class="actions">[[Actions]]</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$tree_items item=tree_value key=sid name=items_block}
					<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
						<td>
							<input type="checkbox" name="item_sid[{$sid}]" value="1" id="checkbox_{$smarty.foreach.items_block.iteration}">
							<input type="hidden" name="item_order[{$sid}]" value="1">
						</td>
						<td class="dragHandle">{$tree_value}</td>
						<td><a href="?field_sid={$field_sid}&node_sid={$sid}" title="[[Edit]]" class="editbutton">[[Edit]]</td>
						<td><a href="?field_sid={$field_sid}&node_sid={$node_sid}&action=delete&item_sid={$sid}" onclick="return confirm('[[Are you sure you want to delete this?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
						<td>{if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}<a href="?field_sid={$field_sid}&node_sid={$node_sid}&amp;action=move_down&amp;item_sid={$sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>{/if}</td>
						<td>{if $smarty.foreach.items_block.iteration > 1}<a href="?field_sid={$field_sid}&node_sid={$node_sid}&amp;action=move_up&amp;item_sid={$sid}"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>{/if}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</form>
{/if}

<script type="text/javascript" src="{$GLOBALS.site_url}/../system/ext/jquery/jquery.tablednd.js"></script>
<script>
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
		$("#tree_table").tableDnD({ 
			onDragClass: "myDragClass",
			dragHandle: "dragHandle"
		});
		
	
	});
	
	
	function submitForm(action) {
		document.getElementById('action_name').value = action;
		var form = document.tree_items_form;
		form.submit();
	}
</script>