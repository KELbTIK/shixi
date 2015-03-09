<div class="row grid-space-20">
	{foreach from=$profiles item=profile name=profile_block}
        <div class="col-md-3">
            <div class="image-box mb-20 object-non-visible" data-animation-effect="fadeInLeft" data-effect-delay="300">
                <div class="overlay-container">
                    <img class="img-responsive center-block" src="{$profile.Logo.thumb_file_url}" alt="{$profile.WebSite}">
                    <a href="{$GLOBALS.site_url}/company/{$profile.id}/{$profile.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/" class="overlay small">
                        <i class="fa fa-link"></i>
                        {*<span>Web Design</span>*}
                    </a>
                </div>
                <a href="{$GLOBALS.site_url}/company/{$profile.id}/{$profile.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/" class="btn btn-light-gray btn-lg btn-block">{$profile.WebSite}</a>
            </div>
            {if $smarty.foreach.profile_block.iteration is div by $number_of_cols}
            {/if}
        </div>
	{/foreach}




</div>