<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'agenda');
$form->initSearch();

$r_cat = agenda_cat();
$form->search->addInput('cat_id','select');
$form->search->input->cat_id->addOption('Select Type', '');
$form->search->input->cat_id->addOption($r_cat, array_keys($r_cat));

$form->search->addInput('start_date','dateinterval');
$form->search->input->start_date->setEndDateField('end_date');

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$tabs = array();
$form = _lib('pea', 'agenda AS a LEFT JOIN bbc_content_text AS t ON (t.content_id=a.content_id AND t.lang_id='.lang_id().')');
$form->initRoll("$add_sql ORDER BY `start_date` DESC", 'id');

$form->roll->addInput('title', 'sqllinks');
$form->roll->input->title->setTitle('Title');
$form->roll->input->title->setLinks($Bbc->mod['circuit'].'.agenda_edit');

$form->roll->addInput('cat_id', 'select');
$form->roll->input->cat_id->setTitle('Type');
foreach($r_cat AS $id => $title)
	$form->roll->input->cat_id->addOption($title, $id);
$form->roll->input->cat_id->setPlainText(true);

$form->roll->addInput('start_date','dateinterval');
$form->roll->input->start_date->setTitle('Date');
$form->roll->input->start_date->setEndDateField('end_date');
$form->roll->input->start_date->setPlainText(true);

$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Publish');

$form->roll->onDelete('agenda_delete');

$tabs['Agenda'] = $form->roll->getForm();
$tbl = array();
foreach($r_cat AS $id => $title) {
	$tbl[] = array('<a href="'.$Bbc->mod['circuit'].'.agenda_add&id='.$id.'&return='.urlencode(seo_uri()).'" title="Add '.$title.'">'.$title.'</a>');
}
$tabs['Add Agenda'] = table($tbl, array('Add New Agenda'));

echo tabs($tabs, 0);
