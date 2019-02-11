<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$data = array();
if ($menu_id && $prefix=='edit_')
{
	$sql   = 'WHERE id='.$menu_id;
	$title = 'Edit Menu';
}else{
	$sql   = '';
	$title = 'Add Menu';
	if ($menu_id)
	{
		$title = 'Add Sub Menu';
		$data = $db->getRow("SELECT * FROM bbc_menu WHERE id={$menu_id}");
		$q = "SELECT lang_id, title FROM bbc_menu_text WHERE menu_id={$menu_id}";
		$data['title'] = $db->getAssoc($q);
	}
}
link_js($Bbc->mod['root'].'menu/menuForm.js', false);
$form = _lib('pea', 'bbc_menu');

$form->initEdit($sql);
$form->edit->setLanguage('menu_id');

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle($title);

if($prefix=='edit_')
{
	$form->edit->addInput('menu_id','hidden');
	$form->edit->input->menu_id->setFieldName('id AS menu_id');
	$form->edit->input->menu_id->setIsIncludedInUpdateQuery( false );
}

$form->edit->addInput('title','text');
$form->edit->input->title->setTitle('Menu Title');
$form->edit->input->title->setExtra('rel="'.$form->edit->formName.'_title"');
$form->edit->input->title->setRequire();
$form->edit->input->title->setLanguage();

$form->edit->addInput('module','multiinput');
$form->edit->input->module->setTitle('Select Module');
$form->edit->input->module->addInput('module_id', 'select');
$form->edit->input->module->addInput('options', 'plaintext', '<button type="button" class="btn btn-default module_task" rel="'.$form->edit->formName.'">'.icon('fa-info-circle').'</button>');

$r = array();
$p = $is_admin ? '/admin' : '';
foreach ($r_module as $d)
{
	if (is_file(_ROOT.'modules/'.$d['name'].$p.'/_switch.php'))
	{
		$r[] = $d;
	}
}
$form->edit->input->module_id->addOption($r);
$form->edit->input->module_id->setDefaultValue(@$data['module_id']);

$form->edit->addInput('link','text');
$form->edit->input->link->setTitle('Real Link');
$form->edit->input->link->setDefaultValue('index.php?mod=module.main');
$form->edit->input->link->addTip('This is the real link in the system, normal format will be index.php?mod=[module_name].[task_name] you can also copy from URL bar and the system will automatically find out the real Link is.');

