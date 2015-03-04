{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/?restore=1">
		[[Manage {$listingType.name}s]]
	</a>
	&#187; [[Display Listing]]
{/breadcrumbs}
<h1>[[Display Listing]]</h1>

{if $errors}
	{foreach from=$errors key=error item=error_message}
		{if $error == 'LISTING_ID_ISNOT_SPECIFIED'}
			<p class="error">[[Listing ID is not specified]]</p>
		{elseif $error == 'LISTING_DOESNOT_EXIST'}
			<p class="error">[[Listing with specified ID does not exist]]</p>
		{elseif $error == 'NO_SUCH_FILE'}
			<p class="error">[[No such file found in the system]]</p>
		{/if}
	{/foreach}
{else}

	<p>
		{if $comments_total > 0}
			<a href="{$GLOBALS.site_url}/listing-comments/?listing_id={$listing.id}">[[Comments]] ({$comments_total})</a>,
		{else}
			[[Comments]] ({$comments_total}),
		{/if}
		{if $rate}
			<a  href="{$GLOBALS.site_url}/listing-rating/?listing_id={$listing.id}">[[Rate]] ({$rate})</a>
		{else}
			[[Rate]] ({$rate})
		{/if}
	</p>

	<table>
		<thead>
		    <tr>
				<th colspan="2">[[Listing Details]]</th>
			</tr>
		</thead>
		<tr class="oddrow">
			<td>[[Listing ID]]</td>
			<td>{$listing.id}</td>
		</tr>
		<tr class="evenrow">
			<td>[[Listing Type]]</td>
			<td>{$listing.type.id}</td>
		</tr>
		<tr class="oddrow">
			<td>[[Activation Date]]</td>
			<td>{$listing.activation_date}</td>
		</tr>
		<tr class="evenrow">
			<td>[[Expiration Date]]</td>
			<td>{$listing.expiration_date}</td>
		</tr>
		<tr class="oddrow">
			<td>[[Username]]{if in_array($listingType.id, ['Job', 'Resume'])} ({if $listingType.id == 'Job'}[[Company Name]]{else}[[Name]]{/if}){/if}</td>
			<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$listing.user.sid}">{$listing.user.username}</a>
				{if in_array($listing.type.id, ['Job', 'Resume'])}
					({if $listing.type.id == 'Job'}{$listing.user.CompanyName}{else}{$listing.user.FirstName} {$listing.user.LastName}{/if})
				{/if}
			</td>
		</tr>
		<tr class="evenrow">
			<td>[[# of Views]]</td>
			<td>{$listing.views}</td>
		</tr>
		{foreach from=$form_fields item=field}
			{* Hide anonymous field for Jobs, hide 'reject_reason' and 'status' for not wait approve listings *}
			{if (!isset($form_fields.Resume) && $form.id == anonymous) || 
				($wait_approve == 0 && ($field.id == 'reject_reason' || $field.id == 'status'))}
			{elseif $field.id == 'video' && empty($listing.video.file_url)}
			<tr class="{cycle values='oddrow,evenrow'}">
				<td>[[{$field.caption}]]</td>
				<td></td>
			</tr>
			{elseif $field.id == 'Salary' or $field.id == 'DesiredSalary'}
			<tr class="{cycle values='oddrow,evenrow'}">
				<td>[[{$field.caption}]]</td>
				<td>{display property=$field.id}&nbsp;{if $field.id == 'DesiredSalary'}{display property=DesiredSalaryType}{else}{display property=SalaryType}{/if}</td>
			</tr>
			{elseif $field.id == 'ApplicationSettings'}
				<tr class="{cycle values='oddrow,evenrow'}">
					<td>[[{$field.caption}]]</td>
					<td>{display property=$field.id template="application.settings.tpl"}</td>
				</tr>	
			{elseif $field.id == 'access_type'}
			<tr class="{cycle values='oddrow,evenrow'}">
				<td>[[{$field.caption}]]</td>
				<td>
					{foreach from=$access_type_properties->type->property_info.list_values item=access_type}
						{if $access_type_properties->value == $access_type.id}
							[[{$access_type.caption}]]
						{/if}	
					{/foreach}
				</td>
			</tr>
			{elseif $field.type == 'location'}
			<tr class="{cycle values='oddrow,evenrow'}">
				<td>[[{$field.caption}]]</td>
				<td>
					{locationFormat location=$listing.Location format="extraLong"}
				</td>
			</tr>	
			{elseif $field.id == 'SalaryType' || $field.id == 'DesiredSalaryType'}
			{else}
				<tr class="{cycle values='oddrow,evenrow'}">
					<td>[[{$field.caption}]]</td>
					<td>
						{display property=$field.id}
					</td>
				</tr>
			{/if}
		{/foreach}
	</table>
{/if}