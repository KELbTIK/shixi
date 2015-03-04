{breadcrumbs}[[Manage Guest Alerts]]{/breadcrumbs}
<h1><img src="{image}users-online.png" border="0" alt="" class="titleicon" />[[Manage Guest Alerts]]</h1>

<div class="setting_button" id="mediumButton">
	<strong>[[Click to modify search criteria]]</strong>
	<div class="setting_icon"><div id="accordeonClosed"></div></div>
</div>

<div class="setting_block" style="display: none"  id="clearTable">
	<form method="get" name="search_form">
		<table  width="100%">
			<tr><td>[[Guest Alert ID]]:</td><td>{search property="sid"}</td></tr>
			<tr><td>[[Email]]:</td><td>{search property="email" template="string.like.tpl"}</td></tr>
			<tr><td>[[Email frequency]]:</td><td>{search property="email_frequency"}</td></tr>
			<tr><td>[[Subscription date]]:</td><td>{search property="subscription_date"}</td></tr>
			<tr><td>[[Status]]:</td><td>{search property="active"}</td></tr>
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
		
		$("#subscription_date_notless, #subscription_date_notmore").datepicker({
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