if (empty($is_admin)) // public menu
{
	$form->edit->addInput('site_url', 'multiinput');
	$form->edit->input->site_url->setTitle('Search Engine Optimization URL');
	$form->edit->input->site_url->addInput('seo0','plaintext',_URL);
	$form->edit->input->site_url->addInput('seo','text','SEO URL');
	$form->edit->input->site_url->addInput('seo1','plaintext','.html');
	$form->edit->input->site_url->addInput('seo2','plaintext', '<a href="#" onClick="return check_seo(\''.$prefix.'\');">'.icon('check',' Check SEO').' Check SEO</a>');
	$form->edit->input->seo->setExtra('rel="'.$form->edit->formName.'_seo"');

	if($prefix=='edit_') // edit mode [public menu]
	{
		/* JANGAN BISA PINDAH POSIS DULU, SOALNYA NANTI HARUS UPDATE BAWAHNYA JG DAN UPDATE POSISI YANG LAMA DAN YANG BARU
		if (empty($data['par_id']))
		{
			$form->edit->addInput( 'position', 'dependentdropdown' );
			$form->edit->input->position->setTitle('Menu Position');
			$form->edit->input->position->addInput('cat_id', 'bbc_menu_cat ORDER BY orderby ASC', '');
			$form->edit->input->position->setTable('cat_id', 'name', 'id');
			$form->edit->input->position->addInput('orderby', 'bbc_menu AS m LEFT JOIN bbc_menu_text AS t ON (t.menu_id=m.id AND t.lang_id='.lang_id().') WHERE is_admin=0 ORDER BY orderby ASC', 'cat_id');
			$form->edit->input->position->setTable('orderby', 'title', 'orderby');
			$form->edit->input->position->addOption('orderby', '--first order--', '0');
			$form->edit->input->position->addTip('Select menu position and order sequence, this menu will be place after order sequence selection, <a href="index.php?mod=_cpanel.menu&act=position">click here</a> to edit your menu position list');
			$form->edit->input->cat_id->setDefaultValue(@$keyword['cat_id']);
		}else{

		}*/
	}else{ // add mode [public menu]
		if (empty($data['id']))
		{
			$form->edit->addExtraField('par_id', '0', 'add');
			$form->edit->addInput( 'position', 'dependentdropdown' );
			$form->edit->input->position->setTitle('Menu Position');
			$form->edit->input->position->addInput('cat_id', 'bbc_menu_cat ORDER BY orderby ASC', '');
			$form->edit->input->position->setTable('cat_id', 'name', 'id');
			$form->edit->input->position->addInput('orderby', 'bbc_menu AS m LEFT JOIN bbc_menu_text AS t ON (t.menu_id=m.id AND t.lang_id='.lang_id().') WHERE is_admin=0 ORDER BY orderby ASC', 'cat_id');
			$form->edit->input->position->setTable('orderby', 'title', 'orderby');
			$form->edit->input->position->addOption('orderby', '--first order--', '0');
			$form->edit->input->position->addTip('Select menu position and order sequence, this menu will be place after order sequence selection, <a href="index.php?mod=_cpanel.menu&act=position" class="admin_link">click here</a> to edit your menu position list');
			$form->edit->input->cat_id->setDefaultValue(@$keyword['cat_id']);
		}else{
			$par_id = @intval($data['id']);
			$cat_id = @intval($data['cat_id']);
			if (empty($cat_id)) {
				$cat_id = $db->getOne("SELECT id FROM bbc_menu_cat WHERE 1 ORDER BY orderby ASC LIMIT 1");
			}
			$form->edit->addExtraField('par_id', $par_id, 'add');
			$form->edit->addExtraField('cat_id', $cat_id, 'add');
			$form->edit->addInput( 'orderby', 'orderby' );
			$form->edit->input->orderby->setAddCondition('is_admin='.$is_admin.' AND cat_id='.$cat_id.' AND par_id='.$par_id);
		}
	}
}else{
	$par_id = @intval($data['id']);
	$form->edit->addExtraField('seo', '', 'add');
	$form->edit->addExtraField('par_id', $par_id, 'add');
	$form->edit->addExtraField('cat_id', '1', 'add');
	$form->edit->addInput( 'orderby', 'orderby' );
	$form->edit->input->orderby->setAddCondition('is_admin='.$is_admin.' AND par_id='.$par_id);
}
$form->edit->addExtraField('is_admin', $is_admin, 'add');

$form->edit->addInput('protected', 'checkbox');
$form->edit->input->protected->setTitle('Menu Protection');
$form->edit->input->protected->setCaption('Protect this menu from unauthorize users');
$form->edit->input->protected->setExtra(' rel="protected"');
$form->edit->input->protected->addTip('if you check this option, Please <a href="index.php?mod=_cpanel.group" class="admin_link">Click here!</a> to go to Control Panel / User Group to set the privileges for which user groups are allowed to access this menu');
if (!empty($data))
{
	$form->edit->input->protected->setDefaultValue($data['protected']);
}else{
	$form->edit->input->protected->setDefaultValue($is_admin);
}
/*
$form->edit->addInput('privileges','multiselect');
$form->edit->input->privileges->setTitle('User group which is allowed to access');
$form->edit->input->privileges->setReferenceTable('bbc_user_group');
$form->edit->input->privileges->setReferenceField('name','id');
$form->edit->input->privileges->setReferenceCondition('is_admin='.$is_admin);
$form->edit->input->privileges->setReferenceCondition('menus!=",all,"');
$form->edit->input->privileges->setIsIncludedInSelectQuery( false );
$form->edit->input->privileges->setIsIncludedInUpdateQuery( false );
*/
$form->edit->addInput('active', 'checkbox');
$form->edit->input->active->setTitle('Menu Status');
$form->edit->input->active->setCaption('Published');

$form->edit->onSave('menu_update_insert');

$formMenu[$prefix] = $form->edit->getForm();
$formMenu[$prefix].= '<script type="text/javascript">var menu_delimiter="'.menu_delimiter().'";</script>';