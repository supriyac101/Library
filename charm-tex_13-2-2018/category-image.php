<?php
require_once('app/Mage.php');
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
$installer = new Mage_Sales_Model_Mysql4_Setup;
$attribute  = array(
    'type' => 'varchar',
    'label'=> 'Menu Image',
    'input' => 'image',
    'backend' => 'catalog/category_attribute_backend_image',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => 0,
    'source' => 'eav/entity_attribute_source_boolean',
    'group' => "General Information"
);
$installer->addAttribute('catalog_category', 'category_menu_image', $attribute);
$installer->endSetup();
?>
