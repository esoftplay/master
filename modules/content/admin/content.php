<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$type_id   = @intval($_GET['type_id']);
$q_add     = $type_id ? "type_id={$type_id}" : '1';
$edit_link = ($Bbc->mod['task']=='content_sub') ? $Bbc->mod['circuit'].'.'.$type_id.'_content_sub_edit' : $Bbc->mod['circuit'].'.content_edit';
$r_lang    = array();
foreach(lang_assoc() AS $i => $d)
{
	$r_lang[$i] = $d['title'];
}

$form    = _lib('pea', 'bbc_content_'.$type_id);
$form->initSearch();
if (count($r_lang) > 1)
{
	$form->search->addInput('lang_id','select');
	$form->search->input->lang_id->addOption('Select Language', '');
	$form->search->input->lang_id->addOption($r_lang);
}

$kinds = content_kind();
$form->search->addInput('kind_id','select');
$form->search->input->kind_id->setTitle('Search by Format');
$form->search->input->kind_id->addOption('Select Format', '');
$form->search->input->kind_id->addOption($kinds, array_keys($kinds));

$q = "SELECT COUNT(*) FROM bbc_content_cat WHERE {$q_add}";
$c = $db->getOne($q);
if ($c > 1)
{
	$form->search->addInput('cat_id','selecttable');
	$form->search->input->cat_id->setTitle('Search by Category');
	$form->search->input->cat_id->addOption('Select Category', '');
	$form->search->input->cat_id->setReferenceTable('bbc_content_cat AS c LEFT JOIN bbc_content_cat_text AS t ON (c.id=t.cat_id AND t.lang_id='.lang_id().')');
	$form->search->input->cat_id->setReferenceField('title', 'id');
	$form->search->input->cat_id->setReferenceNested();
	if ($type_id)
	{
		$form->search->input->cat_id->setReferenceCondition($q_add);
	}
	if ($c > 30)
	{
		$form->search->input->cat_id->setAutoComplete();
	}
}
if($type_id)
{
	$form->search->addExtraField('type_id', $type_id);
}

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title,description,keyword,tags,intro,content');

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$add_where = '';
if(isset($keyword['cat_id']))
{
	$add_where = ' LEFT JOIN bbc_content_category AS y ON (c.id=y.content_id AND pruned=0)';
}
if (!isset($keyword['lang_id']))
{
	$keyword['lang_id'] = lang_id();
}
$form = _lib('pea', 'bbc_content AS c LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND lang_id='.$keyword['lang_id'].')'.$add_where);
$form->initRoll( $add_sql.' ORDER BY id DESC', 'id' );

$form->roll->addInput( 'id', 'sqlplaintext' );
$form->roll->input->id->setFieldName( 'id AS page_id' );
$form->roll->input->id->setDisplayColumn(true);

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('image');
$form->roll->input->image->setFolder($Content->img_path.'p_', '', false);
$form->roll->input->image->setPlaintext( true );
$form->roll->input->image->setImageClick( true );
$form->roll->input->image->setDisplayColumn(true);

$form->roll->addInput('col','multiinput');
$form->roll->input->col->setTitle('Title');
$form->roll->input->col->setDelimiter(' ');
$form->roll->input->col->addInput('title', 'sqllinks');
$form->roll->input->col->addInput('visit', 'editlinks');

$form->roll->input->title->setLinks( $edit_link );
$form->roll->input->title->setExtra( 'title="edit page"' );

$form->roll->input->visit->setIcon('fa-external-link', 'open page');
$form->roll->input->visit->setLinks(_URL.'id.htm');
$form->roll->input->visit->setExtra('target="external"');
$form->roll->input->visit->setFieldName('id AS visit');

if (config('manage', 'is_nested'))
{
	$form->roll->addInput( 'par_id', 'selecttable' );
	$form->roll->input->par_id->setTitle('Parent');
	$form->roll->input->par_id->setReferenceTable('bbc_content_text');
	$form->roll->input->par_id->setReferenceField( 'title', 'content_id' );
	$form->roll->input->par_id->setReferenceCondition( 'lang_id='.$keyword['lang_id'] );
	$form->roll->input->par_id->setLinks($edit_link);
	$form->roll->input->par_id->setDisplayColumn(false);
}
$form->roll->addInput('hits','sqlplaintext');
$form->roll->input->hits->setNumberFormat();
$form->roll->input->hits->setDisplayColumn(false);

