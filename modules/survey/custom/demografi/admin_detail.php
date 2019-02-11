<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*===============================================
 * START FORM ADD
 *==============================================*/
$id = @intval($id);
$form = _lib('pea', 'survey_questionary');
$form->initAdd();

$form->add->addInput('header','header');
$form->add->input->header->setTitle('Add Question');

$form->add->addInput('title','text');
$form->add->input->title->setTitle('Question');
$form->add->input->title->setSize( 60 );

$form->add->addInput('question_id','hidden');
$form->add->input->question_id->setDefaultValue($id);

$form->add->addInput('publish','checkbox');
$form->add->input->publish->setTitle('Publish');
$form->add->input->publish->setCaption('Actived');
$form->add->input->publish->setDefaultValue(1);

$form->add->onSave('survey_questionary_add');
$form->add->action();
function survey_questionary_add($id)
{
	global $db;
	if($id > 0)
	{
		$question_id = @intval($_GET['id']);
		$q = "SELECT COUNT(*) FROM survey_questionary WHERE question_id=$question_id";
		$orderby = $db->getOne($q);
		$q = "UPDATE survey_questionary SET orderby=$orderby WHERE id=$id";
		$db->Execute($q);
	}
}

/*===============================================
 * START LISTING
 *==============================================*/

$form->initRoll("WHERE question_id=$id ORDER BY orderby ASC", 'id' );

$form->roll->addInput('title','text');
$form->roll->input->title->setTitle('Question');
$form->roll->input->title->setSize(60);

$form->roll->addInput('orderby','orderby');
$form->roll->input->orderby->setTitle('Orderby');

$form->roll->addInput('publish','checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Actived');

$tabs = array(
	'Question'=> $form->roll->getForm(),
	'Add'			=> $form->add->getForm()
);

function questionary_option($opti, $c)
{
	global $dt;
	$r = explode('<br />', $opti);
	foreach($r AS $d)
	{
		preg_match('~^([0-9]+)~is', $d, $m);
		if(isset($m[1]) && isset($dt[$m[1]]))
		{
			$dt[$m[1]]++;
		// }else{
		// 	$dt[$m[1]] = 0;
		}
	}
}

$q   = "SELECT `question_title`, `option_titles` FROM `survey_posted_question` WHERE `question_id`={$id}";
$arr = $db->getAll($q);
if(count($arr) > 0)
{
	$c = array(
		1 => lang('Sangat Tidak Setuju'),
		2 => lang('Tidak Setuju'),
		3 => lang('Netral'),
		4 => lang('Setuju'),
		5 => lang('Sangat Setuju')
	);
	ob_start();
	foreach ($arr as $data)
	{
		$r_q = explode('<br />', $data['question_title']);
		$r_a = explode('<br />', $data['option_titles']);
		foreach($r_q AS $j => $q)
		{
			echo '<b>'.$q.'</b><br />';
			$dt = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
			questionary_option($r_a[$j], $c);
			$chd = $chl = array();
			$total = array_sum($dt);
			if ($total > 0)
			{
				foreach($dt AS $i => $voted)
				{
					if($voted > 0) $voted = round($voted / $total * 100, 2);
					else $voted = 0;
					$chd[] = $voted;
					$chl[] = $c[$i].' ('.$voted.' %)';
				}
				$img_url = 'http://chart.apis.google.com/chart?cht=p3&chs=460x100&chd=t:'.urlencode(implode(',', $chd)).'&chl='.urlencode(implode('|', $chl));

				echo '<p><img src="'.$img_url.'" border=0></p>';
			}
		}
	}
	$tabs['Report'] = ob_get_contents();
	ob_end_clean();
}
echo '<br />'.tabs($tabs,1,'questionary_'.$id);
