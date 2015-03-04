{breadcrumbs}[[Manage {if $userGroupInfo.id == 'Employer' || $userGroupInfo.id == 'JobSeeker'}{$userGroupInfo.name}s{else}'{$userGroupInfo.name}' Users{/if}]]{/breadcrumbs}
<h1><img src="{image}users-online.png" border="0" alt="" class="titleicon" />[[Manage {if $userGroupInfo.id == 'Employer' || $userGroupInfo.id == 'JobSeeker'}{$userGroupInfo.name}s{else}'{$userGroupInfo.name}' Users{/if}]]</h1>
<p><a href="{$GLOBALS.site_url}/add-user/{$userGroupInfo.id|lower}" class="grayButton">[[Add a New {$userGroupInfo.name}]]</a></p>

<div class="setting_button" id="mediumButton"><strong>[[Click to modify search criteria]]</strong><div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div class="setting_block" style="display: none"  id="clearTable">
	<form method="get" name="search_form">
		<table  width="100%">
			<tr><td>[[User ID]]:</td><td>{search property="sid"}</td></tr>
			<tr><td>[[Username]]:</td><td>{search property="username" template="string.like.tpl"}</td></tr>
			{if $userGroupInfo.id == 'Employer'}
               <tr><td>[[Company Name]]:</td><td>{search property="CompanyName" template="string.like.tpl"}</td></tr>
            {elseif $userGroupInfo.id == 'JobSeeker'}
				<tr><td>[[First/Last Name]]:</td><td>{search property="FirstName"}</td></tr>
            {/if}
		    <tr><td>[[Email]]:</td><td>{search property="email" template="string.like.tpl"}</td></tr>
		    <tr><td>[[Registration Date]]:</td><td>{search property="registration_date"}</td></tr>
			<tr>
				<td>[[Product]]:</td>
				<td><select name="product[simple_equal]">
						<option value="">[[Any]]</option>
					{foreach from=$products item=product}
						<option value="{$product.sid}" {if $selectedProduct eq $product.sid}selected="selected"{/if}>[[{$product.name}]]</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr><td>[[Status]]:</td><td>{search property="active"}</td></tr>
			<tr><td>[[Online]]:</td><td><input type="checkbox" value="1" name="online" {if $online}checked="checked"{/if} /></td></tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<div class="floatRight">
						<input type="hidden" name="action" value="search" />
						<input type="hidden" name="page" value="1" />
						<input type="submit" value="[[Search]]" class="grayButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
	$( function () {
	
		var dFormat = '{$GLOBALS.current_language_data.date_format}';
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");
		
		$("#registration_date_notless, #registration_date_notmore").datepicker({
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
					butt.children("strong").text("[[Click to hide search criteria]]");
				} else {
					butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
					butt.children("strong").text("[[Click to modify search criteria]]");
				}
			});
		});

	
	});
</script>

