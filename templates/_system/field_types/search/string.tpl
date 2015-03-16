<input type="text" name="{$id}[equal]" id="{$id}" class=" searchString form-control" value="{if $value.multi_like_and.0}{$value.multi_like_and.0}{else}{$value.equal}{/if}"/>

{if $useAutocomplete == 1}
	{include file='../field_types/search/autocomplete.tpl'}
{/if}