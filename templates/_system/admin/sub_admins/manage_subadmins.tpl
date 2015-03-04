{breadcrumbs}[[Admin Sub Accounts]]{/breadcrumbs}
<h1><img src="{image}/icons/users32.png" border="0" alt="" class="titleicon"/>[[Admin Sub Accounts]]</h1>

{if !$GLOBALS.subAdminSID}
    <div id="adminSubAccounts" class="ui-tabs-panel">
        <form method="post" action="{$GLOBALS.site_url}/add-subadmin/">
            <p><a href="{$GLOBALS.site_url}/add-subadmin/" class="grayButton">[[Create a New Admin Sub-Account]]</a></p>
            <div class="head_actions">
                [[Actions with Selected]]: <br/>
                <input type="submit" name="action" value="[[Delete]]" class="deletebutton" onclick="return confirm('[[Are you sure you want to delete the selected Admin Sub-Account?]]');"/>
            </div>
            <div class="clr"><br/></div>
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" name="checkall" id="checksubadmins"/></th>
                        <th>[[Username]]</th>
                        <th>[[Email]]</th>
                        <th colspan="2" class="actions">[[Actions]]</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach item=subadmin from=$subadmins}
                        <tr class="{cycle values = 'evenrow,oddrow'}">
                            <td><input type="checkbox" name="subadmin[]" value="{$subadmin.sid}" /></td>
                            <td>{$subadmin.username}</td>
                            <td>{$subadmin.email}</td>
                            <td><a href="{$GLOBALS.site_url}/edit-subadmin/?action=edit&subadmin={$subadmin.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
                            <td><a title="[[Delete]]" onclick="return confirm('[[Are you sure you want to delete the selected Admin Sub-Account?]]');" href="{$GLOBALS.site_url}/add-subadmin/?action=delete&subadmin[]={$subadmin.sid}" class="deletebutton">[[Delete]]</a></td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </form>
    </div>

    <script type="text/javascript">
        $("#checksubadmins").click(function(){
            if( $(this).attr("checked") ){
                $("input[name^='subadmin']").each(function(){
                    $(this).attr({ checked:"checked" });
                })
            } else {
                $("input[name^='subadmin']").each(function(){
                    $(this).removeAttr("checked");
                })
            }
        });
    </script>
{/if}