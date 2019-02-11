<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id   = @intval($_GET['id']);
if (empty($table))
{
	$table  = 'bbc_content_comment';
}
if (empty($i_field))
{
	$i_field  = 'content';
}
if (empty($i_list))
{
	$i_list  = $i_field;
}
if (empty($i_func))
{
	$i_func  = 'content_link';
}
$data = $db->getRow("SELECT * FROM `{$table}` WHERE id={$id} ");
if(empty($data))
{
	redirect($Bbc->mod['circuit'].'.comment');
}

$form  = _lib('pea', $table);

$form->initEdit('WHERE id='.$id, 'id');

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Edit Comment');

$form->edit->addInput('title', 'plaintext');
$form->edit->input->title->setValue(@$data[$i_field.'_title']);
$form->edit->input->title->setExtra(array('<a href="index.php?mod='.$i_field.'.'.$i_list.'_edit&id='.$data[$i_field.'_id'].'" class="admin_link">','</a>'));


$form->edit->addInput('name', 'text');
$form->edit->addInput('image', 'text');
$form->edit->addInput('email', 'text');
$form->edit->addInput('website', 'text');

$form->edit->addInput('content', 'textarea');
$form->edit->input->content->setTitle('Message');
$form->edit->input->content->setNl2br( false );
$form->edit->input->content->addTip('<button type="button" onclick="return i_reply('.$id.');" class="btn btn-default btn-sm">'.icon('fa-reply').' reply</button>');

$form->edit->addInput('publish', 'checkbox');
$form->edit->input->publish->setTitle('Publish');
$form->edit->input->publish->setCaption('publish');

echo $form->edit->getForm();
$cfg = array(
	'table'      => $table,
	'field'      => $i_field,
	'id'         => $data[$i_field.'_id'],
	'par_id'     => $data['par_id'],
	'comment_id' => $id,
	'link'       => $i_func($data[$i_field.'_id'], $data[$i_field.'_title']),
	'list'       => 9,
	'captcha'    => 0,
	'approve'    => 1,
	'admin'      => 1
	);
echo _class('comment', $cfg)->show();
?>
<style type="text/css">
	.comment>h3 {
		display: none;
	}
	.comment>.form-comment {
		display: none;
	}
</style>
<script type="text/javascript">
	function i_reply(a) {
		if($("#comment_media_"+a).length) {
			var b = $('a[href=\\#reply]', $("#comment_media_"+a)).get(0);
			$(b).trigger("click");
		}else{
			alert("select spesific comment below this form to reply!");
		}
		return false;
	};
</script>