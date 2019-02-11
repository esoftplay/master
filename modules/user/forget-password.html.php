<?php link_js(_LIB.'pea/includes/formIsRequire.js', false); ?>
<form action="" method="post" enctype="multipart/form-data" role="form" class="formIsRequire">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang('Forget Password');?></h3>
		</div>
		<div class="panel-body">
			<?php
			if(!empty($Msg))
			{
				echo msg($Msg);
			}
			?>
			<div class="form-group">
				<label>
					<?php echo lang('Email');?>
				</label>
				<input name="email" value="" placeholder="<?php echo lang('Insert your email'); ?>" class="form-control" type="email" req="email true" />
			</div>
			<?php echo _lib('captcha')->create(); ?>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submit" value="Submit" class="btn btn-primary btn-sm">
				<?php	echo icon('saved'); ?>
				<?php echo lang('Submit');?>
			</button>
			<button type="reset" value="Reset" class="btn btn-warning btn-sm">
				<?php echo icon('repeat'); ?>
				<?php echo lang('Reset'); ?>
			</button>
		</div>
	</div>
</form>