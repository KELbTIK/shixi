<?php

return array
(
	'display_name' => 'Applications',
	'description' => 'Job applications view',
	'classes' => 'classes/',
	'functions' => array
	(
		'view' => array
		(
			'display_name'	=> 'View applications',
			'script'		=> 'view.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		/**
		 * TODO: возможно, что это нигде не используется. проверить и удалить, если не нужно
		 */
		'edit' => array
		(
			'display_name'	=> 'Edit application',
			'script'		=> 'edit.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'screening_questionnaires' => array
		(
			'display_name'	=> 'Screening Questionnaires',
			'script'		=> 'screening_questionnaires.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'add_questionnaire' => array
		(
			'display_name'	=> 'Screening Questionnaires',
			'script'		=> 'add_questionnaire.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array ('action', 'template_name')
		),
		'add_questions' => array
		(
			'display_name'	=> 'Add New Question',
			'script'		=> 'add_questions.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'edit_questions' => array
		(
			'display_name'	=> 'Add New Question',
			'script'		=> 'edit_questions.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'edit_question' => array
		(
			'display_name'	=> 'Edit Question',
			'script'		=> 'edit_question.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'view_questionnaire' => array
		(
			'display_name'	=> 'view questionnaire',
			'script'		=> 'view_questionaire.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
