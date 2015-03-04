{*
	* Template for apply to JobG8 listings.
	* It opened after click 'Apply Now' button of JobG8 listings.
*}

{if $errors}
	{foreach from=$errors item=error key=error_code}
		<p class="error">
			{if $error_code == 'UNDEFINED_LISTING_ID'}[[Undefined Listing ID for apply]]
			{elseif $error_code == 'WRONG_LISTING_ID_SPECIFIED'} [[Listing does not exist]]
			{/if}
		</p>
	{/foreach}
{else}
	<div class="text-center">
		<h2>[[Apply Now]]</h2>
		<!-- Jobg8 iframe -->
		<iframe border="0" runat="server" height="450px" width="700px" frameborder="0" src="{$applicationURL|regex_replace:"/http[s]?:/":""}"></iframe>
		<!-- /Jobg8 iframe -->
	</div>
{/if}