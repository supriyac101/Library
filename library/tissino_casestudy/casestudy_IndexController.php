<?php
class Custom_Casestudy_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/casestudy?id=15 
    	 *  or
    	 * http://site.com/casestudy/id/15 	
    	 */
    	/* 
		$casestudy_id = $this->getRequest()->getParam('id');

  		if($casestudy_id != null && $casestudy_id != '')	{
			$casestudy = Mage::getModel('casestudy/casestudy')->load($casestudy_id)->getData();
		} else {
			$casestudy = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($casestudy == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$casestudyTable = $resource->getTableName('casestudy');
			
			$select = $read->select()
			   ->from($casestudyTable,array('casestudy_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$casestudy = $read->fetchRow($select);
		}
		Mage::register('casestudy', $casestudy);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    public function viewAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/casestudy?id=15 
    	 *  or
    	 * http://site.com/casestudy/id/15 	
    	 */
    	 
		$casestudy_id = $this->getRequest()->getParam('id');

  		if($casestudy_id != null && $casestudy_id != '') {
			$casestudy = Mage::getModel('casestudy/casestudy')->load($casestudy_id)->getData();
            echo "<pre>";
            print_r($casestudy);
            echo "</pre>";
		} else {
			$casestudy = null;
		}
		
		/*
    	 * If no param we load a the last created item
    	 */
    	
    	if($casestudy == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$casestudyTable = $resource->getTableName('casestudy');
			
			$select = $read->select()
			   ->from($casestudyTable,array('casestudy_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC');
			   
			$casestudy = $read->fetchRow($select);
		}
		Mage::register('casestudy', $casestudy);
		

			
		$this->loadLayout();
        $this->renderLayout();
    }
    
    public function filtercaseAction(){
        $casecat = $this->getRequest()->getParam('casecat');
        $collection = Mage::getModel('casestudy/casestudy')->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('casecat',$casecat);
        $i=1;
        $_responseData = '';
        foreach ($collection as $case) {
            $_responseData .='
            <li><a href="javascript:void(0);" title="'.$case->getTitle().'" data="'.$case->getId().'">
                <img title="'.$case->getTitle().'" alt="'.$case->getTitle().'" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/'.$case->getFilename().'" />
                <h2>'.$case->getTitle().'</h2>
                <h3>'.$case->getLocation().'</h3></a>
                <p><strong>'.$case->getShortDescription().'</strong></p>'
                .$case->getContent().
            '</li>';
            $i++;
        }
        $_response['caselist'] = $_responseData;
        return $this->getResponse()->setHeader('Content-Type','application/json')->setBody(Mage::helper('core')->jsonEncode($_response));
    }
}