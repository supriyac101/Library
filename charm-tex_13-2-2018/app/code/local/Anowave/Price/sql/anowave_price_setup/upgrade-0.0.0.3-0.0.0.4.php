<?php
$installer = $this;
 
$installer->startSetup();

$indexes = $installer->getConnection()->fetchAll("SHOW INDEXES FROM " . Mage::getConfig()->getTablePrefix() . "anowave_customerprice WHERE Key_name = 'price_customer_id'");

if ($indexes)
{
	$sql = array();

	$sql[] = "ALTER TABLE " . Mage::getConfig()->getTablePrefix() . "anowave_customerprice DROP INDEX price_customer_id";
	
	foreach ($sql as $query)
	{
		$installer->run($query);
	}
}

$installer->endSetup();