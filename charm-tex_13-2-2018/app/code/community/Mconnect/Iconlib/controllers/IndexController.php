<?php
  /**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */ 
?>
<?php
class Mconnect_Iconlib_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/iconlib?id=15 
    	 *  or
    	 * http://site.com/iconlib/id/15 	
    	 */
    	/* 
		$iconlib_id = $this->getRequest()->getParam('id');

  		if($iconlib_id != null && $iconlib_id != '')	{
			$iconlib = Mage::getModel('iconlib/iconlib')->load($iconlib_id)->getData();
		} else {
			$iconlib = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($iconlib == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$iconlibTable = $resource->getTableName('iconlib');
			
			$select = $read->select()
			   ->from($iconlibTable,array('iconlib_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$iconlib = $read->fetchRow($select);
		}
		Mage::register('iconlib', $iconlib);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}