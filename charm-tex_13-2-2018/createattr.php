<?php
require_once('app/Mage.php');
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
$setup =  new Mage_Sales_Model_Mysql4_Setup;

$setup->addAttribute('catalog_category', 'is_featured', array(
    'group'                => 'General Information',
    'type'              => 'int',//can be int, varchar, decimal, text, datetime
    'backend'           => '',
    'frontend_input'    => '',
    'frontend'          => '',
    'label'             => 'Is Featured',
    'input'             => 'select', //text, textarea, select, file, image, multilselect
    'default' => array(0),
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',//this is necessary for select and multilelect, for the rest leave it blank
    'global'             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,//scope can be SCOPE_STORE or SCOPE_GLOBAL or SCOPE_WEBSITE
    'visible'           => true,
    'frontend_class'     => '',
    'required'          => false,//or true
    'user_defined'      => true,
    'default'           => '0',
    'position'            => 100,//any number will do
));
$setup->endSetup();
?>