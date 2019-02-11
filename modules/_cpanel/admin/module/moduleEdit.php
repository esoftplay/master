<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$id = @intval($_GET['id']);
if (!empty($_POST['submit']) && $_POST['submit']=='upload')
{
	include 'upload.php';
}else
if (isset($_POST['edit_allow_group']))
{
	if (in_array('all', $_POST['edit_allow_group']))
	{
		$_POST['edit_allow_group'] = array('all');
	}
}

$form = _lib('pea', 'bbc_module');
$form->initEdit('WHERE id='.$id);

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Module Configuration');

$form->edit->addInput('name','sqlplaintext');

$form->edit->addInput('site_title','textarea');
$form->edit->input->site_title->setTitle('Meta Title');

$form->edit->addInput('site_desc','textarea');
$form->edit->input->site_desc->setTitle('Meta Description');

$form->edit->addInput('site_keyword','textarea');
$form->edit->input->site_keyword->setTitle('Meta Keywords');

$form->edit->addInput('protected', 'checkbox');
$form->edit->input->protected->setTitle('protect this module from unauthorized user');
$form->edit->input->protected->setCaption('Protected');

$form->edit->addInput('allow_group','multiselect');
$form->edit->input->allow_group->setTitle('if it\'s protected, which user group is allowed to access?');
$form->edit->input->allow_group->setReferenceTable('bbc_user_group');
$form->edit->input->allow_group->setReferenceField('name','id');
$form->edit->input->allow_group->setReferenceCondition('is_admin=0');
$form->edit->input->allow_group->addOption('--All Logged User--', 'all');

$form->edit->addInput('active', 'checkbox');
$form->edit->input->active->setTitle('Status');
$form->edit->input->active->setCaption('Activate');

$txt = '<a href="'.$Bbc->mod['circuit'].'.module&act=download&module_id='.$id.'" class="btn btn-default btn-xs">'.icon('cloud-download', 'Download Parameter').'</a>
<a data-toggle="modal" href="#module-upload" class="btn btn-default btn-xs">'.icon('cloud-upload', 'Upload Parameter').'</a>';
$form->edit->input->active->addTip('Module Parameter : '.$txt);

$form->edit->action();
echo $form->edit->getForm();
?>
<div class="modal fade" id="module-upload" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Upload Parameter</h4>
			</div>
			<form action="" method="POST" class="form" role="form" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="form-group">
						<label class="sr-only">Upload Parameters</label>
						<input type="file" name="params" class="form-control" placeholder="Upload File">
						<span class="text-danger">* WARNING : All the parameters that have been entered will be replaced with new parameters in the uploaded files</span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="submit" value="upload" class="btn btn-primary"><?php echo icon('cloud-upload', 'Upload Parameter') ?> Upload</button>
				</div>
			</form>
		</div>
	</div>
</div>