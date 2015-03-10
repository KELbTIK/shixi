

<div class="col-sm-8">
    {if $id_saved}<h1>[[Edit Saved Search]]</h1>{else}<h1>[[Search Resumes]]</h1>{/if}
    {if $id_saved}
        <form action="{$GLOBALS.site_url}/saved-searches/" method="get"  id="search_form">
            <input type="hidden" name="action" value="{$action}" />
            <input type="hidden" name="id_saved" value="{$id_saved}" />
    {else}
        <form action="{$GLOBALS.site_url}/search-results-resumes/"  id="search_form">
            <input type="hidden" name="action" value="search" />
    {/if}
        <input type="hidden" name="listing_type[equal]" value="Resume" />
        <div id="adMargin">
            {if $id_saved}
                <div class="form-group">
                    <label class="inputName">[[Search Name]]</label>
                    <div class="inputField">{search property=name template='string.tpl'}</div>
                </div>
            {/if}

            {include file="../builder/bf_searchform_fieldsholders.tpl"}

            <label class="inputName">&nbsp;</label>
            <div class="inputField">
                {if $id_saved}
                    <input class="button btn btn-default" type="submit" name="submit" value="[[Save]]" id="search_button" />
                {else}
                    <input type="submit" value="[[Search]]" class="button btn btn-default" id="search_button" />
                {/if}
            </div>
        </div>
    </form>
    <div id="adSpace">{module name="static_content" function="show_static_content" pageid="SearchResumesAdSpace"}</div>
</div>