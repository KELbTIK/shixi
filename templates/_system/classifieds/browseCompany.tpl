<h1>[[Search by Company]]</h1>
{foreach from = $alphabets item = alphabet name=alphabet}  
<div>
	<div class="browseCompanyAB">
		<a class='browseItem' href="{$GLOBALS.site_url}/browse-by-company/?first_char=any_char">#</a>
	</div>
	{foreach from = $alphabet item = char name=char}  
	<div class="browseCompanyAB">
		<a class='browseItem' href="{$GLOBALS.site_url}/browse-by-company/?first_char={$char}">{$char}</a>
	</div>
	{/foreach}
	<div class="clr"></div>
</div>
{/foreach}

{include file="searchFormByCompany.tpl"}