<input type="text" value="{$value|escape:'html'}" class="inputString {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][original]{else}{$id}[original]{/if}" />
	</td>
</tr>
{if $isRequireConfirmation == 1}
<tr>
	<td valign="top"></td>
	<td valign="top" align="right" class="required">*</td>
	<td><input type="text" {if $editProfile == 1} value="{$value|escape:'html'}" {else} value="{$confirmed|escape:'html'}" {/if} class="inputString" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][confirmed]{else}{$id}[confirmed]{/if}" style="margin-top:2px;"/><br />
		<span style="font-size:11px">[[Confirm E-mail]]</span>
	</td>
</tr>
{/if}