{foreach from=$current_banners item=current_banner}
{if $current_banner.type == 'application/x-shockwave-flash'}

<div style="width: 100%; text-align: center;">
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0" ID="banner"" WIDTH="{$current_banner.width}" HEIGHT="{$current_banner.height}">
	<param name="movie" value="{$GLOBALS.site_url}{$current_banner.image_path}">
	<param name="quality" value="high">
	<param name="loop" value="true">
	<embed FlashVars="sjb_banner_link={capture name="banner_link"}{$GLOBALS.site_url}/go-link/?bannerId={$current_banner.id}{/capture}{$smarty.capture.banner_link|escape:'url'}&sjb_banner_window={$current_banner.openBannerIn}" src="{$GLOBALS.site_url}{$current_banner.image_path}" loop="true" quality="high" WIDTH="{$current_banner.width}" HEIGHT="{$current_banner.height}" TYPE="application/x-shockwave-flash"  PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
	</embed>
	</object>
</div>

{else}
<div class="banner">
	{if $current_banner.bannerType == 'file'}
	<a href="{$GLOBALS.site_url}/go-link/?bannerId={$current_banner.id}" target="{$current_banner.openBannerIn}">
		<img src="{$GLOBALS.site_url}{$current_banner.image_path}" width="{$current_banner.width}" height="{$current_banner.height}" title="{$current_banner.title}" border="0"/>
	</a>
	{else}
		<a href="{$GLOBALS.site_url}/go-link/?bannerId={$current_banner.id}" target="{$current_banner.openBannerIn}">
			{$current_banner.code}
		</a>
	{/if}
</div>
{/if}
{/foreach}