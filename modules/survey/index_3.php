<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// CHECK SESSION...
if(!isset($sess['index']) || empty($sess['index'])) redirect($Bbc->mod['circuit']);
if(!isset($sess['index_2']) || empty($sess['index_2'])) redirect($Bbc->mod['circuit'].'.index_2');

$ids = $sess['index'];
$q = "SELECT q.id, q.*, t.* FROM survey_question AS q LEFT JOIN survey_question_text AS t
		ON (q.id=t.question_id AND t.lang_id=".lang_id().")
		WHERE q.id IN(".implode(',', $ids).") AND q.type='custom' ORDER BY orderby";
$question = $db->getAssoc($q);
$valid		= false;
foreach((array)$question AS $d)
{
	if(file_exists(survey_path($d['file'],3)))
	{
		$valid = true;
	}
}
if(!$valid) redirect($Bbc->mod['circuit'].'.index_4');

// START ACTION...
if(isset($_POST['Submit']) && !empty($_POST['Submit']))
{
	$is_error = false;
	$input		= array();
	foreach((array)$question AS $d)
	{
		if(is_file(survey_path($data['file'],3, '-action')))
		{
			include survey_path($data['file'],3, '-action');
		}
	}
	if(!$is_error) redirect($Bbc->mod['circuit'].'.index_4');
	else survey_sess('index_3', $input);
}

// DISPLAY FORM...
?>
<form action="" method="post">
<?php
foreach($question AS $id => $data)
{
?>
	<div class="formItem">
<?php		if(is_file(survey_path($data['file'],3)))
		{
			include survey_path($data['file'],3);
		}
?>
	</div>
<?php
}
if(!$valid) redirect($Bbc->mod['circuit'].'.index_4');
?>
	<p class="button">
		<input type="Button" value="&#171; Back" class="btn" onClick="window.history.go(-1);" />
		<input type="submit" name="Submit" value="Next &#187;" class="btn" />
	</p>
</form>
