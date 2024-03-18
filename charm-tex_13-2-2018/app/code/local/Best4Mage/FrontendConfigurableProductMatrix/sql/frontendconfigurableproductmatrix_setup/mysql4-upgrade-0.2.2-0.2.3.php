<?php

$installer = $this;

$installer->startSetup();

$data= array (
	'attribute_set'	=> 'Default',
	'group' 		=> 'Product Matrix',
	'label'    		=> 'Display Tabs',
	'visible'     	=> true,
	'global' 		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'type'     		=> 'int',
	'input'    		=> 'select',
	'source'		=> 'frontendconfigurableproductmatrix/system_config_source_displaytabs',
	'system'   		=> true,
	'required' 		=> false,
	'user_defined' 	=> 1,
	'apply_to' 		=> 'configurable',
);

$installer->addAttribute('catalog_product','fcpm_display_tabs',$data);

if($attributeId = $installer->getAttributeId('catalog_product', 'fcpm_display_tabs'))
{
	$installer->updateTableRow('catalog/eav_attribute',
		'attribute_id', $attributeId,
		'apply_to', 'configurable'
	);
}

$data= array (
	'attribute_set'	=> 'Default',
	'group' 		=> 'Product Matrix',
	'label'    		=> 'Default Tab',
	'visible'     	=> true,
	'global' 		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'type'     		=> 'int',
	'input'    		=> 'select',
	'source'		=> 'frontendconfigurableproductmatrix/system_config_source_defaulttab',
	'system'   		=> true,
	'required' 		=> false,
	'user_defined' 	=> 1,
	'apply_to' 		=> 'configurable',
);

$installer->addAttribute('catalog_product','fcpm_default_tab',$data);

if($attributeId = $installer->getAttributeId('catalog_product', 'fcpm_default_tab'))
{
	$installer->updateTableRow('catalog/eav_attribute',
		'attribute_id', $attributeId,
		'apply_to', 'configurable'
	);
}

$data= array (
	'attribute_set'	=> 'Default',
	'group' 		=> 'Product Matrix',
	'label'    		=> 'Number of columns',
	'visible'     	=> true,
	'global' 		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'type'     		=> 'varchar',
	'input'    		=> 'text',
	'system'   		=> true,
	'required' 		=> false,
	'user_defined' 	=> 1,
	'apply_to' 		=> 'configurable',
);

$installer->addAttribute('catalog_product','fcpm_multiple_option',$data);

if($attributeId = $installer->getAttributeId('catalog_product', 'fcpm_multiple_option'))
{
	$installer->updateTableRow('catalog/eav_attribute',
		'attribute_id', $attributeId,
		'apply_to', 'configurable'
	);
}


$installer->endSetup();
