{if $value.file_name ne null}
<a href="{$GLOBALS.site_url}/manage-news/?action=delete_image&amp;article_sid={$article_sid}">[[Delete]]</a>
&nbsp;&nbsp;&nbsp;&nbsp;
<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
<br/><br/>
{/if}
<input type="file" name="{$id}" />
<small>([[max.]] {$uploadMaxFilesize} M)</small>