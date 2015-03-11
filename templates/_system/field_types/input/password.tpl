<input type="password"  name="{if $complexField}{$complexField}[{$id}][{$complexStep}][original]{else}{$id}[original]{/if}" class="inputString form-control {if $complexField}complexField{/if}" /><i class="fa fa-lock form-control-feedback"></i>
<div class="form-group"></div>
<div class="row">
    <div class="col-xs-12">
        <input type="password"  name="{if $complexField}{$complexField}[{$id}][{$complexStep}][confirmed]{else}{$id}[confirmed]{/if}" class="inputString form-control {if $complexField}complexField{/if}" /><i class="fa fa-lock form-control-feedback"></i>
    </div>
</div>
<span class="small">[[Confirm Password]]</span>