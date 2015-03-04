{breadcrumbs}[[Refine Search Settings]]{/breadcrumbs}
<h1><img src="{image}/icons/search32.png" border="0" alt="" class="titleicon"/>[[Refine Search Settings]]</h1>

<form method="post" action="">
	<input type="hidden" name="action" value="save_setting" />
	<p>[[Items Limit]]: <input type="text" name="refine_search_items_limit" value="{$refine_search_items_limit}" /> <input type="submit" value="[[Save]]" class="greenButton"/>
	<br /><small>[[Specify the limit for the number of items to be displayed in Refine Search blocks]]</small></p>
</form>

{foreach from=$listingTypes item=listingType}
    <fieldset>
        <legend>[[{$listingType.id} Search]]</legend>
        <form method="post" action="">
            <input type="hidden" name="action" value="save_setting" />
            <input type="hidden" name="listing_type_id" value="{$listingType.id}" />
            <input type="hidden" name="turn_on_refine_search_{$listingType.id}" value="0">
            <input type="checkbox" name="turn_on_refine_search_{$listingType.id}"  value="1" {if $listingType.setting}checked{/if} onchange='form.submit();' /> [[Turn on Refine Search]]
        </form>
        <form method="post" action="" class="refine-block">
            <input type="hidden" name="action" value="save" />
            <input type="hidden" name="listing_type_sid" value="{$listingType.sid}" />
            <select name="field_id" class="left">
                {foreach from=$listingType.fields item=fields}
                    <option value="{$fields.sid}">[[{$fields.caption}]]</option>
                {/foreach}
                {if $listingType.user_fields.id}
                    <option value="user_{$listingType.user_fields.sid}">{$listingType.user_fields.id}</option>
                {/if}
            </select><span class="greenButtonInEnd"><input type="submit" value="[[Add]]" class="grayButton" /></span>
        </form>
        <br/>
        <table>
            <thead>
                <tr>
                    <th>[[Field Name]]</th>
                    <th colspan="3" width="1%" class="actions">[[Actions]]</th>
                </tr>
            </thead>
            {foreach from=$listingType.saved_fields item=saved_fields name=items_block}
            <tr class="{cycle values = 'evenrow,oddrow'}">
                <td width="100%">[[{$saved_fields.caption}]]</td>
                <td nowrap="nowrap"><a href="?field_id={$saved_fields.id}&action=delete" onclick='return confirm("[[Are you sure you want to delete this field?]]")' title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
                <td nowrap="nowrap">
                    {if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}
                        <a href="?field_id={$saved_fields.id}&amp;listing_type_sid={$listingType.sid}&amp;action=move_down"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
                    {/if}
                </td>
                <td nowrap="nowrap">
                    {if $smarty.foreach.items_block.iteration > 1}
                        <a href="?field_id={$saved_fields.id}&amp;listing_type_sid={$listingType.sid}&amp;action=move_up"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
                    {/if}
                </td>
            </tr>
            {/foreach}
        </table>
    </fieldset>
    <br/>
{/foreach}