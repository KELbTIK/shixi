<input type="text" value="{$value|escape:'html'}" class="inputString {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][original]{else}{$id}[original]{/if}" onblur="checkField($(this), '{$id}')"/><span class="aMessage" id="am_{$id}"></span>
{if $isRequireConfirmation == 1}
<br/><input type="text" {if $editProfile == 1} value="{$value|escape:'html'}" {else} value="{$confirmed|escape:'html'}" {/if} class="inputString" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][confirmed]{else}{$id}[confirmed]{/if}" style="margin-top:2px;"/><br />
<span style="font-size:11px">[[Confirm E-mail]]</span>
{/if}