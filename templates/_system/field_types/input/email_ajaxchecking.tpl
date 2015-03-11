<input type="text" value="{$value|escape:'html'}" class="inputString form-control {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][original]{else}{$id}[original]{/if}" onblur="checkField($(this), '{$id}')"/><i class="fa fa-envelope form-control-feedback"></i><span class="aMessage" id="am_{$id}"></span>
{if $isRequireConfirmation == 1}
    <div class="form-group"></div>
    <div class="row">
        <div class="col-xs-12">
            <input type="text" {if $editProfile == 1} value="{$value|escape:'html'}" {else} value="{$confirmed|escape:'html'}" {/if} class="inputString form-control" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][confirmed]{else}{$id}[confirmed]{/if}" /><i class="fa fa-envelope form-control-feedback"></i>
        </div>
    </div>
<span class="small">[[Confirm E-mail]]</span>
{/if}