{if $navCount == "0"}{else}
<div class="BreadCrumbs">
	<p>{foreach from=$navArray item=navItem name=navForeach} {if $smarty.foreach.navForeach.iteration<$navCount }<a href="{$GLOBALS.site_url}{$navItem.uri}">[[{$navItem.name}]]</a> &#187; {else} <span>[[{$navItem.name}]]</span> {/if} {/foreach} </p>
</div>
{/if}