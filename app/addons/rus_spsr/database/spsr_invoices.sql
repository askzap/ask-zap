
DROP TABLE IF EXISTS rus_spsr_invoices;
CREATE TABLE IF NOT EXISTS `?:rus_spsr_invoices` (
  `invoice_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `register_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `shipment_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `ship_ref_num` varchar(32) NOT NULL DEFAULT '',
  `invoice_number` bigint(20) unsigned NOT NULL DEFAULT '0',
  `barcodes` varchar(255) NOT NULL DEFAULT '',
  `client_barcodes` varchar(255) NOT NULL DEFAULT '',
  `tariff_code` varchar(5) NOT NULL DEFAULT '',
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `courier_key` varchar(32) NOT NULL DEFAULT '',
  `courier_id` int(9) NOT NULL DEFAULT '0',
  `courier_owner_id` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
