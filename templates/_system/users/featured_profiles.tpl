{foreach from=$profiles item=profile name=profile_block}
	<div class="FeaturedCompaniesLogo">
		<a href="{$GLOBALS.site_url}/company/{$profile.id}/{$profile.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/"><img src="{$profile.Logo.thumb_file_url}" border="0" alt="{$profile.WebSite}"/></a>
	</div>
	{if $smarty.foreach.profile_block.iteration is div by $number_of_cols}
		<div class="clr"></div>
	{/if}
{/foreach}