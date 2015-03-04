{title} {$listing.Title} {/title}
{keywords} {$listing.Title} {/keywords}
{description} {$listing.Title} {/description}
<div class="printPage">
	<div class="printLeft">
		<h2>[[Company Info]]</h2>
		{if $listing.user.Logo.file_url || $listing.ListingLogo.file_url}
			<div class="text-center"><img src="{if $listing.ListingLogo.file_url}{$listing.ListingLogo.file_url}{else}{$listing.user.Logo.file_url}{/if}" alt="" /></div><br/>
		{/if}
		<span class="strong">{$listing.user.CompanyName}</span>
		{if $allowViewContactInfo}{if $listing.user.Location.Address != ''}<br />{$listing.user.Location.Address}{/if} {/if}
		<br/>{locationFormat location=$listing.user.Location format="long"}
		{if $allowViewContactInfo}
			<br/><span class="strong">[[Phone]]</span>: <span class="longtext-25">{$listing.user.PhoneNumber}</span>
			<br/><span class="strong">[[Web Site]]</span>: <span class="longtext-25">{$listing.user.WebSite}</span>
		{elseif $acl->getPermissionParams('view_job_contact_info') == "message"}
			<br/>{module name="miscellaneous" function="access_denied" permission="view_job_contact_info"}<br/>
		{/if}
	</div>
	<div class="printRight">
		<h1>{$listing.Title}</h1>
		{include file="../builder/bf_displaylisting_fieldsholders.tpl"}
	</div>
</div>