$form->roll->addInput( 'kind_id', 'select' );
$form->roll->input->kind_id->setTitle( 'Format' );
$form->roll->input->kind_id->addOption($kinds, array_keys($kinds));
$form->roll->input->kind_id->setPlaintext(true);
$form->roll->input->kind_id->setDisplayColumn(false);

$form->roll->addInput('category','multiselect');
$form->roll->input->category->setReferenceTable('bbc_content_cat_text');
$form->roll->input->category->setReferenceField('title','cat_id');
$form->roll->input->category->setReferenceCondition('lang_id='.lang_id());
$form->roll->input->category->setRelationTable('bbc_content_category');
$form->roll->input->category->setRelationTableId('category_id');
$form->roll->input->category->setRelationField('content_id','cat_id');
$form->roll->input->category->setPlaintext(true);
$form->roll->input->category->setDisplayColumn(false);
$form->roll->input->category->textTip='';

$form->roll->addInput('privilege','multiselect');
$form->roll->input->privilege->setReferenceTable('bbc_user_group');
$form->roll->input->privilege->setReferenceField('name','id');
$form->roll->input->privilege->addOption('public', 'all');
$form->roll->input->privilege->setPlaintext(true);
$form->roll->input->privilege->setDisplayColumn(false);
$form->roll->input->privilege->textTip='';

$tbl = array(
		'Author'   => '{created_by_alias}',
		// 'Hit'      => '{hits}',
		'Last Open' => '{last_hits}',
		'Modified' => '{modified}',
		);
// if (in_array('hits', (array)@$_SESSION['ColView'][menu_save(@$_GET['mod'].$form->roll->formName)]))
// {
// 	unset($tbl['Hit']);
// }

$form->roll->addInput( 'created', 'texttip' );
$form->roll->input->created->setTitle( 'Date' );
$form->roll->input->created->setDateFormat();
$form->roll->input->created->setTemplate(table($tbl));
$form->roll->input->created->setDisplayColumn(true);
$form->roll->input->last_hits->setDateFormat('M jS, Y H:i:s');
$form->roll->input->modified->setDateFormat('M jS, Y H:i:s');

if(!config('frontpage','auto'))
{
	$form->roll->addInput( 'is_front', 'checkbox' );
	$form->roll->input->is_front->setTitle( 'show' );
	$form->roll->input->is_front->setCaption( 'front' );
}

$form->roll->addInput( 'publish', 'checkbox' );
$form->roll->input->publish->setTitle( 'Active' );
$form->roll->input->publish->setCaption( 'publish' );

$form->roll->onDelete('content_delete');
$form->roll->onSave('content_type_refresh');

echo $form->roll->getForm();
?>
<script type="text/JavaScript">
_Bbc(function($) {
	$('#direct_edit').submit(function() {
		var a = $('#input_content_id').val();
		var id = parseInt(a);
		var go = '<?php echo $Bbc->mod['circuit'];?>.content_edit_check&';
		if(id > 0) {
			go += 'id='+id;
		}else
		if(/^(?:ht|f)tps?:\/\//.test(a)){
			go += 'url='+encodeURIComponent(a);
		}else{
			go = "";
		}
		if (go!="") {
			$.getJSON(go, function(data) {
				if(data.found > 0) {
					document.location.href = '<?php echo $edit_link;?>&id='+data.found+'&return='+encodeURIComponent(document.location.href);
				}else{
					alert('Content is not found in database');
				}
			});
		}else{
			alert('Content ID or URL is invalid');
		}
		return false;
	});
})</script>
<form method="POST" action="" id="direct_edit" class="form-inline pull-left" role="form">
	<div class="form-group">
		<input id="input_content_id" type="text" value="" class="form-control" title="Insert Content ID or URL you want to edit" placeholder="Insert ID or URL to edit" />
	</div>
	<button type="submit" name="Submit" value="EDIT" class="btn btn-default">
		<?php echo icon('edit');?>
	</button>
</form>
