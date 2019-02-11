<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if($user->id > 0)
{
  echo 'You\'re Login as '.$user->username;
}else{
  ?>
  <form class="form-signin" action="index.php?mod=user.login" method="POST" target="_parent">
    <label for="inputEmail" class="sr-only"><?php echo lang('Username');?></label>
    <input id="inputEmail" class="form-control" placeholder="<?php echo lang('Username');?>" required="" type="username" name="usr">
    <label for="inputPassword" class="sr-only"><?php echo lang('Password');?></label>
    <input id="inputPassword" class="form-control" placeholder="<?php echo lang('Password');?>" required="" type="password" name="pwd">
    <div class="checkbox">
      <label>
        <input value="1" name="rememberme" id="_user_remember" type="checkbox"><?php echo lang('Remember me');?>
      </label>
    </div>
    <label>
      	<input type="hidden" name="url" value="<?php echo @$user_url;?>" />
  				<?php	if(@$config['forget'])	{	?>
  					<a href="index.php?mod=user.forget-password"><?php echo lang('Forget Password ?');?></a>
  				<?php	}	?>
  				<?php	if(@$config['register'])	{	?>
  					<a href="index.php?mod=user.register"><?php echo lang('Register');?></a>
  				<?php	}	?>
     </label>
    <button class="btn btn-sm btn-primary btn-block" value="Login" type="submit" name="submit"><?php echo lang('Login');?></button>
  </form>
  <div class="clearfix"></div>
  <?php
}
