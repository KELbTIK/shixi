{title} {$listing.Title} {/title}
{keywords} {$listing.Title} {/keywords}
{description} {$listing.Title} {/description}
<div class="printPage">
	<div class="printLeft">
		<h2>[[User Info]]</h2>
		{if $listing.anonymous != 1 || $applications.anonymous === 0 }
			{foreach from=$listing.pictures key=key item=picture name=picimages }
				<div class="text-center"><a target="_blank" href ="{$picture.picture_url}"> <img src="{$picture.thumbnail_url}" border="0" title="{$picture.caption}" alt="{$picture.caption}" /> </a></div><br/>
			{/foreach}
			<span class="strong"><span class="longtext-25">{$listing.user.FirstName}</span> <span class="longtext-25">{$listing.user.LastName}</span></span>
			{if $allowViewContactInfo}{if $listing.user.Location.Address != ''}<br />{$listing.user.Location.Address}{/if}{/if}
			<br />{locationFormat location=$listing.user.Location format="long"}
			{if $allowViewContactInfo}
				<br /><span class="strong">[[Phone]]</span>: <span class="longtext-25">{$listing.user.PhoneNumber}</span>
				<br /><span class="strong">[[Email]]</span>: <span class="longtext-25">{$listing.user.email}</span>
			{elseif $acl->getPermissionParams('view_job_contact_info') == "message"}
				<br />{module name="miscellaneous" function="access_denied" permission="view_job_contact_info"}
			{/if}
		{else}
			<div class="text-center strong">[[Anonymous User Info]]</div>
		{/if}
	</div>
	<div class="printRight">
		<h1>{$listing.Title}</h1>
		{include file="../builder/bf_displaylisting_fieldsholders.tpl"}
	</div>
</div>
