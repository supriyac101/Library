<?php
/**
 * Discount For Order Extension
 *
 * @category   QS
 * @package    QS_Discount4Order
 * @author     Quart-soft Magento Team <magento@quart-soft.com>
 * @copyright  Copyright (c) 2010 Quart-soft Ltd http://quart-soft.com
 */

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('discount4order/customers')}`;
CREATE TABLE `{$this->getTable('discount4order/customers')}` (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `last_order_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();



