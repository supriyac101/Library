<?php

$installer = $this;

$installer->startSetup();

$paths = array(
	'ewpribbon_developer/selector/fallback_position',
);

$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->addFieldToFilter('path', $paths);
foreach ($configCollection as $item) {
	$item->delete();
}

$installer->endSetup();
