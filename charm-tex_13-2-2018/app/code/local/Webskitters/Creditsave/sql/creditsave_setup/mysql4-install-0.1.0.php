<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('creditsave')};
CREATE TABLE {$this->getTable('creditsave')} (
  `creditsave_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_entiry_id` varchar(255) NOT NULL DEFAULT '',
  `order_increment_id` varchar(255) NOT NULL DEFAULT '',
  `card_name` text NOT NULL,
  `card_type` varchar(255) NOT NULL DEFAULT '0',
  `card_no` varchar(255) NOT NULL DEFAULT '',
  `card_exp_month` varchar(255) NOT NULL DEFAULT '0',
  `card_exp_year` varchar(255) NOT NULL DEFAULT '0',
  `card_cvv` varchar(255) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`creditsave_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 