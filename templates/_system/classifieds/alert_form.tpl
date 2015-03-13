{foreach from=$errors item=message key=error}
	{if $error eq 'EMPTY_VALUE'}
		<div class="error alert alert-danger">'[[Alert Name]]' [[is empty]]</div>
	{else}
		<div class="error alert alert-danger">[[{$error}]]</div>
	{/if}
{/foreach}
<div class="page-top">
    <div class="form-block center-block">
        <h2>
            {if $action == 'save'}[[Add new {$listing_type_id|strtolower} alert]]{elseif $action == 'edit'}[[Edit {$listing_type_id|strtolower} alert]]{/if}
        </h2>
        <hr/>
        <form action="" method="post" id="search_form" onsubmit="disableSubmitButton('submitSave');" class="form-horizontal">
            <input type="hidden" name="action" value="{$action|htmlspecialchars}" />
            <input type="hidden" name="listing_type[equal]" value="{$listing_type_id}" />
            {if $action == 'edit'}<input type="hidden" name="id_saved" value="{$id_saved}" />{/if}
            <div class="form-group">
                <label class="inputName control-label col-sm-3">[[Alert Name]]:</label>
                <div class="inputField col-sm-8">{search property=name template='string.tpl'}</div>
            </div>
            {include file="../builder/bf_searchform_fieldsholders.tpl"}
            <div class="form-group">
                <label class="inputName control-label col-sm-3">[[Email frequency]]</label>
                <div class="inputField col-sm-8">
                    {search property="email_frequency"}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8 col-sm-offset-3">
                    <input type="button" value="[[Back]]" class="button btn btn-default" onclick="history.back()"/>
                    <input type="submit" name="submit" value="[[Save]]" id="submitSave" class="button btn btn-success" />
                </div>
            </div>
        </form>
        <div class="clearfix"></div>
    </div>
</div>