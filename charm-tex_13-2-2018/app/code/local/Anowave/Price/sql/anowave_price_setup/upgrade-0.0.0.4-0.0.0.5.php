<?php
$this->startSetup();

$sql = array();

$sql[] = "ALTER TABLE " . Mage::getConfig()->getTablePrefix() . "anowave_customerprice ADD price_apply_further BOOLEAN NOT NULL DEFAULT FALSE";
	
foreach ($sql as $query)
{
	$this->run($query);
}

$this->endSetup();