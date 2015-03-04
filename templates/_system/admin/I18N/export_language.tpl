{breadcrumbs}[[Export language]]{/breadcrumbs}
<h1><img src="{image}/icons/boxupload32.png" border="0" alt="" class="titleicon"/>[[Export language]]</h1>

{if $errors}
	{include file="errors.tpl" errors=$errors}
{/if}

<table>
	<form method="post">
	<input type="hidden" name="action" value="export_language">
		<tr class="evenrow">
			<td>[[Select language to export]]</td>
			<td>
	            <select name="languageId">            
	            	{foreach from=$languages item=lang}            	
	            	<option value="{$lang.id}">{$lang.caption}</option>            		
	            	{/foreach}            	
	            </select>
	        </td>
	    </tr>
	    <tr id="clearTable">
	        <td colspan="2" align="right">
                <div class="floatRight"><input type="submit" value="[[Export]]" class="grayButton" /></div>
	        </td>
	    </tr>
	</form>
</table>