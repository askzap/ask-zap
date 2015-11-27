
DROP TABLE IF EXISTS rus_sdek_products;
CREATE TABLE IF NOT EXISTS `?:rus_sdek_products` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `register_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) NOT NULL,
  `shipment_id` mediumint(8) NOT NULL,
  `ware_key` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(12,2)	NOT NULL DEFAULT '0',
  `amount` mediumint(8) NOT NULL,
  `total` decimal(12,2)	NOT NULL DEFAULT '0',
  `weight` int(9) NOT NULL,  
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
