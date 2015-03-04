{if $errors}
    {foreach from=$errors key=error_code item=error_message}
        {if $error_code == 'UNDEFINED_LISTING_ID'}<p class="error">[[Listing ID is not defined]]</p>
        {elseif $error_code == 'WRONG_LISTING_ID_SPECIFIED'}<p class="error">[[Listing does not exist]]</p>
        {elseif $error_code == 'LISTING_IS_NOT_ACTIVE'}<p class="error">[[Listing with specified ID is not active]]</p>
        {elseif $error_code == 'LISTING_IS_NOT_APPROVED'}<p class="error">[[Listing with specified ID is not approved by admin]]</p>
        {/if}
	{/foreach}
{else}
	{if $listing.type.id == "Job"}
		{include file="job_details.tpl" listing=$listing }
	{elseif $listing.type.id == "Resume"}
		{include file="resume_details.tpl" listing=$listing }
	{/if}
{/if}
<div id="print-button"><input type=button value="[[Print This Ad]]" onClick="this.style.display='none';window.print();" class="standart-button" /></div>