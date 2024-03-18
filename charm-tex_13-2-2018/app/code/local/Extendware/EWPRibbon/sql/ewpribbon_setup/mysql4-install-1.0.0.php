<?php
Mage::helper('ewcore/cache')->clean();
$installer = $this;
$installer->startSetup();

$command = "
SET foreign_key_checks = 0;
DROP TABLE IF EXISTS `ewpribbon_image`;
CREATE TABLE `ewpribbon_image` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `namespace` enum('default','custom') NOT NULL DEFAULT 'custom',
  `path` varchar(255) NOT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`image_id`),
  UNIQUE KEY `idx_path` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
DROP TABLE IF EXISTS `ewpribbon_ribbon`;
CREATE TABLE `ewpribbon_ribbon` (
  `ribbon_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'disabled',
  `rule_processing_mode` enum('stop','continue') NOT NULL,
  `hide_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `default_condition_state` enum('pass','fail') NOT NULL DEFAULT 'fail',
  `name` varchar(255) NOT NULL,
  `product_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `product_position` enum('top_left','top_center','top_right','middle_left','middle_center','middle_right','bottom_left','bottom_center','bottom_right') NOT NULL DEFAULT 'top_left',
  `product_text` varchar(255) NOT NULL,
  `product_image_id` int(11) unsigned DEFAULT NULL,
  `product_text_style` text NOT NULL,
  `product_image_style` text NOT NULL,
  `product_container_style` text NOT NULL,
  `product_inner_container_style` text NOT NULL,
  `category_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `category_position` enum('top_left','top_center','top_right','middle_left','middle_center','middle_right','bottom_left','bottom_center','bottom_right') NOT NULL DEFAULT 'top_left',
  `category_text` varchar(255) NOT NULL,
  `category_image_id` int(11) unsigned DEFAULT NULL,
  `category_text_style` text NOT NULL,
  `category_image_style` text NOT NULL,
  `category_container_style` text NOT NULL,
  `category_inner_container_style` text NOT NULL,
  `has_products` tinyint(3) unsigned NOT NULL,
  `conditions` text NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `from_date` datetime DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`ribbon_id`),
  KEY `idx_product_image_id` (`product_image_id`),
  KEY `idx_category_image_id` (`category_image_id`),
  KEY `idx_from_date` (`from_date`),
  KEY `idx_to_date` (`to_date`),
  CONSTRAINT `fk_d5vmnkyvgfsagap` FOREIGN KEY (`product_image_id`) REFERENCES `ewpribbon_image` (`image_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_syc2xdete5mm99o` FOREIGN KEY (`category_image_id`) REFERENCES `ewpribbon_image` (`image_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ewpribbon_ribbon_store`;
CREATE TABLE `ewpribbon_ribbon_store` (
  `ribbon_store_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ribbon_id` int(10) unsigned NOT NULL,
  `store_id` smallint(10) unsigned NOT NULL,
  PRIMARY KEY (`ribbon_store_id`),
  KEY `idx_ribbon_id` (`ribbon_id`),
  KEY `idx_store_id` (`store_id`),
  CONSTRAINT `fk_n5xej5ydxbrci0f` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nw23qo0i5tzxge0` FOREIGN KEY (`ribbon_id`) REFERENCES `ewpribbon_ribbon` (`ribbon_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ewpribbon_ribbon_product`;
CREATE TABLE `ewpribbon_ribbon_product` (
  `ribbon_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('included','excluded') NOT NULL DEFAULT 'included',
  `ribbon_id` int(11) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `from_date` datetime DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  PRIMARY KEY (`ribbon_product_id`),
  KEY `idx_ribbon_id` (`ribbon_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_from_date` (`from_date`),
  KEY `idx_to_date` (`to_date`),
  CONSTRAINT `fk_c7cmbpy725ablws` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_r6muktvd9of5243` FOREIGN KEY (`ribbon_id`) REFERENCES `ewpribbon_ribbon` (`ribbon_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET foreign_key_checks = 1;
";

$command = @preg_replace('/(EXISTS\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
$command = @preg_replace('/(REFERENCES\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
$command = @preg_replace('/(TABLE\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
if ($command) $installer->run($command);
$installer->endSetup(); 

// add default images to database
Mage::getModel('ewpribbon/image')->getResource()->syncWithFileSystem();


$c1= Mage::getModel('ewpribbon/image')->loadByPath('default/sticker/1/blue-small.png');
$p1 = Mage::getModel('ewpribbon/image')->loadByPath('default/ribbon/2/rh/red.png');
$c2 = Mage::getModel('ewpribbon/image')->loadByPath('default/bubble/2/red-small.png');
$p2 = Mage::getModel('ewpribbon/image')->loadByPath('default/bubble/2/blue-large.png');

if ($c1->getId() and $p1->getId() and $c2->getId() and $p2->getId()) {
	$command = "
		INSERT INTO `ewpribbon_ribbon` VALUES ('1', 'enabled', 'continue', 'enabled', 'fail', 'Example Ribbon #1', 'enabled', 'top_right', 'Featured Product', '" . $p1->getId() . "', 'font-size: 14px; color: white; font-weight: bold; text-align: center; margin-top: 6px;', '', 'z-index: 9999999; margin-left: 10px;', '', 'enabled', 'top_left', 'HOT!', '" . $c1->getId() . "', 'font-size: 12px; text-align: center; margin-top: 28px; color: white; font-weight: bold;', '', 'z-index: 999;', '', '0', 'a:1:{i:0;a:2:{s:10:\"sort_order\";i:0;s:10:\"conditions\";a:1:{i:1;a:3:{s:9:\"attribute\";s:5:\"price\";s:9:\"condition\";s:4:\"gteq\";s:5:\"value\";s:3:\"200\";}}}}', '0', null, null, now(), now());
		INSERT INTO `ewpribbon_ribbon` VALUES ('2', 'enabled', 'continue', 'enabled', 'pass', 'Example Ribbon #2', 'enabled', 'bottom_center', 'On Sale!',  '" . $p2->getId() . "', 'font-size: 15px; margin-top: 18px; text-align: center; color: white; font-size: 20px; font-weight: bold;', '', 'z-index: 9999999; margin-top: 15px', '', 'enabled', 'bottom_center', 'On Sale!', '" . $c2->getId() . "', 'font-weight: 12px; margin-top: 8px; text-align: center; color: white; font-weight: bold;', '', 'z-index: 999; margin-top: 7px;', '', '0', 'a:0:{}', '0', null, null, now(), now());
	";

	$command = @preg_replace('/(INTO\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
	$command = @preg_replace('/(EXISTS\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
	$command = @preg_replace('/(REFERENCES\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
	$command = @preg_replace('/(TABLE\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
	if ($command) $installer->run($command);
	
	try {
		$stores = Mage::getModel('core/store')->getCollection();
		$stores->addFieldToFilter('store_id', array('neq' => 0));
		$storeIds = $stores->getAllIds();

		$collection = Mage::getModel('ewpribbon/ribbon')->getCollection();
		foreach ($collection as $ribbon) {
			$ribbon->setStoreIds($storeIds)->save();
		}
	} catch (Exception $e) {}
}

// [[if normal]]
try {
	$incompatModules = array('Fooman_Speedster', 'GT_Speed', 'Magefox_Minify', 'Apptrian_Minify', 'Fooman_SpeedsterEnterprise', 'Fooman_SpeedsterAdvanced', 'Diglin_UIOptimization', 'Jemoon_Htmlminify');
	foreach ($incompatModules as $module) {
		$model = Mage::getSingleton('ewcore/module');
		if (!$model) continue;
		
		$module = $model->load($module);
		if ($module->isActive() === false) continue;
		
		Mage::getModel('compiler/process')->registerIncludePath(false);
		$configTools = Mage::helper('ewcore/config_tools');
		if ($configTools) $configTools->disableModule($module->getId());
	}
} catch (Exception $e) {}
// [[/if]]