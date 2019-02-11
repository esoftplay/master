<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$Content = _class('content');

$form = _lib('pea','bbc_content_trash');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title, image, params', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form = _lib('pea',  'bbc_content_trash' );
$form->initRoll( $add_sql, 'id' );

$form->roll->setSaveButton( 'submit_update', 'RESTORE' );

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'Title' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.trash&act=edit' );

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('image');
$form->roll->input->image->setCaption('image');
$form->roll->input->image->setFolder($Content->img_path);
$form->roll->input->image->setPlaintext( true );
$form->roll->input->image->setImageHover( true );

$form->roll->addInput( 'trashed', 'datetime' );
$form->roll->input->trashed->setTitle( 'trashed' );
$form->roll->input->trashed->setPlaintext( true );

$form->roll->addInput( 'restore', 'checkbox' );
$form->roll->input->restore->setTitle( 'Restore' );
$form->roll->input->restore->setCaption( 'restore' );

$form->roll->onDelete('content_trash_delete', $form->roll->getDeletedId(), false);
$form->roll->onSave('content_restore', $form, $loadLast = true);

echo $form->roll->getForm();