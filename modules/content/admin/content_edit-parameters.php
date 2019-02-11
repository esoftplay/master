<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

include_once '../_config.php';
$conf        = content_config_detail();
$form_config = _class('bbcconfig');
?>
<div class="panel panel-default panel-sm">
	<div class="panel-heading checkbox">
		<label>
			<input type="checkbox" name="is_config" value="1" id="is_config"<?php echo is_checked($data['is_config']);?>>
			Specified Parameter
		</label>
		<a href="index.php?mod=content.type_edit&id=<?php echo $type_id; ?>" class="pull-right admin_link"><?php echo icon('wrench', 'Edit Default Parameter'); ?></a>
	</div>
	<div class="panel-body" id="content_param">
		<?php echo $form_config->show_param($conf['config'], $data['config'], 'null', 'config');?>
	</div>
</div>