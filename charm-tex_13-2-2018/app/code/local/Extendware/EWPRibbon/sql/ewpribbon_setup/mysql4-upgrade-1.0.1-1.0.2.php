<?php

$installer = $this;
$installer->startSetup();

$command  = "
/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/
ALTER TABLE `ewpribbon_ribbon` DROP FOREIGN KEY `fk_d5vmnkyvgfsagap` ;
ALTER TABLE `ewpribbon_ribbon` DROP FOREIGN KEY `fk_syc2xdete5mm99o` ;

/* Alter table in target */
ALTER TABLE `ewpribbon_ribbon` 
	ADD COLUMN `product_ref_selectors` text  COLLATE utf8_general_ci NOT NULL after `product_inner_container_style`, 
	CHANGE `category_status` `category_status` enum('enabled','disabled')  COLLATE utf8_general_ci NOT NULL DEFAULT 'enabled' after `product_ref_selectors`, 
	CHANGE `category_position` `category_position` enum('top_left','top_center','top_right','middle_left','middle_center','middle_right','bottom_left','bottom_center','bottom_right')  COLLATE utf8_general_ci NOT NULL DEFAULT 'top_left' after `category_status`, 
	CHANGE `category_text` `category_text` varchar(255)  COLLATE utf8_general_ci NOT NULL after `category_position`, 
	CHANGE `category_image_id` `category_image_id` int(11) unsigned   NULL after `category_text`, 
	CHANGE `category_text_style` `category_text_style` text  COLLATE utf8_general_ci NOT NULL after `category_image_id`, 
	CHANGE `category_image_style` `category_image_style` text  COLLATE utf8_general_ci NOT NULL after `category_text_style`, 
	CHANGE `category_container_style` `category_container_style` text  COLLATE utf8_general_ci NOT NULL after `category_image_style`, 
	CHANGE `category_inner_container_style` `category_inner_container_style` text  COLLATE utf8_general_ci NOT NULL after `category_container_style`, 
	ADD COLUMN `category_ref_selectors` text  COLLATE utf8_general_ci NOT NULL after `category_inner_container_style`, 
	CHANGE `has_products` `has_products` tinyint(3) unsigned   NOT NULL after `category_ref_selectors`, 
	CHANGE `conditions` `conditions` text  COLLATE utf8_general_ci NOT NULL after `has_products`, 
	CHANGE `priority` `priority` int(10) unsigned   NOT NULL after `conditions`, 
	CHANGE `from_date` `from_date` datetime   NULL after `priority`, 
	CHANGE `to_date` `to_date` datetime   NULL after `from_date`, 
	CHANGE `updated_at` `updated_at` datetime   NOT NULL after `to_date`, 
	CHANGE `created_at` `created_at` datetime   NOT NULL after `updated_at`;
 

/* The foreign keys that were dropped are now re-created*/
ALTER TABLE `ewpribbon_ribbon`
ADD CONSTRAINT `fk_d5vmnkyvgfsagap` 
FOREIGN KEY (`product_image_id`) REFERENCES `ewpribbon_image` (`image_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ewpribbon_ribbon`
ADD CONSTRAINT `fk_syc2xdete5mm99o` 
FOREIGN KEY (`category_image_id`) REFERENCES `ewpribbon_image` (`image_id`) ON DELETE SET NULL ON UPDATE CASCADE;
";

$command = @preg_replace('/(EXISTS\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
$command = @preg_replace('/(ON\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
$command = @preg_replace('/(REFERENCES\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);
$command = @preg_replace('/(TABLE\s+`)([a-z0-9\_]+?)(`)/ie', '"\\1" . $this->getTable("\\2") . "\\3"', $command);

$installer->run($command);


$installer->endSetup();
