<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Detail Question');
$id = @intval($_GET['id']);

$form1 = _lib('pea', 'survey_question');
$form1->initEdit('WHERE id='.$id, 'id');
$form1->edit->setLanguage('question_id');


$form1->edit->addInput('header','header');
$form1->edit->input->header->setTitle('Edit Question');

$form1->edit->addInput('title','text');
$form1->edit->input->title->setTitle('Question');
$form1->edit->input->title->setSize( 60 );
$form1->edit->input->title->setLanguage( true );

$form1->edit->addInput('description','textarea');
$form1->edit->input->description->setTitle('Description');
$form1->edit->input->description->setSize(4, 80);
$form1->edit->input->description->setLanguage( true );

$form1->edit->addInput('type','select');
$form1->edit->input->type->setTitle('Type');
$form1->edit->input->type->addOptionArray(array('checkbox','multiple','radio','select','text','none','custom'));

$form1->edit->addInput('file','text');
$form1->edit->input->file->setTitle('Path');
$form1->edit->input->file->addTip('insert directory name if you select custom as Type');

$form1->edit->addInput('voted','sqlplaintext');
$form1->edit->input->voted->setTitle('Voted');

$form1->edit->addInput('is_note','checkbox');
$form1->edit->input->is_note->setTitle('Note');
$form1->edit->input->is_note->setCaption('Use Note');

$form1->edit->addInput('checked','checkbox');
$form1->edit->input->checked->setTitle('Checked');
$form1->edit->input->checked->setCaption('Checked');
$form1->edit->input->checked->addTip('automatically checked if this option is displayed');

$form1->edit->addInput('publish','checkbox');
$form1->edit->input->publish->setTitle('Publish');
$form1->edit->input->publish->setCaption('Actived');

$form1->edit->action();
$q = "SELECT type FROM survey_question WHERE id=$id";
$type = $db->getOne($q);
if(!$db->Affected_rows()) redirect($Bbc->mod['circuit'].'.question');
$is_option = true;
$q = "SELECT * FROM `survey_question` WHERE id=$id";
if($type == 'custom')
{
	$q = "SELECT file FROM survey_question WHERE id=$id";
	$path = $db->getOne($q);
	$path = dirname($Bbc->mod['root'])."/custom/{$path}/admin_detail.php";
	if(is_file($path))
	{
		echo $form1->edit->getForm();
		chdir(dirname($path));
		include $path;
		$is_option = false;
	}
}

