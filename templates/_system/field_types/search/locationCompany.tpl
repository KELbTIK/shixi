<fieldset>
{foreach from=$form_fields item=form_field}
		<div class="bcName">
			{tr}{$form_field.caption}{/tr|escape:'html'}
		</div>
		<div class="bcField">{search property=$form_field.id searchWithin=true template="location.like.tpl"}</div>
{/foreach}
</fieldset>
{assign var="parentID" value=false} 