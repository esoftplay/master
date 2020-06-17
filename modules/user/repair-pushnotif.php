<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$db->debug=1;
$exist = $db->getOne("SHOW TABLES LIKE 'bbc_user_push'");
if (empty($exist))
{
	$db->Execute("CREATE TABLE `bbc_user_push` (
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) DEFAULT '0',
		`group_ids` varchar(120) DEFAULT '0' COMMENT 'comma separated like repairImplode()',
		`username` varchar(120) DEFAULT '',
		`token` varchar(255) DEFAULT '',
		`device` varchar(255) DEFAULT '',
		`os` varchar(60) DEFAULT '',
		`ipaddress` varchar(20) DEFAULT '',
		`created` datetime DEFAULT CURRENT_TIMESTAMP,
		`updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'setiap mengirim pesan ke table bbc_user_push_notif maka field ini akan di update',
		PRIMARY KEY (`id`),
		KEY `user_id` (`user_id`),
		KEY `group_ids` (`group_ids`),
		KEY `os` (`os`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table untuk menyimpan token dari para pengguna mobile app'");
}else{
	/* UPDATE SEMUA USER DI bbc_user_push TAMBAHIN FIELD group_ids */
	$fields = $db->getCol("SHOW FIELDS FROM `bbc_user_push`");
	if (!in_array('group_ids', $fields))
	{
		$db->Execute("ALTER TABLE `bbc_user_push` ADD `group_ids` varchar(120) COMMENT 'comma separated like repairImplode()' AFTER `user_id`");
		$db->Execute("ALTER TABLE `bbc_user_push` ADD INDEX (`group_ids`)");
		$r = $db->getAll("SELECT id, user_id, group_ids FROM `bbc_user_push` WHERE 1 ORDER BY id");
		foreach ($r as $d)
		{
			if (!empty($d['user_id']))
			{
				$group_ids = $db->getOne("SELECT group_ids FROM bbc_user WHERE id={$d['user_id']}");
				$db->Update('bbc_user_push', array('group_ids' => $group_ids), $d['id']);
			}
		}
	}
	if (!in_array('os', $fields))
	{
		$db->Execute("ALTER TABLE `bbc_user_push` ADD `os` VARCHAR(60)  NULL  DEFAULT ''  AFTER `device`");
		$db->Execute("ALTER TABLE `bbc_user_push` ADD INDEX (`os`)");
	}
}

$exist = $db->getOne("SHOW TABLES LIKE 'bbc_user_push_notif'");
if (empty($exist))
{
	$db->Execute("CREATE TABLE `bbc_user_push_notif` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) DEFAULT '0',
		`group_id` int(11) DEFAULT '0',
		`title` varchar(150) DEFAULT '',
		`message` varchar(255) DEFAULT '',
		`params` text COMMENT 'variable yang akan di proses dalam mobile app field wajib action, module, argument',
		`return` text COMMENT 'data return dari API notifikasi',
		`status` tinyint(1) DEFAULT '0' COMMENT '0=belum terkirim, 1=berhasil terkirim, 2=sudah terbaca, 3=gagal terkirim',
		`created` datetime DEFAULT CURRENT_TIMESTAMP,
		`updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		KEY `user_id` (`user_id`),
		KEY `group_id` (`group_id`),
		KEY `status` (`status`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table untuk menyimpan data notifikasi yang dikirim ke para pengguna mobile app'");
}else{
	/* UPDATE SEMUA USER DI bbc_user_push_notif TAMBAHIN FIELD group_id */
	$fields = $db->getCol("SHOW FIELDS FROM `bbc_user_push_notif`");
	if (!in_array('group_id', $fields))
	{
		$db->Execute("ALTER TABLE `bbc_user_push_notif` CHANGE `user_id` `user_id` bigint(20)");
		$db->Execute("ALTER TABLE `bbc_user_push_notif` ADD `group_id` INT(11)  NULL  DEFAULT '0'  AFTER `user_id`;");
		$db->Execute("ALTER TABLE `bbc_user_push_notif` ADD INDEX (`group_id`)");
	}
}
pr($Bbc->debug);die();