{keywords} {$article.keywords} {/keywords}
{description} {$article.description} {/description}
{title} {$article.title} {/title}
{if $errors}
	{foreach from=$errors item=error key=error_code}
		<p class="error">{$error_code}</p>
	{/foreach}
{else}
	<div class="NewsItems">
		<h2>{$article.title}</h2>
		<span class="small">[[Posted]]: {tr type="date"}{$article.date}{/tr}</span>
		<div class="newsPreview">
			{* if $article.image}<img src="{$article.image_link}" align="left" vspace="10" hspace="10">{/if *}
			{$article.text}
		</div>
	</div>
{/if}

{if $GLOBALS.plugins.ShareThisPlugin.active == 1 && $GLOBALS.settings.display_on_news_page == 1}
	{$GLOBALS.settings.header_code}
	{$GLOBALS.settings.code}
{/if}
<div class="clr"><br/></div>
<span class="strong"><a href="{$GLOBALS.site_url}/news/">[[View All News]]</a></span>