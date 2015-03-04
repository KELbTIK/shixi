{breadcrumbs}[[Invoices]]{/breadcrumbs}
<h1><img src="{image}/icons/paperstar32.png" border="0" alt="" class="titleicon"/>[[Invoices]]</h1>
<div class="clr"></div>

<p><a href="#" class="grayButton" id="create_invoice">[[Create Invoice]]</a></p>
<div id="invoice_search_criteria">
	<div class="setting_button" id="mediumButton"><strong>[[Click to modify search criteria]]</strong><div class="setting_icon"><div id="accordeonClosed"></div></div></div>
	<div class="setting_block" style="display: none"  id="clearTable">
		<form method="get" name="search_form">
			<table  width="100%" border="1">
				<tr><td>[[From]]</td><td>{search property="date" template="date.from.tpl"}&nbsp;[[To]]&nbsp;{search property="date" template="date.to.tpl"}</td></tr>
				<tr><td>[[Customer Name/Email]]</td><td >{search property="username" template="string.like.tpl"}</td></tr>
				<tr><td>[[Invoice]]#</td><td >{search property="sid"}</td></tr>
				<tr><td>[[Payment Method]]</td><td >{search property="payment_method"}</td></tr>
				<tr><td>[[Status]]</td><td >{search property="status"}</td></tr>
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
</div>
<div class="clr"></div>
<script>
	$( function () {
		var dFormat = '{$GLOBALS.current_language_data.date_format}';
			dFormat = dFormat.replace('%m', "mm");
			dFormat = dFormat.replace('%d', "dd");
			dFormat = dFormat.replace('%Y', "yy");

			$("#date_notless, #date_notmore").datepicker({
				dateFormat:dFormat,
				showOn:'both',
				yearRange:'-99:+99',
				buttonImage: '{image}icons/icon-calendar.png'
			});

			$("#invoice_search_criteria .setting_button").click(function(){
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

	$('#create_invoice').click(function(){
		var progBar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";
		$("#messageBox").dialog('destroy').html("[[Please wait ...]]" + progBar);
		$("#messageBox").attr({ title: "[[Choose User Group]]"});
		$("#messageBox").dialog({ width: 300});
		$.get("{$GLOBALS.site_url}/choose-user/", function(data){
			$("#messageBox").html(data);
		});
		return false;
	});
</script>
