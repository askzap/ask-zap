
DROP TABLE IF EXISTS rus_spsr_invoices_items;
CREATE TABLE IF NOT EXISTS `?:rus_spsr_invoices_items` (
  `invoice_item_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `register_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `item_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `barcode` bigint(20) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL DEFAULT '',
  `ship_ref_num` varchar(32) NOT NULL DEFAULT '',
  `shipment_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoice_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
