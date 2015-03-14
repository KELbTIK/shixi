{if $errors}
    {foreach from=$errors key=error_code item=error_message}
        <div class="error alert alert-danger">
            {if $error_code == 'UNDEFINED_LISTING_ID'}[[Listing ID is not defined]]
            {elseif $error_code == 'WRONG_LISTING_ID_SPECIFIED'}[[Listing does not exist]]
            {elseif $error_code == 'LISTING_IS_NOT_ACTIVE'}[[Listing with specified ID is not active]]
            {elseif $error_code == 'LISTING_IS_NOT_APPROVED'}[[Listing with specified ID is not approved by admin]]
            {/if}
        </div>
	{/foreach}
{else}
	{if $listing.type.id == "Job"}
		{include file="job_details.tpl" listing=$listing }
	{elseif $listing.type.id == "Resume"}
		{include file="resume_details.tpl" listing=$listing }
	{/if}
{/if}
<div id="print-button"><input type=button value="[[Print This Ad]]" onClick="this.style.display='none';window.print();" class="btn btn-default" /></div>