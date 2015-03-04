{foreach from=$pictures item=picture}
	<img src="{$picture.picture_url|escape:'html'}" border="0" alt=""/>
{/foreach}