<input type="text" value="{if $id == 'keywords'}{$value.all_words}{else}{$value.like}{/if}"  name="{$id}[{if $id == 'keywords'}all_words{else}like{/if}]"  id="{$id}" />
