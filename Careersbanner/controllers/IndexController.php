<?php
class Custom_Careersbanner_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/careersbanner?id=15 
    	 *  or
    	 * http://site.com/careersbanner/id/15 	
    	 */
    	/* 
		$careersbanner_id = $this->getRequest()->getParam('id');

  		if($careersbanner_id != null && $careersbanner_id != '')	{
			$careersbanner = Mage::getModel('careersbanner/careersbanner')->load($careersbanner_id)->getData();
		} else {
			$careersbanner = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($careersbanner == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$careersbannerTable = $resource->getTableName('careersbanner');
			
			$select = $read->select()
			   ->from($careersbannerTable,array('careersbanner_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$careersbanner = $read->fetchRow($select);
		}
		Mage::register('careersbanner', $careersbanner);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}