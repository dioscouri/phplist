CREATE TABLE IF NOT EXISTS `#__phplist_config` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM ;
