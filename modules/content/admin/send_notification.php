<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!defined('_FCM_SENDER_ID'))
{
	echo msg('it looks like you don\'t have any users in your mobile app or simply you have no integrated mobile app. Make sure you have constant variables _FCM_SENDER_ID and _FCM_SERVER_JSON in the config', 'danger');
}else{
	$tables = $db->getCol("SHOW TABLES LIKE 'bbc_user_push%'");
	if (!in_array('bbc_user_push_topic_list', $tables))
	{
		redirect('index.php?mod=_cpanel.user&act=fcm-activate');
	}else{
		$user_exist = $db->getOne("SELECT 1 FROM `bbc_user_push` WHERE 1 LIMIT 1");
		if (empty($user_exist))
		{
			echo msg('You don\'t have any users in your mobile app', 'danger');
		}else{
			/*
			1. news content
			2. update menu
			3. notification alert (gur masuk neng MasterActivity biasa tp ono alert e koyo nek post comment)
			4. koyo nomer 3 tp nek tombol ok di click ngarah neng url
			*/
			if (!empty($_POST['type_id']))
			{
				_func('alert');
				// // news about new content
				if ($_POST['type_id'] == 1)
				{
					$out = alert_push('/topics/userAll',
						$_POST['title'],
						$_POST['message'],
						'content/detail',
						array(
							'url' => str_replace('://', '://data.', $_POST['url'])
							)
						);
				}else{
					// alert modal
					$out = alert_push('/topics/userAll',
						$_POST['title'],
						$_POST['message'],
						'content',
						array(
							'url' => $_POST['url']
							),
						'alert'
						);
				}
				if ($out)
				{
					echo msg('The system will send your message', 'success');
				}else{
					echo msg('Fail to send the message', 'danger');
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
							<input type="url" name="url" req="url false" class="form-control" placeholder="http://..." />
						</div>
					</div>
					<div class="panel-footer">
						<button type="submit" name="submit" value="submit" class="btn btn-primary btn-sm"> <span class="glyphicon glyphicon-send"></span> Send</button>
					</div>
				</div>
			</form>
			<script type="text/javascript">
			_Bbc(function($) {
				$("#type_id").on("change", function() {
					$(".inputs .form-control").val("").trigger("change");
					$("#content_title input").val("").trigger("change");
					switch($(this).val()) {
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
				$('[name="content_id"]').change(function() {
					if ($(this).val()!="") {
						$.ajax({
							url:"index.php?mod=content.send_notification_content&id="+$(this).val(),
							global: false,
							dataType: "json",
							success: function(a){
								if (a.ok) {
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
	}
}