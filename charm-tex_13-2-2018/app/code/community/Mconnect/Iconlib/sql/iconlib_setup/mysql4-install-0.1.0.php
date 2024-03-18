<?php
  /**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */ 
?>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('iconlib')};
CREATE TABLE {$this->getTable('iconlib')} (
  `iconlib_id` int(11) unsigned NOT NULL auto_increment,
  `iconlabel` varchar(255) NOT NULL default '',
  `iconfilename` varchar(100) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',    
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`iconlib_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('iconlib_to_products_relation')};
CREATE TABLE {$this->getTable('iconlib_to_products_relation')} (
`iconlib_to_products_relation_id` INT NOT NULL auto_increment,
`productid` INT NOT NULL ,
`iconid` INT NOT NULL ,
`created_time` datetime NULL,
`update_time` datetime NULL,
PRIMARY KEY (`iconlib_to_products_relation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/general/icondisplaylimit','3');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/general/icontoproductassociationlimit','3');");

$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconsettings/icondisplaystyleenabled','1');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconsettings/iconimgborderenabled','1');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconsettings/iconimgwidth','100');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconsettings/iconimgheight','85');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconsettings/icondisplayrandomlyenabled','1');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconsettings/icondisplaylabelenabled','1');");

$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconblocksettings/icondisplayblocktitle','PRODUCT ICONS');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconblocksettings/icondisplayleftblockenabled','1');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconblocksettings/icondisplayrightblockenabled','1');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','iconlib/advancediconblocksettings/icondisplaycontentblockenabled','1');");

$installer->endSetup(); 