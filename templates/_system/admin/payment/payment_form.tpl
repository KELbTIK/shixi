{breadcrumbs}[[Transaction History]]{/breadcrumbs}
<h1><img src="{image}/icons/moneyreceipt32.png" border="0" alt="" class="titleicon"/>[[Transaction History]]</h1>
<div class="setting_button" id="mediumButton"><strong>[[Click to modify search criteria]]</strong><div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div class="setting_block" style="display: none"  id="clearTable">
    <form method="post" name="search_form">
        <table width="100%" border="1">
            <tr><td>[[Transaction Id]]</td><td>{search property='transaction_id'}</td></tr>
            <tr><td>[[Invoice]]#</td><td>{search property='invoice_sid'}</td></tr>
            <tr><td>[[Period]]</td><td>{search property="date" template="date.from.tpl"}&nbsp;[[To]]&nbsp;{search property="date" template="date.to.tpl"}</td></tr>
            <tr><td>[[Username]]</td><td>{search property='username' template='string.like.tpl'}</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <div class="floatRight">
                        <input type="hidden" name="action" value="filter" />
                        <input type="hidden" name="page" value="1" />
                        <input type="submit" value="[[Search]]" class="grayButton" />
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
<div class="clr"></div>
<script>
    $( function () {ldelim}
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
	{rdelim});
</script>