<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

link_js(tpl('comment.js'), false);
link_css(tpl('comment.css'), false);
?>
<div class="comment">
	<h3 class="text">
		<?php echo icon('fa-comments-o');?>
		<span id="comment_count"><?php echo money($total_all); ?></span>
		<?php echo lang('Comment'); ?>
		<span class="clearfix"></span>
	</h3>
	<?php
	echo page_ajax($total_list, $config['list'], _URL.'user/comment_list?token='.urlencode($token).'&page_comment=');
	if (!empty($config['form']))
	{
		_class('comment')->session();
		if (empty($user->website))
		{
			$user->website = '';
			if ($user->id==1 || !empty($_SESSION['bbcAuthAdmin']['id']))
			{
				$user->website = 'http://'.config('site', 'url');
			}
		}
		?>
		<form action="" method="post" class="form-comment" rel="<?php echo $config['id']; ?>" class="form">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo lang('Post Your Comment');?></h3>
				</div>
				<div class="panel-body" id="comment_post">
					<div class="form-group comment_output"></div>
					<div class="form-group col-md-6 no-left" style="padding-right: 2px;">
						<input name="name" value="<?php echo @$user->name; ?>" class="form-control" type="text" placeholder="<?php echo lang('name');?>" />
					</div>
					<div class="form-group col-md-6 no-right" style="padding-left: 2px;">
						<input name="email" value="<?php echo @$user->email; ?>" class="form-control" type="text" placeholder="<?php echo lang('email');?>" />
					</div>
					<div class="clearfix"></div>
					<div class="form-group">
						<input name="website" placeholder="http://" value="<?php echo $user->website; ?>" class="form-control" type="text">
					</div>
					<div class="form-group form-smiley">
						<textarea name="content" class="form-control" id="comment_input" placeholder="<?php echo lang('comment');?>"></textarea>
						<?php
						if(!empty($config['emoticon']))
						{
							?>
							<div class="smiley_link"><a href="#" id="smiley_icon" rel="Icons"><?php echo icon('fa-smile-o');?></a></div>
							<div class="smiley_container">
								<?php
								$r = array_chunk(_func('smiley', 'icon'), 6);
								foreach($r AS $i => $icons)
								{
									for($j=0;$j < 9;$j++)
									{
										echo @$r[$j][$i];
									}
								}
								?>
							</div>
							<?php
						}
						?>
					</div>
					<?php echo !empty($config['captcha']) ? _lib('captcha')->create() : ''; ?>
					<input type="hidden" name="token" id="comment_token" value="<?php echo $token; ?>" />
					<input type="hidden" name="par_id" value="0" id="par_id<?php echo $config['id']; ?>" />
					<input type="hidden" name="user_id" value="<?php echo $user->id; ?>" />
					<input type="hidden" name="image" value="<?php echo @$user->image; ?>" />
	        <span class="dropdown" id="comment_login">
					  <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default btn-sm">
							<?php echo icon('send');?>
							<?php echo lang('Send');?>
					    <span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu" aria-labelledby="dLabel">
					  	<li><a href="user/comment_login/facebook"><?php echo icon('fa-facebook-square'); ?> Facebook</a></li>
					  	<li><a href="user/comment_login/google"><?php echo icon('fa-google-plus-square'); ?> Google+</a></li>
					  	<li><a href="user/comment_login/twitter"><?php echo icon('fa-twitter-square'); ?> Twitter</a></li>
					  	<li><a href="user/comment_login/linkedin"><?php echo icon('fa-linkedin-square'); ?> LinkedIn</a></li>
					  	<li><a href="user/comment_login/instagram"><?php echo icon('fa-instagram'); ?> Instagram</a></li>
					  	<!-- <li><a href="user/comment_login/yahoo"><?php echo icon('fa-yahoo'); ?> Yahoo</a></li> -->
					  </ul>
					</span>
					<button type="submit" name="submit_comment" class="btn btn-default btn-sm" id="comment_send">
						<?php echo icon('send');?>
						<?php echo lang('Send');?>
						<b>(<?php echo @$user->name; ?>)</b>
					</button>
					<button type="reset" class="btn btn-default btn-sm">
						<?php echo icon('repeat');?>
						<?php echo lang('Reset');?>
					</button>
				</div>
			</div>
		</form>
		<div class="modal fade" tabindex="-1">
		  <div class="modal-dialog">
		    <div class="modal-content">
		    	<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title"><?php echo lang('Post Your Comment');?></h4>
		      </div>
		      <div class="modal-body"></div>
		    </div>
		  </div>
		</div>
		<?php
	}else{
		echo '<input type="hidden" name="token" id="comment_token" value="'.$token.'" />';
	}
	?>
</div>
