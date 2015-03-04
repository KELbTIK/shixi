{if !empty($articles)}
	<div id="news">
		<input type="hidden" name="news_count" id="news_count" value="{$news_count}" />
		<ul>
			{foreach from=$articles item=elem name=news_block}
				<li>
					{if $elem.link}
						<a href="{$elem.link}" target="_blank">{$elem.title}</a>
					{else}
						<a href="{$GLOBALS.site_url}/news/{$elem.sid}/{$elem.title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">{$elem.title}</a>
					{/if}
					<div class="clr"></div>
					<div class="news-date">{tr type="date"}{$elem.date}{/tr}</div>
					{$elem.brief}
				</li>
			{foreachelse}
				<li><span class="text-center">[[There is no news in the system.]]</span></li>
			{/foreach}
		</ul>
	</div>
	<div class="view-all">
		<a href="{$GLOBALS.site_url}/news/">[[View All News]]</a>
	</div>
{else}
	<div id="news">
		<ul>
			<li>[[There are no news in the system.]]</li>
		</ul>
	</div>
{/if}