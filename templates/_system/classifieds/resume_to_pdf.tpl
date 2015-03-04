<table border="0" cellpadding="2" cellspacing="4">
	<tr>
		<td style="text-align: left;">
			<br />
			{if $listing.pictures[0].thumbnail_url}
				<img src="{$listing.pictures[0].thumbnail_url}" border="0" />
			{/if}
		</td>
		<td style="text-align: right;">
			<br />
			{if $myListing || $listing.anonymous != 1 || $applications.anonymous === 0}
				{if $allowViewContactInfo || $myListing}
					<br />
					{$listing.user.Location.Address}
					<br />
				{/if}
				{locationFormat location=$listing.user.Location format="long"}
				{if $allowViewContactInfo || $myListing}
					<br /><strong>[[Phone]]</strong>: {$listing.user.PhoneNumber}
					<br /><strong>[[Email]]</strong>: <a href="mailto:">{$listing.user.email}</a><br />
				{/if}
			{else}
				<br /><span class="strong">[[Anonymous User Info]]</span>
			{/if}
		</td>
	</tr>
	<hr>
	<tr>
		<td colspan="2" style="text-align: center">
			{if $listing.anonymous != 1 || $applications.anonymous === 0 || $myListing}
				<h1>{if $listing.user.FirstName}{$listing.user.FirstName}&nbsp;{/if}{if $listing.user.LastName}{$listing.user.LastName}{/if}</h1>
			{else}
				<h1>[[Anonymous User]]</h1>
			{/if}
			<br />
			<h2>{if $listing.Title}{$listing.Title}{/if}</h2>
			<br />
		</td>
	</tr>
</table>
{include file="../builder/bf_displaylisting_fieldsholders_for_pdf.tpl"}
{if $listing.pictures[1].thumbnail_url}
	<table border="0" cellpadding="2" cellspacing="4">
		<tr>
			{foreach from=$listing.pictures key=key item=picture name=picimages}
				{if !$smarty.foreach.picimages.first}
					<td><img src="{$picture.thumbnail_url}" border="0" /></td>
					{if ($smarty.foreach.picimages.iteration - 1) is div by 5}
						{if !$smarty.foreach.picimages.last}
							</tr>
							</table>
							<table>
							<tr>
						{/if}
					{/if}
				{/if}
			{/foreach}
		</tr>
	</table>
{/if}