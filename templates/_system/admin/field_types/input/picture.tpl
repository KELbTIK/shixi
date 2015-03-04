{if $value.file_name ne null}
<a href="{$GLOBALS.site_url}/users/delete-uploaded-file/?user_sid={$user_info.user_sid}&amp;field_id={$id}">[[Remove]]</a>
&nbsp;&nbsp;&nbsp;&nbsp;
<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
<br/><br/>
{/if}
<input type="file" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" class="{if $complexField}complexField{/if}" />
<small>([[max.]] {$uploadMaxFilesize} M)</small>