<form action="" method="post" enctype="multipart/form-data" onsubmit="return validate(this);" role="form">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php echo lang('Change Password');?>
			</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label> <?php echo lang('Old Password');?> </label>
				<input name="oldpass" value="" class="form-control" type="password" />
			</div>
			<div class="form-group">
				<label> <?php echo lang('New Password');?> </label>
				<input name="newpass" value="" class="form-control" type="password" />
			</div>
			<div class="form-group">
				<label> <?php echo lang('Confirm Password');?> </label>
				<input name="confirmpass" value="" class="form-control" type="password" />
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submit_login" value="Submit" class="btn btn-primary btn-sm">
				<?php	echo icon('saved'); ?>
				<?php echo lang('Submit');?>
			</button>
			<button type="reset" value="Reset" value="reset" class="btn btn-warning btn-sm">
				<?php echo icon('repeat'); ?>
				<?php echo lang('Reset'); ?>
			</button>
		</div>
	</div>
</form>
<script type="text/JavaScript">
function validate(a){
	var passed = false;
	with(a)
	{
		if(oldpass.value==''){
			alert("Insert the old password") ;
			oldpass.select(); 
		}else if(newpass.value==''){
			alert("Insert the new password you require") ;
			newpass.select(); 
		}else if(confirmpass.value==''){
			alert("Retype your password to confirm the correct typing") ;
			confirmpass.select(); 
		}else if(confirmpass.value != newpass.value){
			alert("New Password and Confirm Password are not matched") ;
			confirmpass.select(); 
		}else if(oldpass.value == confirmpass.value){
			alert("You don't do any exchange to your password") ;
			newpass.select(); 
		}else{
			passed = true;
		}
	}
	return passed;
}
</script>
