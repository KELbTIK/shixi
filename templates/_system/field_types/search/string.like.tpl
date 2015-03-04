<input type="text" name="{$id}[like]"  id="{$id}" class="searchStringLike" value="{if $value.like}{$value.like}{elseif $value.multi_like_and.0}{$value.multi_like_and.0}{else}{$value.equal}{/if}"/>
{if $parentID}
	{assign var="id" value=$id|replace:$parentID:''}
	{assign var="id" value=$id|replace:'_':''}
{/if}

{if $useAutocomplete == 1}
	{include file='../field_types/search/autocomplete.tpl'}
{/if}