<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$qr = _lib('qr', date('r'));
echo '<img src="'.$qr->show().'" alt="" />';
echo '<br />';
echo $qr->getData();
echo '<br />';
echo $qr->getUrl();
echo '<br />';
echo $qr->getDir();
// $qr->log();
