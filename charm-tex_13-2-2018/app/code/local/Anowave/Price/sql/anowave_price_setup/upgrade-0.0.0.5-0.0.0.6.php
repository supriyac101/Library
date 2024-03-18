<?php
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$entities = array
(
    'quote',
    'quote_address',
    'quote_item',
    'quote_address_item',
    'order',
    'order_item'
);

foreach ($entities as $entity) 
{
    $installer->addAttribute($entity, 'customer_discount', array
	(
	    'type'     => Varien_Db_Ddl_Table::TYPE_DECIMAL,
	    'visible'  => true,
	    'required' => false
	));
}

$installer->endSetup();