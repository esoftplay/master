<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$conf = _class('bbcconfig');
$tabs = array();

include 'config-site.php';
$tabs['Site'] = $conf->show();

include 'config-logged.php';
$tabs['Logged'] = $conf->show();

include 'config-rules.php';
$tabs['Rules'] = $conf->show();

include 'config-email.php';
$tabs['Email'] = $conf->show();

include 'config-user.php';
$tabs['Users'] = $form->roll->getForm();

include_once _ROOT.'modules/content/_config.php';

$conf->set(content_config_frontpage());
$tabs['Front Page'] = $conf->show();

echo tabs($tabs, 1, 'global_config',1);
