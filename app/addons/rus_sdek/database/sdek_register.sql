
DROP TABLE IF EXISTS rus_sdek_register;
CREATE TABLE IF NOT EXISTS `?:rus_sdek_register` (
  `register_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) NOT NULL,
  `shipment_id` mediumint(8) NOT NULL,
  `data` text NOT NULL,
  `data_xml` text NOT NULL,
  `timestamp` int(11) unsigned NOT NULL DEFAULT '0',
  `status` char(1) NOT NULL DEFAULT '',
  `tariff` int(11) NOT NULL,
  `dispatch_number` int(11) NOT NULL,
  `address_pvz` varchar(8) NOT NULL,
  `address` varchar(256) NOT NULL,
  `file_sdek` varchar(256) NOT NULL,
  `notes` varchar(256) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`register_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
