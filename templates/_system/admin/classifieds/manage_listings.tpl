{breadcrumbs}
	[[Manage {$listingsType.name}s]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[Manage {$listingsType.name}s]]</h1>
<p>
	<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingsType.id|lower}" class="grayButton">[[Add New {$listingsType.name}]]</a>
</p>

{if $show_search_form}
	<div class="setting_button" id="mediumButton">[[Click to modify search criteria]]<div class="setting_icon"><div id="accordeonClosed"></div></div></div>
	<div class="setting_block" style="display: none" id="clearTable">
{else}
	<div class="setting_block" id="clear">
{/if}
		<form method="post" name="search_form">
			<input type="hidden" name="action" value="search" />
			<input type="hidden" name="page" value="1" />
			<table  width="100%">
				<tr>
					<td>[[Keywords/Listing ID]]: </td>
					<td><input type="text" value="{if $idKeyword}{$idKeyword|escape:'html'}{/if}" name="idKeyword" id="idkeyword"></td>
				</tr>
				<tr>
					<td>[[Activation Date]]:</td>
					<td>{search property="activation_date"}</td>
				</tr>
				<tr>
					<td>[[Expiration Date]]:</td>
					<td>{search property="expiration_date"}</td>
				</tr>
				<tr>
					<td>
						{if ($listingsType.id == 'Resume')}
							[[Username/Name]]:
						{elseif ($listingsType.id == 'Job')}
							[[Username/Company Name]]:
						{else}
							[[Username]]:
						{/if}
					</td>
					<td>
						<input type="text" value="{if $companyName}{$companyName|escape:'html'}{/if}" name="company_name[like]" />
					</td>
				</tr>
				{if $showApprovalStatusField != false }
					<tr>
						<td>[[Approval Status]]: </td>
						<td>{search property="status"}</td>
					</tr>
				{/if}
				<tr>
					<td>[[Product]]: </td>
					<td>{search property="product_info_sid" template="list.like.tpl"}</td>
				</tr>
				<tr>
					<td>[[Status]]: </td>
					<td>{search property="active"}</td>
				</tr>
				<tr>
					<td>[[Data Source]]: </td>
					<td>{search property="data_source"}</td>
				</tr>
				{if ($listingsType.id != 'Resume')}
					<tr>
						<td>[[Featured]]: </td>
						<td>{search property="featured"}</td>
					</tr>
				{/if}
				<tr>
					<td>[[Priority]]: </td>
					<td>{search property="priority"}</td>
				</tr>
				{capture name="trFind"}[[Find]]{/capture}
				<tr>
					<td colspan="2">
						<div class="floatRight">
							<input type="submit" value="{$smarty.capture.trFind|escape:'html'}" class="greenButton" />
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>

<script>
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");

	$( function() {
		$("#activation_date_notless, #activation_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});

		$("#expiration_date_notless, #expiration_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});

		$(".setting_button").click(function(){
			var butt = $(this);
			$(this).next(".setting_block").slideToggle("normal", function(){
					if ($(this).css("display") == "block") {
						butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
						butt.children("b").text("[[Click to hide search criteria]]");
					} else {
						butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
						butt.children("b").text("[[Click to modify search criteria]]");
					}
				});
		});
	});
</script>