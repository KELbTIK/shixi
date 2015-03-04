<script type="text/javascript">
	function windowMessage(message){
		$("#messageBox").dialog( 'destroy' ).html(message);
		$("#messageBox").dialog({
			width: 370,
			height: 170,
			title: '[[Error]]',
			buttons: {
				OK: function(){
					$(this).dialog('close');
				}
			}
			
		}).dialog( 'open' );
		
		return false;
	}

	function showHideFields(fieldShow, fieldHide) {
		var height = $("#"+fieldHide).height()+2;
		var width = $("#"+fieldHide).width();
		$("#"+fieldHide+"_div").css('display', 'block');
		$("#"+fieldHide+"_div").css('height', height);
		$("#"+fieldHide+"_div").css('width', width);
		$("#"+fieldHide+"_div").css('position', 'relative');
		$("#"+fieldHide+"_div").css('margin-top', -height);
		$("#"+fieldHide+"_div").css('opacity', '0.3');
		$("#"+fieldHide+"_div").css('background-color', '#EEEEEE');

		$("#"+fieldShow+"_div").css('display', 'none');
	}
</script>

{breadcrumbs}<a href="{$GLOBALS.site_url}/products/">[[Products]]</a> &#187; [[Clone product]]{/breadcrumbs}
<h1><img src="{image}/icons/paperplus32.png" border="0" alt="" class="titleicon"/>[[Clone Product]]</h1>
<div id="messageBox" style="display: none;"></div>

<div class="addProduct">
{include file="../users/field_errors.tpl"}

