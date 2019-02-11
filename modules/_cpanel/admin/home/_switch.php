<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$Content = _class('content');

$config = get_config('content', 'frontpage');
$add_sql = $config['auto'] ? 'WHERE 1' : 'WHERE is_front=1';
$config['tot_list'] = is_numeric($config['tot_list']) ? $config['tot_list'] : 1;

$form = _lib('pea', 'bbc_content AS c LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND lang_id='.lang_id().')');
$form->initRoll( $add_sql.' ORDER BY id DESC', 'id' );
$form->roll->setNumRows($config['tot_list']);

$form->roll->addInput( 'id', 'sqlplaintext' );
$form->roll->input->id->setFieldName( 'id AS page_id' );

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('image');
$form->roll->input->image->setFolder($Content->img_path.'p_');
$form->roll->input->image->setPlaintext( true );
$form->roll->input->image->setImageClick( true );

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'Title' );
$form->roll->input->title->setLinks( 'index.php?mod=content.content_edit' );

$form->roll->addInput( 'created', 'texttip' );
$form->roll->input->created->setTitle( 'Date' );
$form->roll->input->created->setDateFormat();
$form->roll->input->created->setTemplate(table(
	array(
		'Author' => '{created_by_alias}',
		'Hit' => '{hits}',
		'Last Hit' => '{last_hits}',
		'Modified' => '{modified}',
		)
	));
$form->roll->input->hits->setNumberFormat();
$form->roll->input->last_hits->setDateFormat('M jS, Y H:i:s');
$form->roll->input->modified->setDateFormat('M jS, Y H:i:s');

$form->roll->addInput( 'created_by_alias', 'sqlplaintext' );
$form->roll->input->created_by_alias->setTitle( 'Author' );

$form->roll->addInput( 'publish', 'checkbox' );
$form->roll->input->publish->setTitle( 'Active' );
$form->roll->input->publish->setCaption( 'publish' );

$form->roll->onDelete('content_delete', $form->roll->getDeletedId(), false);

echo $form->roll->getForm();
