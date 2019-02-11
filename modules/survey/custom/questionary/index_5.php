<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(!function_exists('questionary_title'))
{
	function questionary_title($opti)
	{
		$output = array();
		$r = array(
			1 => lang('Sangat Tidak Setuju')
		,	2 => lang('Tidak Setuju')
		,	3 => lang('Netral')
		,	4 => lang('Setuju')
		,	5 => lang('Sangat Setuju')
		);
		foreach($opti AS $i)
		{
			if(!empty($r[$i]))
			{
				$output[] = $i.'.'.$r[$i];
			}
		}
		return implode('<br />', $output);
	}
}
foreach((array)$dt AS $i => $r)
{
	if($i != 'notes')
	{
		$option_ids		= implode(',', array_flip($r));
		$option_titles= questionary_title($r);
		$q = "SELECT title FROM `survey_questionary` WHERE `question_id`=$question_id AND `publish`=1 ORDER BY `orderby` ASC";
		$question_title = implode('<br />', $db->getCol($q));
		$q = "INSERT INTO `survey_posted_question`
					SET `posted_id`   = $posted_id
					, `question_id`   = $question_id
					, `question_title`= '$question_title'
					, `option_ids`    = '$option_ids'
					, `option_titles` = '$option_titles'
					, `note`          = '".addslashes($dt['notes'])."'";
		$db->Execute($q);
	}
}