if($is_option)
{
	/*===============================================
	 * START FORM ADD
	 *==============================================*/
	$form = _lib('pea', 'survey_question_option');

	$form->initAdd();
	$form->add->setLanguage( 'option_id' );

	$form->add->addInput('header','header');
	$form->add->input->header->setTitle('Add Option');

	$form->add->addInput('title','text');
	$form->add->input->title->setTitle('Option');
	$form->add->input->title->setSize( 30 );
	$form->add->input->title->setLanguage();

	$form->add->addInput('question_id','hidden');
	$form->add->input->question_id->setDefaultValue($id);

	$form->add->addInput('voted','hidden');
	$form->add->input->voted->setDefaultValue(0);

	$form->add->addInput('checked','checkbox');
	$form->add->input->checked->setTitle('Checked');
	$form->add->input->checked->setCaption('Checked');
	$form->add->input->checked->addTip('automatically checked if this option is displayed');

	$form->add->addInput('publish','checkbox');
	$form->add->input->publish->setTitle('Publish');
	$form->add->input->publish->setCaption('Actived');
	$form->add->input->publish->setDefaultValue(1);

	$form->add->onSave('_option_add');
	$form->add->action();

	/*===============================================
	 * START LISTING
	 *==============================================*/
	#$form = _lib('pea',  'survey_question_option' );

	$form->initRoll("WHERE question_id=$id ORDER BY orderby ASC", 'id' );
	$form->roll->setLanguage( 'option_id' );

	$form->roll->addInput('title','text');
	$form->roll->input->title->setTitle('Option');
	$form->roll->input->title->setSize(30);
	$form->roll->input->title->setLanguage();

	$form->roll->addInput('voted','sqlplaintext');
	$form->roll->input->voted->setTitle('voted');

	$form->roll->addInput('orderby','orderby');
	$form->roll->input->orderby->setTitle('Orderby');

	$form->roll->addInput('checked','checkbox');
	$form->roll->input->checked->setTitle('Checked');
	$form->roll->input->checked->setCaption('Checked');

	$form->roll->addInput('publish','checkbox');
	$form->roll->input->publish->setTitle('Publish');
	$form->roll->input->publish->setCaption('Actived');

	#$form->roll->onDelete('_option_delete', $form->roll->getDeletedId(), false);

	$tabs = array(
	  'Chart' => ''
	, 'Options'=> $form->roll->getForm()
	, 'Add'		=> $form->add->getForm()
	);

	/*===============================================
	 * START Chart
	 *==============================================*/
	$q = "SELECT SUM(voted) FROM survey_question_option WHERE question_id=$id";
	$total = $db->getOne($q);
	if($total > 0)
	{
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
					$chd[]= $voted;
					$chl[]= $i;
					$li[]	= '<li>'.$d['title'].' ('.$d['voted'].' voters)</li>';
				}
				$img_url			= 'http://chart.apis.google.com/chart?cht=bvs&chs=320x200&chd=t:'.urlencode(implode(',', $chd)).'&chl='.urlencode(implode('|', $chl)).'&chxt=y';
				$tabs['Chart']= '<p style="float: left;"><img src="'.$img_url.'" border=0></p><ol style="float: left;font-weight: bold;">'.implode('', $li).'</ol><div class="clearfix"></div>';
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
				$img_url       = 'http://chart.apis.google.com/chart?cht=p3&chs=350x100&chd=t:'.urlencode(implode(',', $chd)).'&chl='.urlencode(implode('|', $chl));
				$tabs['Chart'] = '<p><img src="'.$img_url.'" border=0></p>';
			break;
			default:
				unset($tabs['Chart']);
			break;
		}
	}else unset($tabs['Chart']);

	$button = '';
	if(isset($tabs['Chart']))
	{
		ob_start();
		include 'question_report.php';
		$tabs['Report'] = ob_get_contents();
		ob_end_clean();
		$button .= '<input type="button" value="RESET QUESTION" title="Reset this question and delete all voters" onclick="document.location.href=\''.$Bbc->mod['circuit'].'.question_reset&id='.$id.'&return='.urlencode(seo_url()).'\'" class="btn btn-sm btn-danger">';
	}

	echo $form1->edit->getForm().'<div class="clearfix"></div>'.tabs($tabs).$button;
}
function _option_add($id)
{
  global $db;
  if($id > 0)
  {
    $question_id = @intval($_GET['id']);
    $q = "SELECT COUNT(*) FROM survey_question_option WHERE question_id=$question_id";
    $orderby = $db->getOne($q);
    $q = "UPDATE survey_question_option SET orderby=$orderby WHERE id=$id";
    $db->Execute($q);
  }
}
function _question_delete($ids)
{
  global $db;
  if(count($ids) > 0)
  {
    $q = "DELETE FROM survey_question_option WHERE id IN(".implode(',', $ids).")";
    $db->Execute($q);
    $q = "DELETE FROM survey_question_option_text WHERE question_id IN(".implode(',', $ids).")";
    $db->Execute($q);

    $q = "SELECT id FROM survey_question_option WHERE question_id IN(".implode(',', $ids).")";
    $q_ids = $db->getCol($q);
    if(count($q_ids) > 0)
    {
      $q = "DELETE FROM survey_question_option WHERE id IN(".implode(',', $q_ids).")";
      $db->Execute($q);
      $q = "DELETE FROM survey_question_option_text WHERE option_id IN(".implode(',', $q_ids).")";
      $db->Execute($q);
    }
  }
}

