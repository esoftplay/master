<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function cpanel_create_user(&$form)
{
	global $db;
	$output = '';
	if($form->is_updated)
	{
		if (empty($_POST['params']))
		{
			$_POST['params'] = array('group_ids' => $_POST['group_ids']);
		}
		$new_id = user_create($_POST);
		if (empty($new_id))
		{
			$output = user_create_validate_msg();
		}else{
			echo msg('User has been created', 'success');
		}
		$form->is_updated = false;
	}
	return $output;
}
ob_start();
$group_ids = @$_POST['group_ids'];
if (!empty($group_ids))
{
	$form   = _class('params');
	$params = array(
		'title'       => 'Add User',
		'table'       => 'bbc_account',
		'config_pre'  => array(),
		'config'      => user_field_group($group_ids),
		'config_post' => array(),
		'pre_func'    => 'cpanel_create_user',
		'name'        => 'params',
		'id'          => 0
		);

	$params['config_pre'] = array(
		'username' => array(
			'text'      => 'Username',
			'type'      => 'text',
			'mandatory' => '1'
			),
		'password' => array(
			'text'      => 'Password',
			'type'      => 'text',
			'mandatory' => '1'
			),
		'group_ids' => array(
			'text'    => 'User Group',
			'type'    => 'text',
			'default' => fixValue($group_ids)
			),
		'name' => array(
			'text'      => 'Name',
			'type'      => 'text',
			'mandatory' => '1'
			)
		);
	$params['config_post'] = array(
		'email' => array(
			'text'      => 'Email',
			'type'      => 'text',
			'mandatory' => '1',
			'checked'   => 'email'
			)
		);
	$form->set($params);
	$form->set_encode(true);

	if (empty($_GET['is_ajax']))
	{
		echo '<div id="userform">'.$form->show().'</div>';
	}else{
		// karena kalo secara default class params di load di header sedangkan
		// di ajax tidak ada header
		link_js(_LIB.'pea/includes/formIsRequire.js', false);
		echo $form->show();
	}
}
if (empty($_GET['is_ajax']))
{
	$r_group = $db->getAll("SELECT * FROM `bbc_user_group` WHERE 1 ORDER BY `is_admin` DESC, `score` DESC");
	?>
	<div id="userform"></div>
	<form method="POST" action="" name="" id="user_group" enctype="multipart/form-data" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Create User</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label>Select User Group</label>
					<div class="checkbox">
						<?php
						foreach ($r_group as $group)
						{
							$label = $group['is_admin'] ? 'admin :: ' : 'public :: ';
							$label.= $group['name'];
							?>
							<label>
								<input type="checkbox" name="group_ids[]" value="<?php echo $group['id']; ?>" req="any true" <?php echo is_checked(in_array($group['id'], (array)$group_ids)); ?> />
								<?php echo $label; ?>
							</label><br />
							<?php
						}
						?>
					</div>
					<div class="help-block">
						<a href="index.php?mod=_cpanel.group" class="admin_link">Click here</a> to manage your User Groups with privilege of each group
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" name="newuser" value="newuser" class="btn btn-primary btn-sm"><?php echo icon('fa-user-plus'); ?> Create</button>
				<button type="reset" name="newuser" value="cancel" class="btn btn-warning btn-sm"><?php echo icon('fa-recycle'); ?> Cancel</button>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		_Bbc(function($){
			$("input[name=group_ids]").closest(".form-group").hide();
			$("#user_group").on("submit", function(e){
				e.preventDefault();
				if ($(this).serialize()=="") {
					alert("Please select one of user group or more!");
				}else{
					$.ajax("<?php echo $Bbc->mod['circuit'].'.'.$Bbc->mod['task']; ?>&act=user-create&is_ajax=1", {
						method: "POST",
						data: $(this).serialize(),
						success: function(a){
							$("#userform").html(a);
							$("input[name=group_ids]").closest(".form-group").hide();
						},
					});
				}
			}).on("reset", function(){
				$("#userform").html("");
			});
		});
	</script>
	<?php
}
$userEdit = ob_get_contents();
ob_end_clean();
if (!empty($_GET['is_ajax']))
{
	echo $userEdit;
	die();
}
