{if !empty($articles)}
	<div id="news">
		<input type="hidden" name="news_count" id="news_count" value="{$news_count}" />
        {foreach from=$articles item=elem name=news_block}
            <article class="clearfix blogpost object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="200">
                <div class="blogpost-body">
                    <div class="post-info">
                        <span class="day">{$elem.date|date_format:"%d"}</span>
                        <span class="month">{$elem|date_format:"%B %Y"}</span>
                    </div>
                    <div class="blogpost-content">
                        <header>
                            <h2 class="title">
                                {if $elem.link}
                                    <a href="{$elem.link}" target="_blank" class="newsLink">{$elem.title}</a>
                                {else}
                                    <a href="{$GLOBALS.site_url}/news/{$elem.sid}/{$elem.title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html" class="newsLink">{$elem.title}</a>
                                {/if}
                            </h2>
                        </header>
                        <p>{$elem.brief}</p>
                    </div>
                </div>
                <footer class="clearfix">
                    <a href="{$GLOBALS.site_url}/news/" class="smallLink pull-right link">[[View All News]]</a>
                </footer>
            </article>
        {/foreach}
	</div>
{else}
    <br/><div class="alert alert-danger">[[There are no news in the system.]]</div><br/>
{/if}