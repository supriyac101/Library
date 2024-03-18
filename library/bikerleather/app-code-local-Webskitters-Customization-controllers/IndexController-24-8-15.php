<?php
class Webskitters_Customization_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/customization?id=15 
    	 *  or
    	 * http://site.com/customization/id/15 	
    	 */
    	/* 
		$customization_id = $this->getRequest()->getParam('id');

  		if($customization_id != null && $customization_id != '')	{
			$customization = Mage::getModel('customization/customization')->load($customization_id)->getData();
		} else {
			$customization = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($customization == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$customizationTable = $resource->getTableName('customization');
			
			$select = $read->select()
			   ->from($customizationTable,array('customization_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$customization = $read->fetchRow($select);
		}
		Mage::register('customization', $customization);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    public function productAction()
    {
	$_productId = Mage::app()->getRequest()->getParam('proid');
	//echo $_productId; exit;
	$_productCollection = Mage::getResourceModel('catalog/product_collection')
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('entity_id', $_productId);
	foreach($_productCollection as $_product):
	    //echo $_product->getName(); exit;
	    $response['price'] = $_product->getFinalPrice();
	    $response['imgurl'] = $_product->getImageUrl();
	endforeach;
	
	return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
    }
}