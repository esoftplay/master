<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$r = $db->getCol("SHOW TABLES");
foreach ($r as $t)
{
	if (preg_match('~_comment$~is', $t))
	{
    $f = $db->getCol("SHOW FIELDS FROM `{$t}`");
    if (!in_array('par_id', $f))
    {
      $db->Execute("ALTER TABLE `{$t}` ADD `par_id` INT(11)  NULL  DEFAULT '0'  AFTER `id`");
      $db->Execute("ALTER TABLE `{$t}` ADD INDEX (`par_id`)");
    }
    if (!in_array('user_id', $f))
    {
      $db->Execute("ALTER TABLE `{$t}` ADD `user_id` INT(11)  NULL  DEFAULT '0'  AFTER `par_id`");
      $db->Execute("ALTER TABLE `{$t}` ADD INDEX (`user_id`)");
    }
    if (!in_array('reply_all', $f))
    {
      $db->Execute("ALTER TABLE `{$t}` ADD `reply_all` INT(11)  NULL  DEFAULT '0'  AFTER `user_id`");
      $db->Execute("ALTER TABLE `{$t}` ADD INDEX (`reply_all`)");
    }
    if (!in_array('reply_on', $f))
    {
      $db->Execute("ALTER TABLE `{$t}` ADD `reply_on` INT(11)  NULL  DEFAULT '0'  AFTER `reply_all`");
      $db->Execute("ALTER TABLE `{$t}` ADD INDEX (`reply_on`)");
    }
    if (!in_array('image', $f))
    {
      $db->Execute("ALTER TABLE `{$t}` ADD `image` VARCHAR(255)  NULL  DEFAULT ''  AFTER `name`");
    }
  }
}
$f = $db->getCol("SHOW FIELDS FROM `bbc_account`");
if (!in_array('image', $f))
{
  $db->Execute("ALTER TABLE `bbc_account` ADD `image` VARCHAR(255)  NULL  DEFAULT ''  AFTER `name`");
}
$q = "CREATE TABLE IF NOT EXISTS `bbc_alert` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT 'user_id=0 && group_id=0 means all user',
  `group_id` int(11) DEFAULT '0' COMMENT '>0 for all members of the group',
  `module` varchar(60) DEFAULT '',
  `title` varchar(60) DEFAULT '',
  `description` varchar(150) DEFAULT '',
  `params` text,
  `is_open` tinyint(1) DEFAULT '0',
  `is_admin` tinyint(1) DEFAULT '3' COMMENT '0=memberlogin, 1=admin, 2=public, 3=any',
  `updated` datetime DEFAULT '0000-00-00 00:00:00',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`),
  KEY `is_open` (`is_open`),
  KEY `is_admin` (`is_admin`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Jika ingin me-notif semua member dalam suatu group maka harus memasukkan satu2 tiap user_id dalam group tersebut, karena jika mengunakan group_id maka jika salah satu user dalam group_id tsb membuka maka alert untuk yg lain akan hilang'
";
$db->Execute($q);
$f = $db->getCol("SHOW FIELDS FROM `bbc_alert`");
if (!in_array('is_admin', $f))
{
  $db->Execute("ALTER TABLE `bbc_alert` ADD `is_admin` TINYINT(1)  NULL  DEFAULT '3'  COMMENT '0=memberlogin, 1=admin, 2=public, 3=any'  AFTER `is_open`");
  $db->Execute("ALTER TABLE `bbc_alert` ADD INDEX (`is_admin`)");
  $db->Execute("ALTER TABLE `bbc_alert` ADD INDEX (`created`)");
}

$db->Execute("UPDATE `bbc_cpanel` SET `title`='Third Party App', `image`='application.png', `act`='application', `link`='index.php?mod=_cpanel.application' WHERE `id`=9");
$db->Execute("UPDATE `bbc_module` SET `is_config`=1 WHERE `name`='_cpanel'");
$db->cache_clean();