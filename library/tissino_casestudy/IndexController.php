<?php
class Custom_Casestudybanner_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/casestudybanner?id=15 
    	 *  or
    	 * http://site.com/casestudybanner/id/15 	
    	 */
    	/* 
		$casestudybanner_id = $this->getRequest()->getParam('id');

  		if($casestudybanner_id != null && $casestudybanner_id != '')	{
			$casestudybanner = Mage::getModel('casestudybanner/casestudybanner')->load($casestudybanner_id)->getData();
		} else {
			$casestudybanner = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($casestudybanner == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$casestudybannerTable = $resource->getTableName('casestudybanner');
			
			$select = $read->select()
			   ->from($casestudybannerTable,array('casestudybanner_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$casestudybanner = $read->fetchRow($select);
		}
		Mage::register('casestudybanner', $casestudybanner);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function getbannerAction(){
        $caseid = $this->getRequest()->getParam('caseid');
        $_bannerCollection = Mage::getModel('casestudybanner/casestudybanner')->getCollection()
                        ->addFieldToFilter('status', 1)
                        ->addFieldToFilter('casestudy', $caseid);
        $k=1;
        $responseData = '';
        $responseData .= '<ul class="prof_banner owl-carousel owl-theme">';
        foreach($_bannerCollection as $banner){
            $responseData .= '<li><img title="'.$banner->getTitle().'" alt="'.$banner->getTitle().'" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/'.$banner->getFilename().'" /></li>';
            $k++;
        }
        $responseData .= '</ul>';
        $responseData .= '<script type="text/javascript">
            var $j = jQuery.noConflict();
            $j(document).ready(function () {
                $j(".prof_banner").owlCarousel({
                    loop:true,
                    //margin:10,
                    nav:true,
                    dots:true,
                    center:true,
                    stagePadding:434,
                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:1
                        },
                        1000:{
                            items:1
                        }
                    }
                });
            });
        </script>';
        $response['banner'] = $responseData;
        return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
    }
}
















