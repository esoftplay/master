<?php
link_js('includes/lib/pea/includes/formIsRequire.js', false);
?>
<form class="form-signin formIsRequire" method="POST" action="">
  <h2 class="form-signin-heading"><?php echo lang('Please sign in');?></h2>
  <br />
  <label class="sr-only"><?php echo lang('Username');?></label>
  <input class="form-control" placeholder="<?php echo lang('Username');?>" req="any true" autofocus="" type="username" name="usr" />
  <label class="sr-only"><?php echo lang('Password');?></label>
  <input class="form-control" placeholder="<?php echo lang('Password');?>" req="any true" type="password" name="pwd" />
  <div class="checkbox">
    <label>
      <input value="1" type="checkbox" name="rememberme" /> <?php echo lang('Remember me');?>
    </label>
  </div>
  <input type="hidden" name="url" value="<?php echo $user_url; ?>" />
  <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo lang('Login');?></button>
</form>
<div class="clearfix"></div>