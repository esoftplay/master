<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include tpl('detail_header0.html.php');
if (!empty($data['file_url']) || !empty($data['file']))
{
	$valid = false;
	if (!empty($data['file_type']))
	{
		$valid = true;
	}else{
		$path = _class('content')->path.$data['id'].'/';
		if (is_file(_ROOT.$path.$data['file']))
		{
			$valid = true;
		}
	}
	if ($valid)
	{
		$format = !empty($data['file_format']) ? '-'.$data['file_format'] : '';
		$value  = ($data['file_register']) ? '' : ' value="'.$data['id'].'"';
		?>
		<button type="button" class="btn btn-default btn-lg btn-block" id="download_button"<?php echo $value; ?>><?php echo icon('fa-file'.$format.'-o'); ?> <?php echo lang('Download Now'); ?></button>
		<div class="text text-muted">
			<?php echo ($data['file_hit'] > 0) ? '<span class="author pull-left">'.lang('Downloaded').': '.items($data['file_hit'], 'time').'</span>' : '';?>
			<?php echo (strtotime($data['file_hit_time']) > 0) ? '<span class="created pull-right">'.lang('Last Download').': '.content_date($data['file_hit_time']).'</span>' : '';?>
			<div class="clearfix"></div>
			<br />
		</div>
		<script type="text/javascript">eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('p(0($){$("#q").r(0(e){e.b();2($("#3").c){$("#3").s("t")}4{$.d({f:"g",5:6.7.8,h:{"9":1},i:"j",k:0(a){2(a.9){6.7.8=a.5}4{u(\'v... w x y z A B!\')}}})}});2($("#3").c){$.C(D+"l/E/F/l/G.H",0(){$("#m").I(0(e){e.b();$.d({f:"g",5:6.7.8,h:$(J).K(),i:"j",k:0(a){2(a.9){$(\'#m\').n(a.o)}4{$(\'#L\').n(a.o)}}})})})}});',48,48,'function||if|download_register|else|url|document|location|href|ok||preventDefault|length|ajax||type|POST|data|dataType|json|success|includes|download_register_form|html|message|_Bbc|download_button|click|modal|show|alert|Ops|Something|wrong|please|try|again|later|getScript|_URL|lib|pea|formIsRequire|js|submit|this|serialize|download_register_form_output'.split('|'),0,{}));</script>
		<?php
	}
	if ($data['file_register'])
	{
		?>
		<div class="modal fade" id="download_register" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span>
							<span class="sr-only"><?php echo lang('Close');?></span>
						</button>
						<h4 class="modal-title"><?php echo lang('Please let us know who you are');?></h4>
					</div>
					<form action="" method="post" id="download_register_form" class="formIsRequire">
						<div class="modal-body">
							<div class="form-group">
								<div id="download_register_form_output"></div>
							</div>
							<div class="form-group">
								<input name="name" class="form-control" type="text" placeholder="<?php echo lang('Name');?>" req="any true" />
							</div>
							<div class="form-group col-md-6" style="padding-left:0;padding-right:2px;">
								<input name="email" class="form-control" type="email" placeholder="<?php echo lang('Email');?>" req="email true" />
							</div>
							<div class="form-group col-md-6" style="padding-left:2px;padding-right:0;">
								<input name="phone" class="form-control" type="tel" placeholder="<?php echo lang('Phone');?>" req="phone true" />
							</div>
							<div class="clearfix"></div>
							<div class="form-group">
								<input name="address" class="form-control" type="text" placeholder="<?php echo lang('Address');?>" req="any true" />
								<input name="ok" type="hidden" value="1" />
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary"><?php echo lang('Send');?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}