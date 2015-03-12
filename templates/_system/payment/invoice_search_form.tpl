<div class="page-top">
    <div class="form-block center-block">
        <h2>[[My Invoices]]</h2>
        <hr/>
        <form method="get" name="search_form" class="invoice-form form-horizontal" action="">
            <div class="form-group has-feedback">
                <label class="inputName col-sm-3 control-label">[[Invoice]]&nbsp;&#35;</label>
                <div class="inputField col-sm-8">{search property="sid"}</div>
            </div>
            <div class="form-group has-feedback">
                <label class="inputName col-sm-3 control-label">[[From]]</label>
                <div class="inputField col-sm-8">{search property="date"}</div>
            </div>
            <div class="form-group has-feedback">
                <label class="inputName col-sm-3 control-label">[[Status]]</label>
                <div class="inputField col-sm-8">{search property="status"}</div>
            </div>
            <div class="form-group">
                <input type="hidden" name="action" value="search" />
                <div class="inputField col-sm-8 col-sm-offset-3"><input type="submit" value="Search" class="btn btn-gray" /></div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
	$(function () {ldelim}
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	{literal}
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");

		$("#date_notless, #date_notmore").datepicker({dateFormat: dFormat, showOn: 'button', yearRange: '-99:+99', buttonImage: '{/literal}{$GLOBALS.site_url}/system/ext/jquery/calendar.gif{literal}', buttonImageOnly: true });

	{/literal}
	{rdelim});
</script>
