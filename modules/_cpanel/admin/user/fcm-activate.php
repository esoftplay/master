<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Activate mobile notification');

if (!defined('_FCM_SENDER_ID'))
{
	echo msg('Make sure you have constant variables _FCM_SENDER_ID and _FCM_SERVER_JSON in the config.<br />
	         to get <b>_FCM_SENDER_ID</b> here you can do:
					<ul>
						<li>go to <a href="https://console.firebase.google.com/" target="_blank">firebase console</a></li>
						<li>select project you want to setup</li>
						<li>go to project setting</li>
						<li>click tab "Cloud Messaging"</li>
						<li><img src="https://i.ibb.co/1T9b4wD/Screenshot-2024-04-01-at-15-21-20.png" class="img-responsive" /></li>
					</ul>
					to get <b>_FCM_SERVER_JSON</b> you can
					<a href="https://stackoverflow.com/questions/46287267/how-can-i-get-the-file-service-account-json-for-google-translate-api" target="_blank">click here</a>
					and copy the content as constant variable', 'danger');
}else{
	$tables = $db->getCol("SHOW TABLES LIKE 'bbc_user_push%'");
	if (in_array('bbc_user_push_topic_list', $tables))
	{
		echo msg('the system has already supported to send notification to your mobile app users. <a href="index.php?mod=_cpanel.user&act=fcm" rel="admin_link">click here</a> to manage notifiaction', 'success');
	}else{
		if (!empty($_POST['setup']))
		{

			$queries = "
				ALTER TABLE `bbc_user_push` ADD `type` TINYINT(1)  NULL  DEFAULT 0  COMMENT '0=expo, 1=fcm'  AFTER `token`;
				ALTER TABLE `bbc_user_push` ADD INDEX (`type`);
				CREATE TABLE `bbc_user_push` (
				  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` bigint(20) DEFAULT 0,
				  `group_ids` varchar(120) DEFAULT '0' COMMENT 'comma separated like repairImplode()',
				  `username` varchar(120) DEFAULT '',
				  `token` varchar(255) DEFAULT '',
				  `type` tinyint(1) DEFAULT 0 COMMENT '0=expo, 1=fcm',
				  `device` varchar(255) DEFAULT '',
				  `os` varchar(60) DEFAULT '',
				  `ipaddress` varchar(20) DEFAULT '',
				  `created` datetime DEFAULT current_timestamp(),
				  `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'setiap mengirim pesan ke table bbc_user_push_notif maka field ini akan di update',
				  PRIMARY KEY (`id`),
				  KEY `user_id` (`user_id`),
				  KEY `group_ids` (`group_ids`),
				  KEY `token` (`token`),
				  KEY `type` (`type`),
				  KEY `os` (`os`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='daftar device mobile app';

				CREATE TABLE `bbc_user_push_notif` (
				  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` bigint(20) DEFAULT 0,
				  `group_id` int(11) DEFAULT 0,
				  `title` varchar(150) DEFAULT '',
				  `message` varchar(255) DEFAULT '',
				  `params` text DEFAULT NULL COMMENT 'variable yang akan di proses dalam mobile app field wajib action, module, argument',
				  `return` text DEFAULT NULL COMMENT 'data return dari API notifikasi',
				  `status` tinyint(1) DEFAULT 0 COMMENT '0=belum terkirim, 1=berhasil terkirim, 2=sudah terbaca, 3=gagal terkirim',
				  `created` datetime DEFAULT current_timestamp(),
				  `updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
				  PRIMARY KEY (`id`),
				  KEY `user_id` (`user_id`),
				  KEY `group_id` (`group_id`),
				  KEY `status` (`status`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='daftar notifikasi yang dikirim ke para pengguna mobile app';

				CREATE TABLE `bbc_user_push_sending` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` bigint(20) DEFAULT NULL COMMENT 'org yg kirim notif',
				  `to` varchar(255) DEFAULT NULL,
				  `title` varchar(255) DEFAULT NULL,
				  `message` text,
				  `module` varchar(60) DEFAULT NULL,
				  `arguments` text,
				  `action` varchar(60) DEFAULT NULL,
				  `status` tinyint(1) DEFAULT '0' COMMENT '0=process, 1=done',
				  `sent` bigint(20) DEFAULT '0',
				  `created` datetime DEFAULT CURRENT_TIMESTAMP,
				  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`),
				  KEY `user_id` (`user_id`),
				  KEY `status` (`status`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table penyimpanan sementara untuk bisa melihat progress nya';

				CREATE TABLE `bbc_user_push_topic` (
				  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` bigint(20) DEFAULT '0' COMMENT '0=buatan system',
				  `name` varchar(60) DEFAULT NULL,
				  `description` text,
				  `created` datetime DEFAULT CURRENT_TIMESTAMP,
				  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `name` (`name`),
				  KEY `user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='daftar topic atau group user untuk mengirim notif, semua user secara default masuk ke group userAll dan user_ID';

				CREATE TABLE `bbc_user_push_topic_list` (
				  `list_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `push_id` bigint(20) unsigned DEFAULT NULL,
				  `topic_id` bigint(20) unsigned DEFAULT NULL,
				  `user_id` bigint(20) unsigned DEFAULT NULL,
				  PRIMARY KEY (`list_id`),
				  KEY `push_id` (`push_id`),
				  KEY `topic_id` (`topic_id`),
				  KEY `user_id` (`user_id`),
				  CONSTRAINT `bbc_user_push_topic_list_ibfk_1` FOREIGN KEY (`push_id`) REFERENCES `bbc_user_push` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
				  CONSTRAINT `bbc_user_push_topic_list_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `bbc_user_push_topic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='daftar user_id dan push_id yang subscribe ke bbc_user_push_topic';
				INSERT INTO `bbc_cpanel` (`id`, `par_id`, `title`, `image`, `act`, `link`, `is_shortcut`, `orderby`, `active`) VALUES (22, '2', 'Mobile Notification', 'notification.png', 'fcm', 'index.php?mod=_cpanel.user&act=fcm', '0', '2', '1')";
			$r = explode(';', $queries);
			foreach ($r as $q)
			{
				$db->Execute($q);
			}
			echo msg('now the system has already supported to send notification to your mobile app users. <a href="index.php?mod=_cpanel.user&act=fcm" rel="admin_link">click here</a> to manage notifiaction', 'success');
		}else{
			?>
			<div class="container">
				<div class="jumbotron">
					<h1>Setup Mobile notification</h1>
					<p>by click button bellow, the system will setup neccesary data for you mobile notification</p>
					<p>
						<form action="" method="POST" class="form-inline" role="form">
							<button type="submit" name="setup" value="ok" class="btn btn-default"><span class="glyphicon glyphicon-repeat" title="repeat"></span> Setup Now</button>
						</form>
					</p>
				</div>
			</div>
			<?php
		}
	}
}