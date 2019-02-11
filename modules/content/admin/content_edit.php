<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$content_id = @intval($_GET['id']);
$type_id		= @intval($_GET['type_id']);
$form_act		= $content_id ? 'edit' : 'add';

/*=====================================================
 * INCLUDE ALL JAVASCRIPT...
 *====================================================*/
_lib('pea', 'bbc_content');
link_js('content_edit.js', false);
_func('editor');
_func('image');
_func('array');

/*=====================================================
 * FETCH ALL LANG...
 *====================================================*/
$r_lang = lang_assoc();

/*=====================================================
 * FETCH ALL MENU IN JAVASCRIPT...
 *====================================================*/
include 'menu_fetch.php';

/*=====================================================
 * FETCH DEFAULT CONFIG...
 *====================================================*/
$def_config = get_config('content', 'detail');

if(!empty($_POST['submit_update']))
{
	if(_class('content')->content_save($_POST, $content_id))
	{
		echo msg('Success to update Data.');
	}
	include 'menu_fetch.php'; // REFETCH MENU IF MENU IS CHANGING...
}

if($form_act == 'edit')
{
	$data = content_fetch_admin($content_id);
	$type_id = @intval($data['type_id']);
	$sys->nav_add('Edit Content');
}else{
	$data = array(
		'id'               => '0',
		'par_id'           => !empty($_GET['par_id']) ? intval($_GET['par_id']) : 0,
		'kind_id'          => '0',
		'cat_ids'          => array(),
		'config'           => content_type($type_id, 'detail'),
		'created_by_alias' => $user->name,
		'image'            => '',
		'images'           => '',
		'file_type'        => '0',
		'file_format'      => '',
		'is_popimage'      => '1',
		'is_front'         => '0',
		'is_config'        => '0',
		'publish'          => '1'
	);
}
?>
<form action="" method="POST" role="form" name="content_form" id="content_form" enctype="multipart/form-data">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<?php
					echo ucwords($form_act.' content');
					if ($content_id > 0)
					{
						echo ' <a href="'._URL.'id.htm/'.$content_id.'" target="external">'.$content_id.' '.icon('fa-external-link').'</a>';
					}
					?>
				</h3>
			</div>
			<div class="panel-body">
		   	<script type="text/javascript">
					var all_menus = <?php echo $all_menus;?>;
					var lang_ids	=	[<?php echo implode(',', array_keys($r_lang));?>];
					var lang_id		= '<?php echo lang_id();?>';
					var menu_delimiter="<?php echo menu_delimiter(); ?>";
				</script>
				<?php
				if($type_id > 0)
				{
					?>
					<input type="hidden" name="type_id" value="<?php echo $type_id;?>">
					<?php
				}
				$tabs_text = array();
				foreach($r_lang AS $lang_id => $language)
				{
					ob_start();
					include 'content_edit-text.php';
					$tabs_text[$language['title']] = ob_get_contents();
					ob_end_clean();
				}
				echo tabs($tabs_text, 0, 'tabs_content_text');
				?>
			</div>
			<div class="panel-footer">
				<?php
				if (!empty($_GET['return']))
				{
					?>
					<span type="button" class="btn btn-default" onclick="document.location.href='<?php echo $_GET['return'] ?>';"><span class="glyphicon glyphicon-chevron-left"></span></span>
					<?php
				}
				?>
				<button type="submit" name="submit_update" value="&nbsp;SAVE&nbsp;" class="btn btn-primary"><?php echo icon('floppy-disk'); ?> Save</button>
				<button type="reset" class="btn btn-warning"><?php echo icon('repeat'); ?> RESET</button>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<?php
		$tabs_content_param = array();
		ob_start();
		include 'content_edit-attributes.php';
		$tabs_content_param['Attributes'] = ob_get_contents();
		ob_end_clean();
		ob_start();
		include 'content_edit-parameters.php';
		$tabs_content_param['Parameters'] = ob_get_contents();
		ob_end_clean();
		ob_start();
		include 'content_edit-menu.php';
		echo '</form>';
		$tabs_content_param['Menu'] = ob_get_contents();
		ob_end_clean();
		if($form_act == 'edit')
		{
			$cfg = array(
				'table'    => 'bbc_content_comment',
				'field'    => 'content',
				'id'       => $data['id'],
				'type'     => $data['config']['comment'],
				'list'     => $data['config']['comment_list'],
				'link'     => content_link($data['id'], $data['text'][lang_id()]['title']),
				'form'     => $data['config']['comment_form'],
				'emoticon' => $data['config']['comment_emoticons'],
				'captcha'  => 0,
				'approve'  => 1,
				'alert'    => $data['config']['comment_email'],
				'admin'    => 1
				);
			$tabs_content_param['Comments'] = _class('comment', $cfg)->show();
		}
		echo tabs($tabs_content_param, 0, 'tabs_content_param', 1);
		?>
	</div>
