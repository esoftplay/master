<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

foreach((array)$dt AS $i => $r)
{
	if($i != 'notes')
	{
		foreach($r AS $demografi_id => $rd)
		{
			$q = "INSERT INTO `survey_posted_question`
					SET `posted_id`   = $posted_id
					, `question_id`   = $question_id
					, `question_title`= '".addslashes($rd['question'])."'
					, `option_ids`    = $demografi_id
					, `option_titles` = '".addslashes($rd['answer'])."'
					, `note`          = '".addslashes($dt['notes'])."'";
			$db->Execute($q);
		}
	}
}
