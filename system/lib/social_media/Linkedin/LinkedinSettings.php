<?php
/*
 * $key  =  SJB field ID
 * 
 * $valu =  LinkedIn profile field
 */

/*
 * fields:
 * http://developer.linkedin.com/docs/DOC-1061
 */

return array(
	'JobCategory'		=> $oLF->get_Industry('array', 'array', 'JobCategory'),
	'Occupations'		=> $oLF->get_Specialties('array', 'tree', 'Occupations'),
	'Country'			=> $oLF->get_Country('string', 'Country'),
	'Objective'			=> $oLF->get_Summary(),
//	'Skills'			=> '<p>'.$oLF->get_Summary() . '</p><p>'. $oLF->get_Educations() . '</p><p>' . $oLF->get_Positions().'</p>',
	'Title'				=> $oLF->get_Positions_Position_Title(),
//	'Education'			=> $oLF->get_Educations(),
	'Education'			=> $oLF->get_EducationsArr(array(
		'start-date' => 'EntranceDate',
		'end-date' => 'GraduationDate',
		'school-name' => 'InstitutionName',
		'field-of-study' => 'Major',
//		'DegreeLevel',
		)
	),
//	'WorkExperience'	=> $oLF->get_Positions(),
	'WorkExperience'	=> $oLF->get_PositionsArr(array(
		'start-date' => 'StartDate',
		'end-date' => 'EndDate',
		'title' => 'JobTitle',
		'company-name' => 'CompanyName',
		'company-industry' => 'Industry',
		'summary' => 'Description',
		)
	),
	
);
