<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!defined('_FCM'))
{
	echo msg('Sorry, the system cannot find tirth party App. Make sure you have constant variables _FCM and _FCM_ID in the config', 'danger');
}else{
	/*
	1. news content
	2. update menu
	3. notification alert (gur masuk neng MasterActivity biasa tp ono alert e koyo nek post comment)
	4. koyo nomer 3 tp nek tombol ok di click ngarah neng url
	*/
	if (!empty($_POST['type_id']))
	{
		$result = content_fcm(array(
			'data' => array(
				'type_id' => $_POST['type_id'],
				'title'   => $_POST['title'],
				'message' => $_POST['message'],
				'url'     => $_POST['url']
				)
			));
		$result = @json_decode($result, 1);
		echo msg('Your message has been sent with message ID: '.@$result['message_id'], 'success');
		if (!empty($result['message_id']) && count($result) > 1)
		{
			echo(table($result));
		}
	}
	link_js('includes/lib/pea/includes/formIsRequire.js', false);
	link_js('includes/lib/pea/includes/FormTags.js', false);
	$token = array(
		'table'  => 'bbc_content_text',
		'field'  => 'CONCAT(title, " (", content_id, ")")',
		'id'     => 'content_id',
		'format' => 'CONCAT(title, " (", content_id, ")")',
		'sql'    => 'lang_id='.lang_id(),
		'expire' => strtotime('+2 HOUR')
		);
	$type_id = !empty($_POST['type_id']) ? $_POST['type_id'] : 1;
	?>
	<form action="" method="POST" class="formIsRequire" role="form">
		<div class="panel panel-default">
			  <div class="panel-heading">
					<h3 class="panel-title">Send Notification</h3>
			  </div>
			  <div class="panel-body">
					<div class="form-group">
						<label>Notification Type</label>
						<select name="type_id" class="form-control" id="type_id">
							<?php echo createOption(array(1=>'New Content', 3=>'Alert'), $type_id);?>
						</select>
					</div>
					<div class="form-group" id="content_title">
						<label>Content ID / Title</label>
						<input type="text" name="content_id" rel="ac" req="number true" class="form-control" placeholder="Content ID / Title" data-token="<?php echo encode(json_encode($token)); ?>" />
					</div>
					<div class="form-group inputs">
						<label>Notification Title (max. 80 chars)</label>
						<input type="text" name="title" req="any true" class="form-control" placeholder="Title" />
					</div>
					<div class="form-group inputs">
						<label>Notification Message (max. 141 chars)</label>
						<input type="text" name="message" req="any true" class="form-control" placeholder="Message" />
					</div>
					<div class="form-group inputs">
						<label>Notification URL</label>
						<input type="url" name="url" req="url false" class="form-control" placeholder="URL" />
					</div>
			  </div>
	  	  <div class="panel-footer">
				<button type="submit" name="submit" value="submit" class="btn btn-primary btn-sm"> <span class="glyphicon glyphicon-send"></span> Send</button>
	  		</div>
		</div>
	</form>
	<script type="text/javascript">
	_Bbc(function($){
		$("#type_id").on("change", function(){
			$(".inputs .form-control").val("").trigger("change");
			$("#content_title input").val("").trigger("change");
			switch($(this).val())
			{
				case "1":
					$('[name="content_id"]').attr("req", "number true");
					$("#content_title").show();
					$(".inputs").hide();
					break;
				case "3":
					$('[name="content_id"]').removeAttr("req");
					$("#content_title").hide();
					$(".inputs").show();
					break;
			}
		}).trigger("change");
		$('[name="content_id"]').change(function(){
			if ($(this).val()!="")
			{
				$.ajax({
					url:"index.php?mod=content.fcm_content&id="+$(this).val(),
					global: false,
					dataType: "json",
					success: function(a){
						if (a.ok)
						{
							$('[name="title"]').val(a.title).trigger("change");
							$('[name="message"]').val(a.description).trigger("change");
							$('[name="url"]').val(a.url).trigger("change");
						}
					}
				});
			}
		});
	});
	</script>
	<?php
}