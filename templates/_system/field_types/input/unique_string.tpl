<input type="text" value="{$value}" class="form-control {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" onblur="checkField($(this), '{$id}')"/><i class="fa fa-user form-control-feedback"></i><span class="aMessage" id="am_{$id}"></span>