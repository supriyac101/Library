<?php
class Webskitters_Mobilebanner_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/mobilebanner?id=15 
    	 *  or
    	 * http://site.com/mobilebanner/id/15 	
    	 */
    	/* 
		$mobilebanner_id = $this->getRequest()->getParam('id');

  		if($mobilebanner_id != null && $mobilebanner_id != '')	{
			$mobilebanner = Mage::getModel('mobilebanner/mobilebanner')->load($mobilebanner_id)->getData();
		} else {
			$mobilebanner = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($mobilebanner == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$mobilebannerTable = $resource->getTableName('mobilebanner');
			
			$select = $read->select()
			   ->from($mobilebannerTable,array('mobilebanner_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$mobilebanner = $read->fetchRow($select);
		}
		Mage::register('mobilebanner', $mobilebanner);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}