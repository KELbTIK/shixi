{if $complex}
	{if $listing_type_sid}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$listing_type_sid}">[[$listing_type_info.name]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$listing_field_info.sid}">[[$listing_field_info.caption]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?field_sid={$listing_field_info.sid}">[[Edit Fields]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?sid={$field_sid}&field_sid={$listing_field_info.sid}&action=edit">[[$field_info.caption]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/edit-list/?field_sid={$field_sid}">[[Edit List]]</a>
			&#187; {$list_item_value}
		{/breadcrumbs}
	{else}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-fields/">[[Listing Fields]]</a>
			 &#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$listing_field_info.sid}">[[$listing_field_info.caption]]</a>
			 &#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?field_sid={$listing_field_info.sid}">[[Edit Fields]]</a>
			 &#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/?sid={$field_sid}&field_sid={$listing_field_info.sid}&action=edit">[[$field_info.caption]]</a>
			 &#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-fields/edit-list/?field_sid={$field_sid}">[[Edit List]]</a>
			 &#187; {$list_item_value}
		{/breadcrumbs}
	{/if}
{else}
	{if $listing_type_sid}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$listing_type_sid}">[[$listing_type_info.name]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">[[$listing_field_info.caption]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-list/?field_sid={$field_sid}">[[Edit List]]</a>
			&#187; {$list_item_value}
		 {/breadcrumbs}
	{else}
		{breadcrumbs}
			<a href="{$GLOBALS.site_url}/listing-fields/">[[Listing Fields]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/?sid={$field_sid}">[[$listing_field_info.caption]]</a>
			&#187; <a href="{$GLOBALS.site_url}/edit-listing-field/edit-list/?field_sid={$field_sid}">[[Edit List]]</a>
			&#187; {$list_item_value}
		 {/breadcrumbs}
	{/if}
{/if}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon" /> [[Edit List Item]]</h1>
{include file='field_errors.tpl'}

<fieldset>
	<legend>[[Edit List Item]]</legend>
	<form method="post" action="">
	<input type="hidden" id="action" name="action" value="save"/>
	<input type="hidden" name="field_sid" value="{$field_sid}"/>
	<input type="hidden" name="item_sid" value="{$item_sid}"/>
		<table>
			<tr>
				<td>[[Value]] </td>
                <td class="required">*</td>
				<td><input type="text" name="list_item_value" value="{$list_item_value}"/></td>
                <td>
                    <div class="floatRight">
                        <input type="submit" id="apply" value="[[Apply]]" class="greenButton"/>
                        <input type="submit" value="[[Save]]" class="greenButton"/>
                    </div>
                </td>
			</tr>
		</table>
	</form>
</fieldset>

<script type="text/javascript">
	$('#apply').click(
		function(){
			$('#action').attr('value', 'apply_info');
		}
	);
</script>