{if $errors}
	{foreach from=$errors item=error key=error_code}
		<div class="error alert alert-danger" role="alert">
			[[{$error_code}]]
		</div>
	{/foreach}
{else}
	{if $show_categories_block}
		<div id="newsCategory">
			<h1>[[News Categories]]</h1>
			{if empty($current_category_sid)}
				<span class="strong">All</span>
			{else}
				<a href="{$GLOBALS.site_url}/news/">[[All]]</a>
			{/if}
			<span> | </span>
			{foreach from=$categories item=category}
				{if $category.name != 'Archive' && $category.count > 0}
					{if $current_category_sid == $category.sid}
						<span class="strong">[[{$category.name}]]</span>
					{else}
						<a href="{$GLOBALS.site_url}/news/category/{$category.sid}/">[[{$category.name}]]</a>
					{/if}
					<span> | </span>
				{/if}

			{/foreach}
		</div>
	{/if}

	<form action="{$GLOBALS.site_url}/news/" class="form-inline">
			<input type="hidden" name="action" value="search" />

					<div class="form-group">
						<input type="text" name="search_text" value="{$searchText}" class="form-control"/>
					</div>

					<div class="form-group">
						<input type="submit" name="submit" value="[[Search]]" class="btn btn-default" />
					</div>
	</form>

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
			<article class="clearfix blogpost object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="200">
				<div class="blogpost-body">
					<div class="post-info">
						<span class="day">{$item.date|date_format:"%d"}</span>
						<span class="month">{$item|date_format:"%B %Y"}</span>
					</div>
					<div class="blogpost-content">
						<header>
							<h2 class="title">
								{if $item.link}
									<h2><a href="{$item.link}" target="_blank">{$item.title}</a></h2>
								{else}
									<h2><a href="{$GLOBALS.site_url}/news/{$item.sid}/{$item.title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">{$item.title}</a></h2>
								{/if}
							</h2>
						</header>
						<p>{$item.brief}</p>
					</div>
				</div>
				<footer class="clearfix">
					{if $item.link}
						<a href="{$item.link}" target="_blank" class="smallLink pull-right link">[[read more]]</a>
					{else}
						<a href="{$GLOBALS.site_url}/news/{$item.sid}/{$item.title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html" class="smallLink pull-right link">[[read more]]</a>
					{/if}
				</footer>
			</article>
		</div>
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