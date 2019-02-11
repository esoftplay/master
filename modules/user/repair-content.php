<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/* FROM content_fetch_admin() and content_fetch() */
$f = $db->getCol("SHOW FIELDS FROM `bbc_content`");
if (in_array('par_id', $f))
{
  if (!empty($_GET['redirect']))
  {
    redirect($_GET['redirect']);
  }else{
    redirect();
  }
}
$r = debug_backtrace();
$f = !empty($r[0]) ? $r[0]['file'].':'.$r[0]['line'] : '';
@file_put_contents(_ROOT.'images/repair_content_log.txt', date('Y-m-d H:i:s ').$f."\n".print_r(array('REFERER' => @$_SERVER['HTTP_REFERER'], 'URI' => @$_SERVER['REQUEST_URI']), 1)."\n\n", FILE_APPEND);

$q = "
ALTER TABLE `bbc_content` ADD `par_id` INT(255)  NULL  DEFAULT '0' AFTER `id`;
ALTER TABLE `bbc_content` ADD `kind_id` INT(1)  NULL  DEFAULT '0' COMMENT '0=content, 1=gallery, 2=download, 3=video, 4=audio' AFTER `type_id`;
ALTER TABLE `bbc_content` ADD `file` VARCHAR(255)  NULL  DEFAULT ''  AFTER `kind_id`;
ALTER TABLE `bbc_content` ADD `file_url` VARCHAR(255)  NULL  DEFAULT ''  AFTER `file`;
ALTER TABLE `bbc_content` ADD `file_format` VARCHAR(12)  NULL  DEFAULT ''  AFTER `file_url`;
ALTER TABLE `bbc_content` ADD `file_type` INT(1)  NULL  DEFAULT '0' COMMENT '0=local file, 1=tirth party file' AFTER `file_format`;
ALTER TABLE `bbc_content` ADD `file_register` TINYINT(1)  NULL  DEFAULT '0' COMMENT '0=free download, 1=must register' AFTER `file_type`;
ALTER TABLE `bbc_content` ADD `file_hit` INT(255)  NULL  DEFAULT '0'  AFTER `file_register`;
ALTER TABLE `bbc_content` ADD `file_hit_time` DATETIME  NULL  DEFAULT '0000-00-00 00:00:00'  AFTER `file_hit`;
ALTER TABLE `bbc_content` ADD `file_hit_ip` VARCHAR(25)  NULL  DEFAULT ''  AFTER `file_hit_time`;
ALTER TABLE `bbc_content` ADD `video` VARCHAR(255)  NULL  DEFAULT ''  AFTER `file_hit_ip`;
ALTER TABLE `bbc_content` ADD `audio` VARCHAR(255)  NULL  DEFAULT ''  AFTER `video`;
ALTER TABLE `bbc_content` ADD `caption` VARCHAR(255)  NULL  DEFAULT ''  AFTER `image`;
ALTER TABLE `bbc_content` ADD `images` TEXT  NULL  DEFAULT ''  AFTER `caption`;
ALTER TABLE `bbc_content` ADD `privilege` VARCHAR(255)  NULL  DEFAULT ',all,' COMMENT 'all=any user, 1 >= user group (bbc_user_group.id)'  AFTER `revised`;
ALTER TABLE `bbc_content` DROP INDEX `publish`;
ALTER TABLE `bbc_content` DROP INDEX `is_front`;
ALTER TABLE `bbc_content` DROP INDEX `created_by`;
ALTER TABLE `bbc_content` DROP INDEX `type_id`;
ALTER TABLE `bbc_content` DROP INDEX `title`;
ALTER TABLE `bbc_content` ADD INDEX (`par_id`);
ALTER TABLE `bbc_content` ADD INDEX (`type_id`);
ALTER TABLE `bbc_content` ADD INDEX (`kind_id`);
ALTER TABLE `bbc_content` ADD FULLTEXT KEY `title` (`caption`,`created_by_alias`);
ALTER TABLE `bbc_content` ADD INDEX (`created_by`);
ALTER TABLE `bbc_content` ADD INDEX (`is_front`);
ALTER TABLE `bbc_content` ADD INDEX (`publish`);
CREATE TABLE IF NOT EXISTS `bbc_content_registrant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL DEFAULT '',
  `email` varchar(120) NOT NULL DEFAULT '',
  `phone` varchar(30) NOT NULL DEFAULT '',
  `address` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
TRUNCATE TABLE `bbc_content_related`;
";
$r = explode(';', $q);
foreach ($r as $q)
{
	$q = trim($q);
	if (!empty($q))
	{
		$db->Execute($q);
	}
}
$i = $sys->get_module_id('content');
$q = "SELECT 1 FROM `bbc_email` WHERE  `module_id`={$i} AND `name`='download_register'";
if (!$db->getOne($q))
{
  $q = "INSERT INTO `bbc_email` SET
    `module_id`      = '{$i}',
    `name`           = 'download_register',
    `global_subject` = '1',
    `global_footer`  = '1',
    `global_email`   = '1',
    `from_email`     = '',
    `from_name`      = '',
    `is_html`        = '0',
    `description`    = 'register to download'";
  $db->Execute($q);
  $j = $db->Insert_ID();
  if ($j > 0)
  {
    $r = lang_assoc();
    foreach ($r as $k => $d)
    {
      $q = "INSERT INTO `bbc_email_text` SET
        `email_id` = {$j},
        `subject`  = 'Download Your File ([title])',
        `content`  = 'Silahkan download file anda dengan klik link dibawah ini<br />\r\n[url]<br />\r\n<br />\r\n',
        `lang_id`  = {$k}";
      $db->Execute($q);
    }
  }
}
$sys->clean_cache();