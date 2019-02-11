<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

echo $form1->roll->getForm().$language_update;
echo $sys->button('index.php?mod=_cpanel.language&act=super-update', 'Repair Index', 'fa-wrench');
echo $sys->button('index.php?mod=_cpanel.language&act=import', 'Import Excel', 'fa-file-excel-o');