<form method="post" action="{$GLOBALS.site_url}/clone-product/" id="productForm">
	<input type="hidden" id="action" name="action" value="save" />
	<input type="hidden" id="sid" name="sid" value="{$product_info.sid}" />
	<input type="hidden" id="page" name="page" value="{if $pageTab}{$pageTab}{else}#generalTab{/if}"/>
	
	<div id="addProduct">
		<ul class="ui-tabs-nav">
			{foreach from=$pages item=page key=page_id name="pages"}
				<li class="ui-tabs-selected"><a href="#{$page_id}"><span>[[{$page.name}]]</span></a></li>
			{/foreach}
		</ul>
		{foreach from=$form_fields item=form_fields_info key=page_id}
			<div id="{$page_id}" class="ui-tabs-panel">
				<table class="basetable" width="100%">
				{foreach from=$form_fields_info item=form_field}
					{if $form_field.id == 'short_description'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[$form_field.caption]]</td>
							<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
							<td><div  class="productInputField">{input property=$form_field.id template="textarea.tpl"}</div>{if $form_field.comment}<small>[[{$form_field.comment}]]</small>{/if}</td>
						</tr>
					{elseif $form_field.id == 'availability_from'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[$form_field.caption]]</td>
							<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
							<td><div  class="productInputField">from {input property=$form_field.id} to {input property=availability_to}</div>{if $form_field.comment}<small>[[{$form_field.comment}]]</small>{/if}</td>
						</tr>
					{elseif $form_field.id == 'availability_to' || (($product_type == 'access_listings' || $product_type == 'featured_user' || $product_type == 'banners') && $form_field.id == 'pricing_type')}
					{* *}
					{elseif $form_field.id == 'expiration_period'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[Expire after]] {input property=$form_field.id} [[days]]</td>
						</tr>
					{elseif $product_type == 'post_listings' && $form_field.id == 'pricing_type'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td><input type="radio" name="pricing_type" value="fixed" onChange="showHideFields('fixed_pricing', 'volume_based_pricing')" {if $product_info.pricing_type == 'fixed'}checked="checked"{/if} />&nbsp;[[Fixed Pricing]]</td>
							<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
							<td>
								<table id="fixed_pricing">
					{elseif $form_field.id == 'volume_based_pricing'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td><input type="radio" name="pricing_type" value="volume_based" onChange="showHideFields('volume_based_pricing', 'fixed_pricing')" {if $product_info.pricing_type == 'volume_based'}checked="checked"{/if} />&nbsp;[[$form_field.caption]]</td>
							<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
							<td>
								<table id="volume_based_pricing">
									<tr>
										<td>{input property=$form_field.id template="volume_complex.tpl"}</td>
									</tr>
								</table>
						<div id="volume_based_pricing_div" style="display: none;"></div>
							{if $product_info.pricing_type == 'fixed' || !$product_info.pricing_type}
								<script>showHideFields('fixed_pricing', 'volume_based_pricing')</script>
							{elseif $product_info.pricing_type == 'volume_based'}
								<script>showHideFields('volume_based_pricing', 'fixed_pricing')</script>
							{/if}
							</td>
						</tr>
					{elseif $product_type == 'post_listings' && $form_field.id == 'renewal_price'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[$form_field.caption]]</td>
							<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
							<td><div  class="productInputField">{input property=$form_field.id}</div>{if $form_field.comment}<small>[[{$form_field.comment}]]</small>{/if}</td>
						</tr>
						</table>
						<div id="fixed_pricing_div" style="display: none;"></div>
						</td>
						</tr>
					{elseif $form_field.id == 'recurring'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td colspan="3">{input property=$form_field.id template="radiobuttons.tpl"}</td>
						</tr>
					{elseif $form_field.id == 'period'}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[$form_field.caption]]</td>
							<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
							<td><div  class="productInputField">{input property=$form_field.id} {input property=period_name}</div></td>
						</tr>
					{elseif $form_field.id == 'period_name'}
					{else}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[$form_field.caption]]</td>
							<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
							<td><div  class="productInputField">{input property=$form_field.id} {if $form_field.id == 'width' || $form_field.id == 'height'} [[pixels]]{/if}</div>{if $form_field.comment}<small>[[{$form_field.comment}]]</small>{/if}</td>
						</tr>
					{/if}
				{/foreach}
				</table>
			</div>
		{/foreach}
		<div class="clr"><br/></div>
		<div class="product-buttons"><input type="submit" class="grayButton" value="[[Save]]" id="saveProduct" /></div>
	</div>
</form>
</div>
<div id="periodMessage" style="display: none">[[If you want to set up and unlimited period please leave the 'Period' field blank and select 'Unlimited']]</div>


<script type="text/javascript">
	var total_pages={$smarty.foreach.pages.total};
	var user_group_sid = {$product_info.user_group_sid};
	var access_settins = false;
	{foreach from=$pages item=page key=page_id name="pages"}
		{if $page_id == "listings_access_settings"}
			access_settins = 1;
		{/if}
	{/foreach}
	$(document).ready(function(){
		$("#addProduct").tabs({
			select: function(event, ui) {
				currentPage = $('#page').val();
	        	if (currentPage == '#pricing') {
	        		var redirectPage = false; 
	                $('*[name=pricing_type]').each(function(){
	                    if (this.checked && this.value == 'volume_based')
	                    	redirectPage = validateVolumeBasedFields();
	                });
	                if (redirectPage) 
						return false;
	        	}
	        	$('#page').val($(ui.tab).attr('href'));
		    }
	    });
        for (var i=1; i<=total_pages; i++) 
    		$("#addProduct").tabs('disable', i);
	    if (user_group_sid)
	    	changePermissions(user_group_sid);
	});
    $('#apply').click(
         function(){
             $('#action').attr('value', 'apply_product');
         }
     );
	
    var page = '{$pageTab}';
    if (page) {
        $("#addProduct ul li").each(function(){
            if ($('a', this).attr('href') == page) {
                var cl = $(this).attr('class') + ' ui-tabs-selected';
                $(this).attr('class', cl);
            } else {
                $(this).attr('class', 'ui-tabs-unselect');
            }
        });
    }

    function validateVolumeBasedFields()
    {
        var pricing = [];
        var counter = 1;
        var line = 1;
		$('#complexFieldsVolume input').each(function(){
			if (counter == 1)
				pricing[line] = [];
			pricing[line][counter] = parseInt(this.value);
			if (counter == 4) {
				counter = 1;
				line++;
			}
			else
				counter++;
		});
		var fieldError = false;
		
		for (var i = 1; i < pricing.length; i++) {
			if (pricing[i][1] == '' || pricing[i][2] == '') {
				windowMessage('[[All the Qty fields in Volume Based Pricing should be filled]]');
				return '#pricing';
			}
			else if (pricing[i][1] > pricing[i][2]) {
				windowMessage('[[The Qty fields should be filled From: min To: max but not vice versa]]');
				return '#pricing';
			}
		}
    }
    function changePermissions(user_group_sid)
    {
        if (user_group_sid != 0) {
	        for (var i=1; i<=total_pages; i++) 
	    		$("#addProduct").tabs('enable', i);

	    	url = '{$GLOBALS.site_url}/product-permissions/'
			$.post(url, { 'user_group_sid': user_group_sid, 'product_type': '{$product_type}', 'permissions_type': 'additional', 'role': '{$product_info.sid}', 'params': '{$params}'},  function(data){
				$("#additional_permissions").html(data);
			});
			if (access_settins == 1) {
				$.post(url, { 'user_group_sid': user_group_sid, 'product_type': '{$product_type}', 'permissions_type': 'access', 'role': '{$product_info.sid}', 'params': '{$params}'},  function(data){
						$("#listings_access_settings").html(data);
				});
			}
        }
        else {
	        for (var i=1; i<=total_pages; i++) 
	    		$("#addProduct").tabs('disable', i);
        }
    }
	$(function() {
		$("#saveProduct").click(function(){
			return validatePeriod();
		});
	});

	function validatePeriod() {
		var period_name = $("#period_name").val();
		if (period_name == 'unlimited') {
			$("#period").attr('disabled', false);
			$("#period").val('');
		}
		return true;
	}
</script>