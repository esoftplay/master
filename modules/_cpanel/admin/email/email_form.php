<?php if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
// JS di bawah saya mark karena bikin rusak html nya
// $sys->link_js(_URL.'includes/lib/ckeditor/ckeditor.js');
// $sys->link_js(_URL.'modules/_cpanel/admin/email/email_form.js');

$where = $id ? 'WHERE id='.$id : '';
$title = $id ? 'Edit Email Template' : 'Add Email Template';

$form1 = _lib('pea', 'bbc_email');
$form1->initEdit($where);
$form1->edit->setLanguage('email_id');

$form1->edit->addInput('header', 'header');
$form1->edit->input->header->setTitle($title);

$form1->edit->addInput('module_id', 'selecttable');
$form1->edit->input->module_id->setTitle('Modules');
$form1->edit->input->module_id->setReferenceTable('bbc_module ORDER BY name');
$form1->edit->input->module_id->setReferenceField('name', 'id');
$form1->edit->input->module_id->setDefaultValue(@intval($keyword['module_id']));

$form1->edit->addInput('name', 'text');
$form1->edit->input->name->setTitle('Template');
$form1->edit->input->name->setSize(30);
$form1->edit->input->name->addTip('This field will be used by script to execute the template. It must be unique. Changing this content will affect to all the script which use this template.');

$form1->edit->addInput('email','multiinput');
$form1->edit->input->email->setTitle('Email Sender');
$form1->edit->input->email->addInput('from_name', 'text', 'Email Name');
$form1->edit->input->email->addInput('from_email', 'text', 'Email Address');
$form1->edit->input->email->addInput('global_email', 'checkbox', 'Use Global Email');
$form1->edit->input->email->addTip('To change "Global Email" you can go to <a href="index.php?mod=_cpanel.config" class="admin_link">Control Panel / Configuration</a> in tab "Email"');
$form1->edit->input->global_email->setDefaultValue(1);

$form1->edit->addInput('subject', 'text');
$form1->edit->input->subject->setTitle('Subject');
$form1->edit->input->subject->setSize(40);
$form1->edit->input->subject->setLanguage();

$form1->edit->addInput('global_subject', 'checkbox');
$form1->edit->input->global_subject->setTitle('Pre-Subject');
$form1->edit->input->global_subject->setCaption('Add Global Pre-Subject');
$form1->edit->input->global_subject->setDefaultValue(1);

$form1->edit->addInput('is_html', 'select');
$form1->edit->input->is_html->setTitle('Format');
$form1->edit->input->is_html->setExtra('id="email_format"');
$form1->edit->input->is_html->addOption('Plain Text', '0');
$form1->edit->input->is_html->addOption('HTML', '1');

$form1->edit->addInput('content', 'textarea');
$form1->edit->input->content->setTitle('Content');
$form1->edit->input->content->setLanguage();
$form1->edit->input->content->setSize('20', '');
$form1->edit->input->content->setExtra('class="editor" style="font-family: Courier New;"');
$form1->edit->input->content->setNl2br(false);

$form1->edit->addInput('global_footer', 'checkbox');
$form1->edit->input->global_footer->setTitle('Post-Footer');
$form1->edit->input->global_footer->setCaption('Add Global Post-Footer');
$form1->edit->input->global_footer->setDefaultValue(1);

$form1->edit->addInput('description', 'textarea');
$form1->edit->input->description->setTitle('Description');
$form1->edit->input->description->setSize('2', '60');

$form1->edit->action();
