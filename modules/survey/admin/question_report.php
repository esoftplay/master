<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);

$form = _lib('pea',  'survey_posted AS p LEFT JOIN survey_posted_question AS q ON (p.id=q.posted_id)' );

$form->initRoll("WHERE question_id=$id", 'posted_id' );
$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);
$form->roll->addReport();
$form->roll->setFormName('report');

$form->roll->addInput('name','sqllinks');
$form->roll->input->name->setTitle('Name');
$form->roll->input->name->setLinks($Bbc->mod['circuit'].'.posted_detail');

$form->roll->addInput('email','sqlplaintext');
$form->roll->input->email->setTitle('Email');

$form->roll->addInput('option_titles','sqlplaintext');
$form->roll->input->option_titles->setTitle('Answer');

$form->roll->addInput('note','sqlplaintext');
$form->roll->input->note->setTitle('Notes');

if($Bbc->mod['task'] == 'question_report')
{
	$tabs = array(
		'Chart'  => '',
		'Report' => $form->roll->getForm()
	);
	$q = "SELECT COUNT(*) FROM survey_posted_question WHERE question_id=$id";
	$total = $db->getOne($q);
	if($total > 0)
	{
		$q = "SELECT type FROM survey_question WHERE id=$id";
		$type = $db->getOne($q);
		$q = "SELECT * FROM survey_question_option AS o LEFT JOIN survey_question_option_text AS t
		ON(o.id=t.option_id AND t.lang_id=".lang_id().") WHERE question_id=$id ORDER BY o.orderby ASC";
		$r = $db->getAll($q);
		switch($type)
		{
			case'checkbox':
			case'multiple':
				$chd = $chl = $li = array();
				$i = 0;
				foreach($r AS $d)
				{
					if($d['voted'] > 0) $voted = intval($d['voted'] / $total * 100);
					else $voted = 0;
					$i++;
					$chd[] = $voted;
					$chl[] = $i;
					$li[]  = '<li>'.$d['title'].' ('.$d['voted'].' voters)</li>';
				}
				$img_url			= 'http://chart.apis.google.com/chart?cht=bvs&chs=320x200&chd=t:'.urlencode(implode(',', $chd)).'&chl='.urlencode(implode('|', $chl)).'&chxt=y';
				$tabs['Chart']= '<p style="float: left;"><img src="'.$img_url.'" border=0></p><ol style="float: left;font-weight: bold;">'.implode('', $li).'</ol><div class="clear"></div>';
			break;
			case'radio':
			case'select':
				$chd = $chl = array();
				foreach($r AS $d)
				{
					if($d['voted'] > 0) $voted = round($d['voted'] / $total * 100, 2);
					else $voted = 0;
					$chd[] = $voted;
					$chl[] = $d['title'].' ('.$voted.' %)';
				}
				$img_url			= 'http://chart.apis.google.com/chart?cht=p3&chs=350x100&chd=t:'.urlencode(implode(',', $chd)).'&chl='.urlencode(implode('|', $chl));
				$tabs['Chart']= '<p><img src="'.$img_url.'" border=0></p>';
			break;
			default:
				unset($tabs['Chart']);
			break;
		}
	}else unset($tabs['Chart']);
	echo tabs($tabs);
}else{
	echo $form->roll->getForm();
}