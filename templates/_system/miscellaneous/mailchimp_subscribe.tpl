<div id="mailchimp-newsletter">
    <h1>[[Newsletter]]</h1>
    <div id="mailchimp-nl-cont">
        {if $error}
            <div class="error alert alert-danger">
                {if $error eq 'EMPTY_FIELD'}
                    [[All fields are required!]]
                {else}
                    [[{$error}]]
                {/if}
            </div>
        {/if}
        {if $message}
            <div class="message alert alert-info">[[{$message}]]</div>
        {/if}
        <form action="{$GLOBALS.site_url}/system/miscellaneous/mailchimp/" method="get" id="mailchimp-form" class="form-horizontal">
            <p class="mailchimp-nl-desc">[[Fill the form to subscribe]]</p>
            <div class="form-group">
                <label for="mch_name" class="control-label col-sm-3">[[Your name]]:</label>
                <div class="col-sm-8">
                    <input type="text" name="mch_name" id="mch_name" class="form-control" >
                </div>
            </div>
            <div class="form-group">
                <label for="mch_email" class="control-label col-sm-3">[[Email]]:</label>
                <div class="col-sm-8">
                    <input type="text" name="mch_email" id="mch_email" class="form-control" >
                </div>
            </div>
            <input type="submit" name="subscribe" value="Subscribe" id="mch_subscribe" class="btn btn-group btn-default btn-sm">
        </form>
    </div>
</div>

<script type="text/javascript">
    {literal}
    $(document).ready(function(){
        $("#mch_subscribe").on("click", function(){
            var oEmail = $("#mch_email");
            var oName = $("#mch_name");
            var email = oEmail.val();
            var name = oName.val();
            var error = false;
            if (!email || !name){
                error = true;
            }
            if (!error) {
                var content = $("#mailchimp-nl-cont");
                content.html("<img src=\"{/literal}{$GLOBALS.site_url}{literal}/templates/_system/main/images/ajax_preloader_circular_32.gif\" />")
                        .css("text-align", "center");
                $.ajax({
                    url: '{/literal}{$GLOBALS.site_url}{literal}/system/miscellaneous/mailchimp/',
                    type: "GET",
                    data: "mch_name="+name+"&mch_email="+email+"&subscribe=1",
                    success: function(data) {
                        content.html($(data).find("#mailchimp-nl-cont"));
                    }
                });
            }
            return false;
        });
    });
    {/literal}
</script>

