{if $errors}
	{foreach from=$errors item=error key=error_code}
		<p class="error">[[{$error_code}]]</p>
	{/foreach}
{else}
	{if $show_categories_block}
		<div id="newsCategory">
			<h3>[[News Categories]]</h3>
			{if empty($current_category_sid)}
				<span class="strong">&#187; All</span>
			{else}
				<a href="{$GLOBALS.site_url}/news/">[[All]]</a>
			{/if}
			{foreach from=$categories item=category}
				{if $category.name != 'Archive' && $category.count > 0}
					{if $current_category_sid == $category.sid}
						<span class="strong">&#187; [[{$category.name}]]</span>
					{else}
						<a href="{$GLOBALS.site_url}/news/category/{$category.sid}/">[[{$category.name}]]</a>
					{/if}
				{/if}
			{/foreach}
		</div>
	{/if}

	<form action="{$GLOBALS.site_url}/news/">
		<input type="hidden" name="action" value="search" />
		<input type="text" name="search_text" value="{$searchText}" /> <input type="submit" name="submit" value="[[Search]]" />
	</form>
	<br/>

	{if $pages > 1}
		<!-- PAGINATION -->
		<p>
			<form id="news_per_page_form" method="get" action="?">
				{if $current_page-1 > 0}&nbsp;<a href="?page={$current_page-1}">[[Previous]]</a>{else}[[Previous]]{/if}
				{if $current_page-3 > 0}&nbsp;<a href="?page=1">1</a>{/if}
				{if $current_page-3 > 1}&nbsp;...{/if}
				{if $current_page-2 > 0}&nbsp;<a href="?page={$current_page-2}">{$current_page-2}</a>{/if}
				{if $current_page-1 > 0}&nbsp;<a href="?page={$current_page-1}">{$current_page-1}</a>{/if}
				<span class="strong">{$current_page}</span>
				{if $current_page+1 <= $pages}&nbsp;<a href="?page={$current_page+1}">{$current_page+1}</a>{/if}
				{if $current_page+2 <= $pages}&nbsp;<a href="?page={$current_page+2}">{$current_page+2}</a>{/if}
				{if $current_page+3 < $pages}&nbsp;...{/if}
				{if $current_page+3 < $pages + 1}&nbsp;<a href="?page={$pages}">{$pages}</a>{/if}
				{if $current_page+1 <= $pages}&nbsp;<a href="?page={$current_page+1}">[[Next]]</a>{else}[[Next]]{/if}
				<input type="hidden" name="page" value="1" />
			</form>
		</p>
		<!-- END OF PAGINATION -->
	{/if}

	{foreach from=$articles item=item}
		<div class="newsItems">
			{if $item.link}
				<h2><a href="{$item.link}" target="_blank">{$item.title}</a></h2>
			{else}
				<h2><a href="{$GLOBALS.site_url}/news/{$item.sid}/{$item.title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">{$item.title}</a></h2>
			{/if}
			<div class="newsPreview">
				<span class="small">[[Posted]]: {tr type="date"}{$item.date}{/tr}</span>
				{if $item.image}<img src="{$item.image_link}" align="left" width="100" vspace="5" hspace="5" />{/if}
				<br/>{$item.brief}
				{if $item.link}
					<p align="right"><a href="{$item.link}" target="_blank">[[read more]]</a></p>
				{else}
					<p align="right"><a href="{$GLOBALS.site_url}/news/{$item.sid}/{$item.title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">[[read more]]</a></p>
				{/if}
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"><br/></div>
	{/foreach}

	{if $pages > 1}
		<p>
			<form id="news_per_page_form" method="get" action="?">
				{if $current_page-1 > 0}&nbsp;<a href="?page={$current_page-1}">[[Previous]]</a>{else}[[Previous]]{/if}
				{if $current_page-3 > 0}&nbsp;<a href="?page=1">1</a>{/if}
				{if $current_page-3 > 1}&nbsp;...{/if}
				{if $current_page-2 > 0}&nbsp;<a href="?page={$current_page-2}">{$current_page-2}</a>{/if}
				{if $current_page-1 > 0}&nbsp;<a href="?page={$current_page-1}">{$current_page-1}</a>{/if}
				<b>{$current_page}</b>
				{if $current_page+1 <= $pages}&nbsp;<a href="?page={$current_page+1}">{$current_page+1}</a>{/if}
				{if $current_page+2 <= $pages}&nbsp;<a href="?page={$current_page+2}">{$current_page+2}</a>{/if}
				{if $current_page+3 < $pages}&nbsp;...{/if}
				{if $current_page+3 < $pages + 1}&nbsp;<a href="?page={$pages}">{$pages}</a>{/if}
				{if $current_page+1 <= $pages}&nbsp;<a href="?page={$current_page+1}">[[Next]]</a>{else}[[Next]]{/if}
				<input type="hidden" name="page" value="1" />
			</form>

		</p>
	{/if}
{/if}