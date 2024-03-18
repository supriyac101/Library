<?php
class Custom_Careers_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/careers?id=15 
    	 *  or
    	 * http://site.com/careers/id/15 	
    	 */
    	/* 
		$careers_id = $this->getRequest()->getParam('id');

  		if($careers_id != null && $careers_id != '')	{
			$careers = Mage::getModel('careers/careers')->load($careers_id)->getData();
		} else {
			$careers = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($careers == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$careersTable = $resource->getTableName('careers');
			
			$select = $read->select()
			   ->from($careersTable,array('careers_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$careers = $read->fetchRow($select);
		}
		Mage::register('careers', $careers);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function viewAction(){
        $careers_id = $this->getRequest()->getParam('id');
        if($careers_id != null && $careers_id != '')	{
			$careers = Mage::getModel('careers/careers')->load($careers_id)->getData();
            /*echo "<pre>";
            print_r($careers);
            echo "</pre>";*/
            /*echo "<h3>".$careers[title]."</h3><br>";
            echo "<p>".$careers[location]."</p><br>";
            echo "<p>".$careers[content]."</p><br>";*/
            //Mage::register('title', $careers[title]);
            $block = Mage::getSingleton('careers/view');
            $block->setId($careers[careers_id]);
            $block->setTitle($careers[title]);
            $block->setLocation($careers[location]);
            $block->setContent($careers[content]);
            $this->loadLayout();
            $this->renderLayout(); 
		} else {
			$careers = null;
		}
        
        
    }
}