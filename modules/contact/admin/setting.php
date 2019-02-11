<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tabs = array();
$form = _class('bbcconfig');

$_setting = array(
	'email'=> array(
		'text'		=> 'Email',
		'type'		=> 'text',
		'tips'		=> 'Insert email destination from contact us form or leave it blank to use <a href="index.php?mod=_cpanel.config" rel="admin_link">global configuration email</a>'
	),
	'address'		=> array(
		'text'		=> 'Address',
		'type'		=> 'htmlarea',
		'attr'		=> array('Width' => '400px', 'Height'=>'150px')
	)
);
$output = array(
	'config'=> $_setting,
	'name'	=> 'form',
	'title'	=> 'Contact Address'
);
$form->set($output);
$tabs['Address'] = $form->show();


$r_icons = array();
for($i=1;$i <= 24;$i++) $r_icons[] = $i;
$_setting = array(
	'auto_check'=> array(
		'text'   => 'YM Status',
		'type'   => 'radio',
		'option' => array('1' => 'Auto Check', '0' => 'Manual Check'),
		'tips'   => 'select how system check your yahoo messenger status in block'
	),
	'ym_show'	=> array(
		'text'    => 'Max. list',
		'type'    => 'text',
		'default' => '5',
		'tips'    => 'define maximum number of YM ID to show in block'
	),
	'icon'	=> array(
		'text'   => 'YM icon',
		'type'   => 'select',
		'option' => $r_icons,
		'tips'   => 'select which icon will be used for YM ID : <span><img id="ym_icon_1" src=""> <img id="ym_icon_0" src=""></span>'
	),
	'name'	=> array(
		'text'	=> 'Show Name',
		'type'	=> 'radio',
		'option'=> array('1' => 'Yes', '0' => 'No')
	),
	'address'	=> array(
		'text'	=> 'Show Address',
		'type'	=> 'radio',
		'option'=> array('1' => 'Yes', '0' => 'No')
	)
);
$output = array(
	'config' => $_setting,
	'name'   => 'widget',
	'title'  => 'Block Configuration'
);
$form->set($output);
$tabs['Widget'] = $form->show();



$form = _lib('pea', 'contact_field');
$form->initRoll( "WHERE 1 ORDER BY orderby", 'id' );
$form->roll->setDeleteTool(false);

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'Title' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.setting_field_edit' );

$form->roll->addInput( 'orderby', 'orderby' );
$form->roll->input->orderby->setTitle( 'Ordered' );

$form->roll->addInput( 'mandatory', 'checkbox' );
$form->roll->input->mandatory->setTitle( 'not null' );
$form->roll->input->mandatory->setCaption( 'yes' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'active' );

$tabs['Fields'] = $form->roll->getForm();
$tabs['Fields'] .= $sys->button($Bbc->mod['circuit'].'.setting_field&return='.urlencode(seo_uri()), 'Manage Fields', 'tasks');

echo tabs($tabs);
?>
<script type="text/javascript">
_Bbc(function($) {
	$('#widget\\[icon\\]').change(function(){
		var i	= $(this).val();
		var src = _URL+'modules/contact/images/';
		$('#ym_icon_0').attr('src', src+'0/'+i+'.gif');
		$('#ym_icon_1').attr('src', src+'1/'+i+'.gif');
	}).trigger('change');
});
</script>