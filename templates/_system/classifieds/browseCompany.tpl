<h1>[[Search by Company]]</h1>

	<div class="col-sm-8">

		{foreach from = $alphabets item = alphabet name=alphabet}
		<div class="page-intro">
			<span class="browseCompanyAB">
				<a class='browseItem' href="{$GLOBALS.site_url}/browse-by-company/?first_char=any_char">#</a>
			</span>
			{foreach from = $alphabet item = char name=char}
			<span class="browseCompanyAB">
				<a class='browseItem' href="{$GLOBALS.site_url}/browse-by-company/?first_char={$char}">{$char}</a>
			</span>
			{/foreach}
			<span class="clearfix"></span>
		</div>
		{/foreach}
		{include file="searchFormByCompany.tpl"}
		</div>

