{if !empty($articles)}
	<div id="news">
		<input type="hidden" name="news_count" id="news_count" value="{$news_count}" />
		<ul>
			{foreach from=$articles item=elem name=news_block}
				<li>
					{*
						{if $elem.image}
							<img src="{$elem.image_link}" width="80" align="left" vspace="3" hspace="3">
						{/if}
					*}
					<span class="small">{tr type="date"}{$elem.date}{/tr}</span><br/>
					{if $elem.link}
						<a href="{$elem.link}" target="_blank" class="newsLink">{$elem.title}</a>
					{else}
						<a href="{$GLOBALS.site_url}/news/{$elem.sid}/{$elem.title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html" class="newsLink">{$elem.title}</a>
					{/if}
					<br/>{$elem.brief}
				</li>
			{foreachelse}
				<li class="text-center">[[There is no news in the system.]]</li>
			{/foreach}
		</ul>
		<a href="{$GLOBALS.site_url}/news/" class="smallLink">[[View All News]]</a>
	</div>
{else}
	<div id="news">
		<ul>
			<li class="text-center">[[There are no news in the system.]]</li>
		</ul>
	</div>
{/if}