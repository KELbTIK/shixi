{breadcrumbs}<a href="{$GLOBALS.site_url}/settings/">[[System Settings]]</a> &#187; [[Alphabet Letters for "Search by Company" section]]{/breadcrumbs}

{foreach from=$errors key=error item=message}
	{if $error eq "USER_GROUP_SID_NOT_SET"}
		<p class="error">[[User group SID is not set]]</p>
	{/if}
{/foreach}

{if $action == 'list'}
    <h1>[[Alphabet Letters for "Search by Company" section]]</h1>
    <p><a href="{$GLOBALS.site_url}/alphabet-letters/?action=new" class="grayButton">[[Add New Alphabet]]</a></p>
    <table>
        <thead>
            <tr>
                <th>[[SID]]</th>
                <th>[[Name]]</th>
                <th>[[Value]]</th>
                <th>[[Status]]</th>
                <th colspan="4" class="actions">[[Actions]]</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$alphabetInfo item=alphabet name=fields_block}
                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>{$alphabet.sid}</td>
                    <td>{$alphabet.name}</td>
                    <td>{$alphabet.value}</td>
                    <td>{if $alphabet.active == 1}[[active]]{else}[[inactive]]{/if}</td>
                    <td><a href="{$GLOBALS.site_url}/alphabet-letters/?action=edit&sid={$alphabet.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
                    <td><a href="{$GLOBALS.site_url}/alphabet-letters/?action=delete&sid={$alphabet.sid}" onclick="return confirm('[[Are you sure you want to delete this alphabet?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
                    <td>{if $smarty.foreach.fields_block.iteration < $smarty.foreach.fields_block.total}<a href="?action=move_down&sid={$alphabet.sid}"><img src="{image}b_down_arrow.gif" border=0>{/if}</td>
                    <td>{if $smarty.foreach.fields_block.iteration > 1}<a href="?sid={$alphabet.sid}&action=move_up"><img src="{image}b_up_arrow.gif" border=0>{/if}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{elseif $action == 'edit'}

{/if}