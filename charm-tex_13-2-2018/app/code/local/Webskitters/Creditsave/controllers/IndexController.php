<?php
class Webskitters_Creditsave_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/creditsave?id=15 
    	 *  or
    	 * http://site.com/creditsave/id/15 	
    	 */
    	/* 
		$creditsave_id = $this->getRequest()->getParam('id');

  		if($creditsave_id != null && $creditsave_id != '')	{
			$creditsave = Mage::getModel('creditsave/creditsave')->load($creditsave_id)->getData();
		} else {
			$creditsave = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($creditsave == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$creditsaveTable = $resource->getTableName('creditsave');
			
			$select = $read->select()
			   ->from($creditsaveTable,array('creditsave_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$creditsave = $read->fetchRow($select);
		}
		Mage::register('creditsave', $creditsave);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}