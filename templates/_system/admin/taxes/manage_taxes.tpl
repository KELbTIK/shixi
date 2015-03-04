{breadcrumbs}[[Tax Rules]]{/breadcrumbs}
<h1><img src="{image}/icons/paperstar32.png" border="0" alt="" class="titleicon"/>[[Tax Rules]]</h1>
<div class="clr"></div>
<form>
	<input type="hidden" name="action" value="setting" />
	<table>
		<thead>
			<tr>
				<th>[[Enable Taxes]]&nbsp;</th>
				<th align="center"><input type="checkbox" name="enable_taxes" value="1" {if $GLOBALS.settings.enable_taxes}checked = checked{/if} onChange="javascript: form.submit();" /></th>
			</tr>
		</thead>
	</table>
</form>
<p><a href="{$GLOBALS.site_url}/add-tax/" class="grayButton">[[Add Tax Rule]]</a></p>
<table>
	<thead>
	    <tr>
	    	<th>[[Tax Name]]</th>
	    	<th>[[Country]]</th>
	        <th>[[State/Province]]</th>
		    <th>[[Tax Rate]]</th>
	        <th>[[Status]]</th>
	        <th colspan="3" class="actions">[[Actions]]</th>
	    </tr>
    </thead>
    <tbody>
    	{foreach from=$taxes item=tax name=tax_block}
    		<tr class="{cycle values = 'evenrow,oddrow'}">
    			<td>{$tax.tax_name}</td>
			    <td>{if $tax.Country eq null}[[Any Country]]{else}{$tax.Country}{/if}</td>
			    <td>{if $tax.State eq null}[[Any State]]{else}{$tax.State}{/if}</td>
				<td>{tr type="float"}{$tax.tax_rate}{/tr}%</td>
    			<td>{if $tax.active == 1}[[Active]] {else}[[Not Active]]{/if}</td>
    			<td>
				    {if $tax.active == 1}
				        <input type="button" value="[[Deactivate]]" class="deletebutton" onclick="location.href='{$GLOBALS.site_url}/manage-taxes/?action=deactivate&sid={$tax.sid}'"/>
				    {else}
					    <input type="button" value="[[Activate]]" class="editbutton greenbtn" style="width: 88px; text-align: center;"  onclick="location.href='{$GLOBALS.site_url}/manage-taxes/?action=activate&sid={$tax.sid}'"/>
				    {/if}
			    </td>
    			<td><input type="button" value="[[Edit]]" class="editbutton" onclick="location.href='{$GLOBALS.site_url}/edit-tax/?sid={$tax.sid}'"/></td>
    			<td><a href="{$GLOBALS.site_url}/manage-taxes/?action=delete&sid={$tax.sid}" onClick="return confirm('[[Are you sure you want to delete this tax?]]');" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
    		</tr>
    	{/foreach}
    </tbody>
</table>
