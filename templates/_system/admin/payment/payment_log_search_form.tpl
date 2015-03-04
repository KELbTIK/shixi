{breadcrumbs}[[Payment Log]]{/breadcrumbs}
<h1><img src="{image}/icons/notepencil32.png" border="0" alt="" class="titleicon"/>[[Payment Log]]</h1>

<div class="setting_button" id="mediumButton"><strong>[[Click to modify search criteria]]</strong>

	<div class="setting_icon">
		<div id="accordeonClosed"></div>
	</div>
</div>
<div class="setting_block" style="display: none" id="clearTable">
	<form method="get" name="search_form">
		<table width="100%" class="paymentLog">
			<tr>
				<td>[[Date]]:</td>
				<td>{search property="date"}</td>
			</tr>
			<tr>
				<td>[[Gateway]]:</td>
				<td>{search property="gateway"}</td>
			</tr>
			<tr>
				<td>[[Gateway Response]]:</td>
				<td>{search property="keywords" template="string.like.tpl"}</td>
			</tr>
			<tr>
				<td>[[Status]]:</td>
				<td>{search property="status"}</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<div class="floatRight">
						<input type="hidden" name="action" value="search"/>
						<input type="submit" value="[[Search]]" class="grayButton"/>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript">
	$(function () {

		var dFormat = '{$GLOBALS.current_language_data.date_format}';
		var message_click_to_hide = "[[Click to hide search criteria]]";
		var message_click_to_modify = "[[Click to modify search criteria]]";

		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");

		$("#date_notless, #date_notmore").datepicker({
			dateFormat:dFormat,
			showOn:'both',
			yearRange:'-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});


		$(".setting_button").click(function () {
			var butt = $(this);
			$(this).next(".setting_block").slideToggle("normal", function () {
				if ($(this).css("display") == "block") {
					butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
					butt.children("strong").text(message_click_to_hide);
				} else {
					butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
					butt.children("strong").text(message_click_to_modify);
				}
			});
		});

	});
</script>

