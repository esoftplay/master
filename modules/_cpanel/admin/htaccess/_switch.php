<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$thisFile = _ROOT.'.htaccess';
if(isset($_POST['edit_htaccess']))
{
	@chmod($thisFile, 0777);
	if(file_write($thisFile, $_POST['edit_htaccess']))
	{
		$msg = 'Succeed to edit .htaccess file';
	}else $msg = 'Failed to edit .htaccess file, please try again later..!!';
	?>
	<script type="text/javascript">alert('<?php echo $msg;?>');</script>
	<?php
	die();
}
include _ROOT.'modules/user/repair-comment.php';
_func('editor');
$text_css = file_read($thisFile);
$config = array(
	'id'						=> 'edit_htaccess',
	'allow_resize'	=> true,
	'syntax'				=> 'c',
	'begin_toolbar' => 'save',
	'save_callback' => 'edit_submit',
	'height'				=> '350px'
);
?>
<script type="text/javascript">
	function edit_submit() {
		document.getElementById('edit_htaccess').value = editAreaLoader.getValue('edit_htaccess');
		document.forms['edit'].submit();
	}
</script>
<IFRAME name="save_css" src="" frameBorder="0" style="display: none;"></IFRAME>
<form method=post action="" id="edit" name="edit" target="save_css">
	<?php echo editor_code($config, $text_css);?>
</form>
