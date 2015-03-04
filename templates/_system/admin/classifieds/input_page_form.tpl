<script type="text/javascript">
    $( function() {
        var action = '{$action}';
        if (action == 'new') {
            $('#save').attr('value', '[[Add]]');
        }
    });

    $.ui.dialog.prototype.options.bgiframe = true;
    var progbar = "<img src='{$GLOBALS.site_url}/../system/ext/jquery/progbar.gif' />";
    function moveTo ( link, caption ) {
        $("#dialog").dialog('destroy');
        $("#dialog").attr({ title: "[[Loading]]" });
        $("#dialog").html(progbar).dialog({ width: 180 });
        $.get(link, function(data){
            $("#dialog").dialog('destroy');
            $("#dialog").attr({ title: "[[Move]] " + caption });
            $("#dialog").html(data).dialog({
                width: 400,
                buttons: {
                    Close: function() {
                        $(this).dialog('close');
                    },
                    Save: function() {
                        $.get(link, { "movePageID": $("#movePageID").val() },  function(data){
                            parent.document.location.reload();
                        });
                    }
                }
            });
        });
    }
</script>

{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a> &#187; <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}">[[Edit]] [[{$listingTypeInfo.name}]] [[Posting Pages]]</a> &#187; {if $action == 'edit'}[[Edit]] [[{$pageInfo.page_name}]]{else}[[Add a New Posting Page]]{/if}{/breadcrumbs}
<h1>
    {if $action == 'edit'}
        <img src="{image}/icons/linedpapercheck32.png" border="0" alt="" class="titleicon"/>[[Edit]] [[$pageInfo.page_name]]
    {else}
        <img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>[[Add a New Posting Page]]
    {/if}
</h1>
<div id="dialog"></div>

{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'{$field_caption}' [[is empty]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'{$field_caption}' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'{$field_caption}' [[is not an float value]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">'{$field_caption}' [[is not valid]]</p>
	{elseif $error eq 'CAN_NOT_EQUAL_NULL'}
		<p class="error">'{$field_caption}' [[can not equal "0"]]</p>
	{/if}
{/foreach}

<fieldset>
<legend>{if $action == 'edit'}[[Posting Page Info]]{else}[[Add a New Posting Page]]{/if} </legend>
    <form method=post action="">
        <input type="hidden" id="action" name="submit" value="save"/>
        {if $button == 'Edit'}<input type="hidden" name="sid" value="{$sid}">{/if}
        <table>
            {foreach from=$form_fields item=form_field}
            <tr >
                <td valign="top">[[$form_field.caption]]</td>
                <td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
                <td>{input property=$form_field.id}</td>
            </tr>
            {/foreach}
            <tr>
                <td colspan="3" align="center">
                    <div class="floatRight">
                        {if $action == 'edit'}<input type="submit" id="apply" value="[[Apply]]" class="greenButton"/>{/if}
                        <input type="submit" id="save" value="[[Save]]" class="greenButton" />
                    </div>
                </td>
            </tr>
        </table>
    </form>
</fieldset>

{if $action == 'edit'}
    <div class="clr"><br/></div>
	<fieldset>
		<legend>[[Add Posting Page Fields]]</legend>
		<form method=post action="">
			<input type="hidden" name="field_action" value="add_fields" />
			<table>
				<tr>
					<td><div class="posting-fields-legend">&nbsp;</div>
					<div>[[Highlighted fields are already used on another page. <br/>Adding them to this page, will remove them from another one.]]</div></td>
				</tr>
				<tr>
					<td>
						<select multiple="multiple" class="inputList" name="listing_fields[]">
							{foreach from=$listing_fields item=listing_field}
								<option value="{$listing_field.sid}" style="border-bottom: 1px dashed #CCC; {if $listing_field.used == 1}background-color: #ffc481"{/if}">[[{$listing_field.caption}]]</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td align="right">
                        <div class="floatRight"><input type="submit" name="saveFields" value="[[Add]]" class="greenButton" /></div>
                    </td>
				</tr>
			</table>
		</form>
	</fieldset>
    <div class="clr"><br/></div>
    <h1>[[{$pageInfo.page_name}]] [[Fields]]</h1>
    <form method="post" action="" name="fields_items_form" id="fields_items_form">
        <input type="hidden" name="field_action" id="field_action" value="save_order" />
        <input type="hidden" name="page_sid" id="page_sid" value="{$pageSID}" />
        <input type="button" name="action" value="[[Save Order]]" class="grayButton" onclick="if ( confirm('[[Are you sure you want to save the current order for the fields on this page?]]') ) saveOrder();">
        <div class="clr"><br/></div>

        <table id="fields_table">
            <thead>
                <tr>
                    <th>[[Caption]]</th>
                    <th>[[Type]]</th>
                    <th>[[Required]]</th>
                    <th colspan="4" width="20%" class="actions">[[Actions]]</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$fieldsOnPage item=fieldOnPage name=fieldList}
                    <tr class="{cycle values = 'evenrow,oddrow' advance=true}">
                        <td class="dragHandle {if $fieldOnPage.type == 'location'} posting-field-td{/if}">
							{if $fieldOnPage.type == 'location'}
								<div class="field-holder">
									[[{$fieldOnPage.caption}]]
									<input type="hidden" name="item_order[{$fieldOnPage.sid}]" value="1">
								</div>
								{foreach from=$fieldOnPage.fields item=field}
									<div class="field">
										{if $field.id == 'Country'}
			                            	 <a href="{$GLOBALS.site_url}/countries/">[[{$field.caption}]]</a>
			                            {elseif $field.id == 'State'}
			                            	 <a href="{$GLOBALS.site_url}/states/">[[{$field.caption}]]</a>
			                            {else}
			                            	[[{$field.caption}]]
			                            {/if}
									</div>
								{/foreach}
                            {else}
								[[{$fieldOnPage.caption}]]
								<input type="hidden" name="item_order[{$fieldOnPage.sid}]" value="1">
                            {/if}
                        </td>
                        <td {if $fieldOnPage.type == 'location'} class="posting-field-td" {/if}>
                        	{if $fieldOnPage.type == 'location'}
                        		<div>
                        			[[{$fieldOnPage.type}]]
                        		</div>
                        		{foreach from=$fieldOnPage.fields item=field}
									<div class="field">
			                            [[{$field.type}]]
									</div>
								{/foreach}
                        	{else}
                        		[[{$fieldOnPage.type}]]
                        	{/if}
                        </td>
                        <td {if $fieldOnPage.type == 'location'} class="posting-field-td" {/if}>
                        	{if $fieldOnPage.type == 'location'}
								<div>
                        			&nbsp;
                        		</div>
								{foreach from=$fieldOnPage.fields item=field}
									<div class="field">
			                            {if $field.is_required}[[Yes]]{else}[[No]]{/if}
									</div>
								{/foreach}
                        	{else}
                        		{if $fieldOnPage.is_required}[[Yes]]{else}[[No]]{/if}
                        	{/if}
                        </td>
                        <td  align="center" valign="top" nowrap="nowrap">{if $countPages > 1}<a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$pageSID}/move_to/" onclick="moveTo('{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$pageSID}/?field_action=move&field_sid={$fieldOnPage.sid}', '{$fieldOnPage.caption}'); return false;" title="[[Move]]" class="grayButton">[[Move]]</a>{/if}</td>
                        <td  align="center" valign="top"><a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$pageSID}/?field_action=remove&relationId={$fieldOnPage.relationId}" onclick='return confirm("[[The removed field will remain in the system but will not be displayed on the front-end, until added to one of the Posting Pages again. Remove the field?]]")' title="[[Remove]]" class="deletebutton">[[Remove]]</a></td>

                        <td  align="center" valign="top">
                            {if $smarty.foreach.fieldList.iteration < $smarty.foreach.fieldList.total}
                                <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$pageSID}/?field_action=move_down&field_sid={$fieldOnPage.sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
                            {/if}
                        </td>
                        <td  align="center" valign="top">
                            {if $smarty.foreach.fieldList.iteration > 1}
                                <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$pageSID}/?field_action=move_up&field_sid={$fieldOnPage.sid}"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </form>
	<div class="clr"><br/></div>
	<script type="text/javascript" src="{$GLOBALS.site_url}/../system/ext/jquery/jquery.tablednd.js"></script>
	<script>
		$('#apply').click(
			function(){
				$('#action').attr('value', 'apply_info');
			}
		);

		$( function() {

			// Drag'n'Drop table
			$("#fields_table").tableDnD({
				onDragClass: "myDragClass",
				dragHandle: "dragHandle"
			});
		});

		function saveOrder() {
			var form = document.fields_items_form;
			form.submit();
		}
	</script>
{/if}