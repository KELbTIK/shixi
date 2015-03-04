{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$userGroupInfo.id|lower}/">[[Manage {if $userGroupInfo.id == 'Employer' || $userGroupInfo.id == 'JobSeeker'}{$userGroupInfo.name}s{else}'{$userGroupInfo.name}' Users{/if}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit {$userGroupInfo.name}]]</a> &#187; [[View Email Log]]{/breadcrumbs}
<h1><img src="{image}/icons/mailstar32.png" border="0" alt="" class="titleicon"/>[[View Email Log for]] {$user_info.username}</h1>

<div class="clr"><br/></div>

<div class="box" id="displayResults">
    <div class="box-header">
        <div class="resultsnumber"><strong>{$emailsCount}</strong> [[emails]]</div>
        <div class="pagination">
            {foreach from=$pages item=page}
                {if $page == $currentPage}
                    <strong>{$page}</strong>
                {else}
                    {if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
                    <a href="?page={$page}&amp;user_sid={$user_sid}{if $sorting_field ne null}&amp;sorting_field={$sorting_field}{/if}{if $sorting_order ne null}&amp;sorting_order={$sorting_order}{/if}&amp;items_per_page={$items_per_page}{$searchFields}">{$page}</a>
                    {if $page == 1 && $currentPage > 4} ... {/if}
                {/if}
            {/foreach}
        </div>
        <div class="numberPerPage">
            [[per page]]
            <select id="items_per_page" name="items_per_page" onchange="window.location = '?restore=1&items_per_page='+this.value;" class="perPage">
                <option value="10" {if $items_per_page == 10}selected="selected"{/if}>10</option>
                <option value="20" {if $items_per_page == 20}selected="selected"{/if}>20</option>
                <option value="50" {if $items_per_page == 50}selected="selected"{/if}>50</option>
                <option value="100" {if $items_per_page == 100}selected="selected"{/if}>100</option>
            </select>
        </div>
    </div>
    <div class="innerpadding">
        <div id="displayResultsTable">
            <table width="100%">
                <thead>
                    <tr>
                        <th>
                            <a href="?restore=1&amp;user_sid={$user_sid}&amp;sorting_field=date&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'date'}DESC{else}ASC{/if}&amp;items_per_page={$items_per_page}">[[Date]]</a>
                        {if $sorting_field == 'date'}{if $sorting_order == 'ASC'}<img src="{image}b_down_arrow.gif" />{else}<img src="{image}b_up_arrow.gif" />{/if}{/if}
                        </th>
                        <th>
                            <a href="?restore=1&amp;user_sid={$user_sid}&amp;sorting_field=subject&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'subject'}DESC{else}ASC{/if}&amp;items_per_page={$items_per_page}">[[Subject]]</a>
                        {if $sorting_field == 'subject'}{if $sorting_order == 'ASC'}<img src="{image}b_down_arrow.gif" />{else}<img src="{image}b_up_arrow.gif" />{/if}{/if}
                        </th>
                        <th>
                            <a href="?restore=1&amp;user_sid={$user_sid}&amp;sorting_field=status&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'status'}DESC{else}ASC{/if}&amp;items_per_page={$items_per_page}">[[Status]]</a>
                        {if $sorting_field == 'status'}{if $sorting_order == 'ASC'}<img src="{image}b_down_arrow.gif" />{else}<img src="{image}b_up_arrow.gif" />{/if}{/if}
                        </th>
                    </tr>
                </thead>
                {foreach from=$found_emails item=found_email name=emails_block}
                    <tr id="users[{$found_user.sid}]" class="{cycle values = 'evenrow,oddrow'}">
                        <td>{tr type="date"}{$found_email.date}{/tr}<br />{$found_email.date|date_format:"%H:%M:%S"}</td>
                        <td><a href="{$GLOBALS.site_url}/email-log/display-message/" onClick="popUpWindow('{$GLOBALS.site_url}/email-log/?action_name=display_message&sid={$found_email.sid}',600, 500, '[[Viewing Email Message]]'); return false;">{$found_email.subject}</a></td>
                        <td>
                            {if $found_email.error_msg}
                                <a href="{$GLOBALS.site_url}/email-log/display-message/" onClick="popUpWindow('{$GLOBALS.site_url}/email-log/?action_name=display_message&display_error=1&sid={$found_email.sid}',400, 100, '[[Viewing Error Message]]'); return false;"><strong>[[{$found_email.status}]]</strong></a>
                                {else}
                                [[{$found_email.status}]]
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </div>
    <div class="box-footer">
        <div class="resultsnumber"><strong>{$emailsCount}</strong> [[emails]]</div>
        <div class="pagination">
        {foreach from=$pages item=page}
            {if $page == $currentPage}
                <strong>{$page}</strong>
                {else}
                {if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
                <a href="?page={$page}&amp;user_sid={$user_sid}{if $sorting_field ne null}&amp;sorting_field={$sorting_field}{/if}{if $sorting_order ne null}&amp;sorting_order={$sorting_order}{/if}&amp;items_per_page={$items_per_page}{$searchFields}">{$page}</a>
                {if $page == 1 && $currentPage > 4} ... {/if}
            {/if}
        {/foreach}
        </div>
        <div class="numberPerPage">
            [[per page]]
            <select id="items_per_page" name="items_per_page" onchange="window.location = '?restore=1&items_per_page='+this.value;" class="perPage">
                <option value="10" {if $items_per_page == 10}selected="selected"{/if}>10</option>
                <option value="20" {if $items_per_page == 20}selected="selected"{/if}>20</option>
                <option value="50" {if $items_per_page == 50}selected="selected"{/if}>50</option>
                <option value="100" {if $items_per_page == 100}selected="selected"{/if}>100</option>
            </select>
        </div>
    </div>
</div>