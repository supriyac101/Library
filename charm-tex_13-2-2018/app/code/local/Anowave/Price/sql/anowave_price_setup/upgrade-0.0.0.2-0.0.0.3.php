<?php
$installer = $this;
 
$installer->startSetup();

$sql = array();

$sql[] = "SET foreign_key_checks = 0";

$sql[] = "CREATE TABLE IF NOT EXISTS " . Mage::getConfig()->getTablePrefix() . "anowave_customerprice (price_id int(6) NOT NULL AUTO_INCREMENT,price_customer_id int(6) NOT NULL DEFAULT '0',price_product_id int(6) NOT NULL DEFAULT '0',price_type tinyint(1) NOT NULL DEFAULT '1',price decimal(8,2) NOT NULL DEFAULT '0.00',price_discount decimal(8,2) NOT NULL DEFAULT '0.00',PRIMARY KEY (price_id)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
$sql[] = "CREATE TABLE IF NOT EXISTS " . Mage::getConfig()->getTablePrefix() . "anowave_customerprice_global (price_global_id int(6) NOT NULL AUTO_INCREMENT,price_global_customer_id int(10) unsigned NOT NULL,price_global_discount smallint(3) NOT NULL DEFAULT '0',price_global_valid_from timestamp NULL DEFAULT NULL,price_global_valid_to timestamp NULL DEFAULT NULL,PRIMARY KEY (price_global_id),UNIQUE KEY price_global_customer_id (price_global_customer_id),CONSTRAINT anowave_customerprice_global_ibfk_1 FOREIGN KEY (price_global_customer_id) REFERENCES " . Mage::getConfig()->getTablePrefix() . "customer_entity (entity_id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
$sql[] = "CREATE TABLE IF NOT EXISTS " . Mage::getConfig()->getTablePrefix() . "anowave_customerprice_category (price_category_primary_id int(6) NOT NULL AUTO_INCREMENT,price_id int(6) NOT NULL,price_category_id int(10) unsigned NOT NULL,PRIMARY KEY (price_category_primary_id),UNIQUE KEY price_id (price_id,price_category_id),KEY price_category_id (price_category_id),CONSTRAINT anowave_customerprice_category_ibfk_1 FOREIGN KEY (price_id) REFERENCES " . Mage::getConfig()->getTablePrefix() . "anowave_customerprice (price_id) ON DELETE CASCADE ON UPDATE CASCADE,CONSTRAINT anowave_customerprice_category_ibfk_2 FOREIGN KEY (price_category_id) REFERENCES " . Mage::getConfig()->getTablePrefix() . "catalog_category_entity (entity_id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";

$sql[] = "SET foreign_key_checks = 1";

foreach ($sql as $query)
{
	$installer->run($query);
}

$installer->endSetup();