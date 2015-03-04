{if $educations}
	<span class="strong">[[Education]]</span>
	{foreach from=$educations item="education"}
	<p>
		<p><span class="strong">{$education.school}</span></p>
		<p>{$education.year}</p>
		<p>{$education.type}</p>
		{if $education.concentration}
		<p>[[Concentration:]]
			{foreach from=$education.concentration item="concentration"}
				{* possible keys of $concentration: 'id', 'name' *}
				{$concentration.name}&nbsp;
			{/foreach}
		</p>
		{/if}
		{if $education.classes}
		<p>[[Classes:]]
			{foreach from=$education.classes item="class"}
				{* possible keys of $class: 'id', 'name', 'description' *}
				{$class.name}<br/>
				{$class.description}<br/><br/>
			{/foreach}
		</p>
		{/if}
	</p>
	{/foreach}
	<p>&nbsp;</p>
{/if}