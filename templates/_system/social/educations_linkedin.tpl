{if $educations}
	<span class="strong">[[Education]]</span>
	{foreach from=$educations item="education"}
		<p><span class="strong">{$education.school_name}</span></p>
		<p>{$education.degree}, {$education.field_of_study}</p>
		<p>{$education.start_date} - {$education.end_date}</p>
		<p>{$education.notes}</p>
		<p>[[Activities and Societies:]] {$education.activities}</p>
	{/foreach}
	<p>&nbsp;</p>
{/if}