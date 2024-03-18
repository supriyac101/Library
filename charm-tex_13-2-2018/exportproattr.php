<?php
require_once('app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
    /**
     * Get the resource model
     */
    $resource = Mage::getSingleton('core/resource');
     
    /**
     * Retrieve the read connection
     */
    $readConnection = $resource->getConnection('core_read');
     
    /**
     * Retrieve the write connection
     */
    $writeConnection = $resource->getConnection('core_write');
    /**
     * Get the table name
     */
    $tableName = $resource->getTableName('catalog_product_option');

    $collection=Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
    foreach($collection as $product){
        $productCollection = Mage::getModel("catalog/product")->load($product->getId());
        $options = $productCollection->getProductOptionsCollection();
        if (isset($options)) {
            if(count($options) == 4){
                echo "<u>".$product->getName()."</u><br><br>";
                foreach ($options as $o) {
                    //if($o->getTitle() == 'Seat Material') {
                        //$o->setSortOrder(1)->save();
                        echo $o->getTitle()."++++++++".$o->getSortOrder();
                        echo "<br>";
                        //echo $qry = "UPDATE ".$tableName." SET `sort_order` = 2 where `option_id` = ".$o->getId();
                        /**
                        * Execute the query
                        */
                        //$writeConnection->query($qry);
                        //echo "<br>";
                        /*$options2 = array(
                            'sort_order' => 0,
                        );*/
                    //}
                }
            }
        }
    }
?>