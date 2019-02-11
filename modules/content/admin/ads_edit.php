<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (empty($form))
{
	$q = "SELECT id, par_id, title FROM bbc_content_cat AS c
		LEFT JOIN bbc_content_cat_text AS t ON (c.id=t.cat_id AND t.lang_id=".lang_id().")
		ORDER BY c.par_id, t.title ASC";
	$cats = $db->getAll($q);
	$form = _lib('pea',  'bbc_content_ad');
	$show = true;
}else{
	$show = false;
}
$form->initEdit(!empty($id) ? 'WHERE id='.$id : '');

$is_image = $is_title = 1;
if (isset($_POST[$form->edit->formName.'_type_id']))
{
	switch ($_POST[$form->edit->formName.'_type_id'])
	{
		case '1': // banner
			$is_title = 0;
			break;
		case '2': // text only
			$is_image = 0;
			break;
	}
}

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle(!empty($id) ? 'Edit Content Ad' : 'Create Content Ad');

$form->edit->addInput( 'type_id', 'select' );
$form->edit->input->type_id->setTitle('Type');
$form->edit->input->type_id->addOption(array('1'=>'Banner (Image Size: 1080x315 pixel)', '2'=>'Text Only', '0'=>'Logo Text (Logo Size: 315x315 pixel)'));
$form->edit->input->type_id->setDefaultValue(1);
$form->edit->input->type_id->setExtra('id="i_type"');

$form->edit->addInput('image','file');
$form->edit->input->image->setTitle('Image');
$form->edit->input->image->setFolder(_ROOT.'images/modules/content/ads/');
$form->edit->input->image->setExtra('id="i_image"');
if ($is_image) {
	$form->edit->input->image->setRequire();
}

$form->edit->addInput('title','text');
$form->edit->input->title->setTitle('Title');
$form->edit->input->title->setExtra('id="i_title"');
if ($is_title) {
	$form->edit->input->title->setRequire();
}

$form->edit->addInput('link','text');
$form->edit->input->link->setcaption('http://....');
$form->edit->input->link->setRequire("url");
if ($id)
{
	$form->edit->input->link->addTip('<a href="" id="i_link">Go to</a> URL above');
}

$form->edit->addInput( 'cat_id', 'select' );
$form->edit->input->cat_id->setTitle('Place in Category');
$form->edit->input->cat_id->addOption('All Category', 0);
$form->edit->input->cat_id->addOption(_func('array', 'path', $cats, 0, '', '', '--'));
$form->edit->input->cat_id->addTip('<a href="index.php?mod=content.category" class="admin_link">Click here!</a> to manage your category');

$form->edit->addInput('exp','multiinput');
$form->edit->input->exp->setTitle('Advertise Expiration');
$form->edit->input->exp->addInput('expire', 'checkbox', 'Expiration On:');
$form->edit->input->exp->addInput('expire_date', 'date', 'Expire On');
$form->edit->input->expire->setExtra('id="exp"');
$form->edit->input->expire_date->setExtra('id="exp_date"');

$form->edit->addInput( 'active', 'checkbox' );
$form->edit->input->active->setTitle('Status');
$form->edit->input->active->setCaption('Active');

if ($id)
{
	$form->edit->addInput('click','multiinput');
	$form->edit->input->click->setTitle('Total Clicks');
	$form->edit->input->click->addInput('hit', 'sqlplaintext', 'Click');
	$form->edit->input->click->addInput('c1', 'plaintext', 'click(s)');
	$form->edit->input->click->addInput('c2', 'plaintext', ', Last clicked on: ');
	$form->edit->input->click->addInput('hit_last', 'sqlplaintext', 'Click');
	$form->edit->input->hit->setNumberFormat();
	$form->edit->input->hit_last->setDateFormat('M jS, Y', 'Never');

	$form->edit->addInput('creation','multiinput');
	$form->edit->input->creation->setTitle('Data Creation');
	$form->edit->input->creation->addInput('c3', 'plaintext', 'Created on: ');
	$form->edit->input->creation->addInput('created', 'sqlplaintext');
	$form->edit->input->creation->addInput('c4', 'plaintext', ', Updated on: ');
	$form->edit->input->creation->addInput('updated', 'sqlplaintext');
	$form->edit->input->creation->addInput('c5', 'plaintext', ', Displayed on: ');
	$form->edit->input->creation->addInput('displayed', 'sqlplaintext');
	$form->edit->input->created->setDateFormat();
	$form->edit->input->updated->setDateFormat('M jS, Y', 'Never');
	$form->edit->input->displayed->setDateFormat('M jS, Y', 'Never');

	$form->edit->addExtraField('updated', date('Y-m-d H:i:s'));
}else{
	$form->edit->addExtraField('created', date('Y-m-d H:i:s'));
}

$form->edit->onSave('content_ads_save');
$form->edit->action();
if ($show)
{
	echo $form->edit->getForm();
}
function content_ads_save($id)
{
	if (empty($id))
	{
		$id = $GLOBALS['id'];
	}
	if (!empty($id))
	{
		global $db;
		$q   = "SELECT * FROM `bbc_content_ad` WHERE `id`={$id}";
		$d   = $db->getRow($q);
		$sql = array();
		if (substr($d['expire_date'], 0,1)=='0')
		{
			$d['expire_date'] = '0000-00-00';
			$sql[] = '`expire_date`=\'0000-00-00\'';
		}
		if ($d['expire']=='1')
		{
			if ($d['expire_date']=='0000-00-00')
			{
				$sql[] = '`expire`=0';
			}
		}else{
			if ($d['expire_date']!='0000-00-00')
			{
				$sql[] = '`expire_date`=\'0000-00-00\'';
			}

		}
		if (!empty($sql))
		{
			$db->Execute("UPDATE `bbc_content_ad` SET ".implode(', ', $sql)." WHERE `id`={$id}");
		}
	}
}
?>
<script type="text/javascript">
	_Bbc(function($){
		window.isFadeInput = 0;
		$("#i_type").on("change", function(){
			var a = $(this).val();
			var b = $("#i_image");
			var c = $("#i_title");
			var d = function(a) {
				var b = a.closest(".form-group");
				a.attr("req", "any true");
				if (b.is(":hidden")) {
					if (window.isFadeInput) {
						b.show("slow");
					}else{
						b.show();
					}
				}
			};
			var e = function(a) {
				var b = a.closest(".form-group");
				a.removeAttr("req");
				if (!b.is(":hidden")) {
					if (window.isFadeInput) {
						b.hide("slow");
					}else{
						b.hide();
					}
				}
			};
			$(".has-error").removeClass("has-error");
			switch(a) {
				case '1': // banner
					d(b);
					e(c);
					break;
				case '2': // text only
					e(b);
					d(c);
					break;
				case '0': // logo & text
					d(b);
					d(c);
					break;
			}
			window.isFadeInput = 1;
		}).trigger("change");
		$("#exp").on("change", function(){
			if ($(this).is(":checked")) {
				$("#exp_date").fadeTo('slow', 1);
			}else{
				$("#exp_date").fadeTo('slow', 0.3);
			}
		}).trigger("change");
		$("#i_link").on("click", function(e){
			e.preventDefault();
			var u = $("input", $(this).closest(".form-group")).val();
			window.open(u);
		});
	});
</script>