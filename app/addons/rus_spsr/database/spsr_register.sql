
DROP TABLE IF EXISTS rus_spsr_register;
CREATE TABLE IF NOT EXISTS `?:rus_spsr_register` (
  `register_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) NOT NULL DEFAULT '0',
  `session_id` int(11) unsigned NOT NULL DEFAULT '0',
  `session_owner_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL DEFAULT '',
  `data_xml` text NOT NULL DEFAULT '',
  `timestamp` int(11) unsigned NOT NULL DEFAULT '0',
  `status` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`register_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
