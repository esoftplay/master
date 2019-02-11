<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

switch(@$_GET['do'])
{
	case 'sort':
		if(isset($_POST) && is_array($_POST))
		{
			$q = "SELECT id, position_id, orderby FROM bbc_block WHERE template_id=$template_id";
			$r = $db->getAll($q);
			$r_block = array();
			foreach($r AS $dt) {
				$r_block[$dt['id']] = $dt;
			}
			foreach($_POST AS $pos => $blocks)
			{
				if(preg_match('~^position_[0-9]+$~s', $pos))
				{
					$position_id = str_replace('position_', '', $pos);
					$i = 1;
					foreach($blocks AS $txt)
					{
						preg_match('~([0-9]+)$~s', $txt, $match);
						$id = intval($match[1]);
						$block = $r_block[$id];
						if($block['position_id'] != $position_id || $block['orderby'] != $i)
						{
							$q = "UPDATE bbc_block SET position_id=$position_id, orderby=$i WHERE id=$id";
							$db->Execute($q);
						}
						$i++;
					}
				}
			}
		}
		exit;
	break;
	case 'delete':
		$id = intval($_GET['id']);
		if($id > 0)
		{
			$q = "DELETE FROM bbc_block WHERE id=$id";
			if($db->Execute($q)){
				$q = "DELETE FROM bbc_block_text WHERE block_id=$id";
				$db->Execute($q);
				delete_block_file();
				echo '1';
			}else echo '0';
		}
		exit;
	break;
	case 'edit':
		block_edit($id);
	break;
	case 'add':
		block_add();
	break;
}
function block_edit($id)
{
	global $db;
	$data = block_repair_post();
	$q = "UPDATE bbc_block
	SET show_title			= '".$data['show_title']."'
	, link							= '".$data['link']."'
	, cache							= '".$data['cache']."'
	, theme_id					= '".$data['theme_id']."'
	, group_ids					= '".$data['group_ids']."'
	, menu_ids					= '".$data['menu_ids']."'
	, menu_ids_blocked	= '".$data['menu_ids_blocked']."'
	, module_ids_allowed= '".$data['module_ids_allowed']."'
	, module_ids_blocked= '".$data['module_ids_blocked']."'
	, config						= '".$data['config']."'
	, active						= '".$data['active']."'
	WHERE id=".$data['id']."
	";
	$db->Execute($q);
	$q = "SELECT lang_id FROM bbc_block_text WHERE block_id=$id";
	$lang_ids = $db->getCol($q);
	$last_title = '';
	foreach(lang_assoc() AS $lang_id => $d)
	{
		$title = @$data['title'][$lang_id];
		if(empty($title)) $title = $last_title;
		else $last_title = $title;
		if(in_array($lang_id, $lang_ids))
		{
			$q = "UPDATE bbc_block_text
			SET title			= '".$title."'
			WHERE block_id= $id
			AND lang_id		= $lang_id
			";
		}else{
			$q = "INSERT INTO bbc_block_text
			SET title	= '".$title."'
			, block_id= $id
			, lang_id	= $lang_id
			";
		}
		$db->Execute($q);
	}
	$title = !empty($data['title'][lang_id()]) ? strip_tags($data['title'][lang_id()]) : 'Untitled Block';
	$name  = $db->getOne("SELECT name FROM bbc_block_ref WHERE id=".@$data['block_ref_id']);
	if (!empty($name))
	{
		$name = "'{$name}' ";
	}
	$js = '<script type="text/JavaScript">
var a = $("#title_'.$data['id'].'");
if (a.length) {
	var b = $(a).parent();
	$(a).html("'.$title.'");
	$(".delete", b).attr("title", "delete ['.$title.']");
	$(".edit", b).attr("title", "edit '.$name.'['.$title.']");
	$(".info", b).attr("title", "info ['.$title.']");
	$("#form-title").html("edit ['.$title.']");
	BS3("#myModal").modal("hide");
}
</script>';
	echo $js;
	echo msg('Success to update block');
}
function block_add()
{
	global $db;
	$data = block_repair_post();
	$q="SELECT COUNT(*) FROM bbc_block 
			WHERE position_id	=".$data['position_id']."
			AND template_id		=".$data['template_id'];
	$data['orderby'] = $db->getOne($q);
	$data['orderby']++;
	$q = "INSERT INTO bbc_block
	SET template_id			= '".$data['template_id']."'
	,	block_ref_id			= '".$data['block_ref_id']."'
	,	position_id				= '".$data['position_id']."'
	, show_title				= '".$data['show_title']."'
	, link							= '".$data['link']."'
	, cache							= '".$data['cache']."'
	, theme_id					= '".$data['theme_id']."'
	, group_ids					= '".$data['group_ids']."'
	, menu_ids					= '".$data['menu_ids']."'
	, menu_ids_blocked	= '".$data['menu_ids_blocked']."'
	, module_ids_allowed= '".$data['module_ids_allowed']."'
	, module_ids_blocked= '".$data['module_ids_blocked']."'
	, config						= '".$data['config']."'
	, orderby						= '".$data['orderby']."'
	, active						= '".$data['active']."'
	";
	$db->Execute($q);
	$id = $db->Insert_ID();
	$last_title = '';
	foreach(lang_assoc() AS $lang_id => $d)
	{
		$title = @$_POST['title'][$lang_id];
		if(empty($title)) $title = $last_title;
		else $last_title = $title;
		$q = "INSERT INTO bbc_block_text
		SET title	= '".$title."'
		, block_id= $id
		, lang_id	= $lang_id
		";
		$db->Execute($q);
	}
	$title = !empty($data['title'][lang_id()]) ? strip_tags($data['title'][lang_id()]) : 'Untitled Block';
		$html ='\
				<div style="-moz-user-select: none;" class="itemHeader">\
					<div id="title_'.$id.'">'.$title.'</div>\
					<span class="delete" onclick="block_delete(this, '.$id.');" title="delete ['.$title.']"></span>\
					<span class="edit" onclick="block_edit(this, '.$id.');" title="edit ['.$title.']"></span>\
					<span class="info" onclick="block_info(this, '.$id.');" title="info ['.$title.']"></span>\
				</div>\
				<div class="itemContent"></div>';
		$js = '
<script type="text/JavaScript">
	if (typeof BS3 != "undefined") {
		var html = \''.$html.'\';
	  var newdiv = window.parent.document.createElement("div");
	  newdiv.setAttribute("class","groupItem");
	  newdiv.setAttribute("id","block_'.$id.'");
	  newdiv.innerHTML = html;
	  BS3("#position_'.$data['position_id'].'").append(newdiv);
		block_init();
		BS3("#myModal").modal("hide");
	}
</script>
		';
		echo $js;
	echo msg('Success to create new block');
}
function block_repair_post()
{
	global $db, $template_id;
	// DEFINE MENU_IDS_BLOCKED...
	$arr = array();
	$_POST['menu_ids'] = (isset($_POST['menu_ids']) && is_array($_POST['menu_ids'])) ? $_POST['menu_ids'] : array();
	if(isset($_POST['menu_ids_blocked']))
	{
		foreach((array)$_POST['menu_ids_blocked'] AS $id)
		{
			if(!empty($id) AND !in_array($id, $_POST['menu_ids']))
			{
				$arr[] = $id;
			}
		}
	}
	$_POST['menu_ids_blocked']= repairImplode($arr);

	// DEFINE MENU_IDS...
	$arr = array();
	if(in_array('all', $_POST['menu_ids']))
	{
		$arr[] = 'all';
	}else{
		foreach($_POST['menu_ids'] AS $id => $dt)
		{
			if(!empty($dt))
			{
				$arr[] = $dt;
			}
		}
	}
	$_POST['menu_ids'] = repairImplode($arr);

	// DEFINE MODULE_IDS_BLOCKED...
	$arr = array();
	$_POST['module_ids_allowed'] = isset($_POST['module_ids_allowed']) ? $_POST['module_ids_allowed'] : array();
	if(isset($_POST['module_ids_blocked']))
	{
		foreach((array)$_POST['module_ids_blocked'] AS $id){
			if(!in_array($id, $_POST['module_ids_allowed']))
				$arr[] = $id;
		}
	}
	$_POST['module_ids_blocked']= repairImplode($arr);

	// DEFINE MODULE_IDS_ALLOWED...
	$_POST['module_ids_allowed']= repairImplode($_POST['module_ids_allowed']);

	// DEFINE GROUP_IDS...
	$_POST['group_ids'] = isset($_POST['group_ids']) ? $_POST['group_ids'] : array('all');
	$arr = array();
	if(in_array('all', $_POST['group_ids'])){
		$arr[] = 'all';
	}elseif(in_array('logged', $_POST['group_ids'])){
		$arr[] = 'logged';
	}else{
		foreach($_POST['group_ids'] AS $id => $dt)
			if(!empty($dt))	$arr[] = $dt;
	}
	$_POST['group_ids'] = repairImplode($arr);

	$_POST['title']	= is_array($_POST['title']) ? $_POST['title'] : array(lang_id() => $_POST['title']);
	$titles = array();
	foreach(lang_assoc() AS $lang_id => $d) {
		$titles[$lang_id] = @$_POST['title'][$lang_id];
	}
	$input = array(
		'id'		=> @intval($_POST['id'])
	,	'template_id'	=> $template_id
	,	'block_ref_id'=> @intval($_POST['block_ref_id'])
	,	'position_id'	=> @intval($_POST['position_id'])
	,	'title'				=> $titles
	,	'show_title'	=> !empty($_POST['show_title']) ? '1' : '0'
	, 'link'				=> $_POST['link']
	, 'cache'				=> intval($_POST['cache'])
	,	'theme_id'		=> @intval($_POST['theme_id'])
	,	'group_ids'		=> $_POST['group_ids']
	,	'menu_ids'		=> $_POST['menu_ids']
	,	'menu_ids_blocked'	=> $_POST['menu_ids_blocked']
	,	'module_ids_allowed'=> $_POST['module_ids_allowed']
	,	'module_ids_blocked'=> $_POST['module_ids_blocked']
	,	'active'			=> isset($_POST['active']) ? '1' : '0'
	,	'config'			=> isset($_POST['config']) ? config_encode(stripslashes_r($_POST['config'])) : ''
	);
	return $input;
}