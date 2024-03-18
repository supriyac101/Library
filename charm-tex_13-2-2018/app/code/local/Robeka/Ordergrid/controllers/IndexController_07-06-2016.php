<?php
class Robeka_Ordergrid_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/ordergrid?id=15 
    	 *  or
    	 * http://site.com/ordergrid/id/15 	
    	 */
    	/* 
		$ordergrid_id = $this->getRequest()->getParam('id');

  		if($ordergrid_id != null && $ordergrid_id != '')	{
			$ordergrid = Mage::getModel('ordergrid/ordergrid')->load($ordergrid_id)->getData();
		} else {
			$ordergrid = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($ordergrid == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$ordergridTable = $resource->getTableName('ordergrid');
			
			$select = $read->select()
			   ->from($ordergridTable,array('ordergrid_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$ordergrid = $read->fetchRow($select);
		}
		Mage::register('ordergrid', $ordergrid);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function optionAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
		
		//echo "Hello";
		$_productId = Mage::app()->getRequest()->getParam('product');
		$_product = Mage::getModel('catalog/product')->load($_productId);
		//echo "<pre>";
		$_options = $_product->getOptions();
		//print_r($_options);
		
		// set array
		$x = array();
		$y = array();
		foreach($_options as $_option):
			$optionType = $_option->getType();
			$_title = $_option->getTitle();
			$_values = $_option->getValues();
			if($_option->getSortOrder() != 2){
				foreach($_values as $_value):
					$x[] = array($_value->getTitle(),$_value->getId());
				endforeach;
			}elseif($_option->getSortOrder() == 2){
				foreach($_values as $_value):
					$y[] = array($_value->getTitle(),$_value->getId());
				endforeach;
			}
			//echo count($_values);
		endforeach;
		/*echo "<pre>";
		print_r($x);
		exit;*/
		$table = "";
		// display table
		$table .= '<table class="matrix">';
		$counttoken = count($x);
		$k=0;
		foreach($x as $key=>$value){
			$table .= '<tr><td>'.$value[0].'</td>';
			foreach($y as $key1=>$value1):
				$k++;
				$_xId = $value[1];
				$_yId = $value1[1];
				$table .= '<td>' .$value1[0]. '<input type="text" name="items['.$_yId.']['.$_xId.']" /> </td>';
				if($k == $counttoken){ echo '</tr>';}
			endforeach;
		}
		$table .= '</table>';
		//Mage::register('option', $table);
		
		//$response['table'] = $table;
		echo $table; 
		
		//exit;
	}
	public function addtocartAction()
	{
		ini_set('display_errors', 1);
        Varien_Profiler::enable();
		Mage::setIsDeveloperMode(true);
		
		$_items = Mage::app()->getRequest()->getParam('ooptionsvalueId');
		$_options = Mage::app()->getRequest()->getParam('optionstype');
		
		$_items2 = Mage::app()->getRequest()->getParam('optionsvalueId2');
		$_options2 = Mage::app()->getRequest()->getParam('optionstypeId2');
		
		$_optionId = Mage::app()->getRequest()->getParam('optionId');
		$_optionvalId = Mage::app()->getRequest()->getParam('optionvalId');
		
		$_txtinsId = Mage::app()->getRequest()->getParam('txtinsId');
		$_txtinsvalId = Mage::app()->getRequest()->getParam('txtinsvalId');
		
		$qty = Mage::app()->getRequest()->getParam('qty');
		$proid = Mage::app()->getRequest()->getParam('proid');
		
		
		/*echo "<pre>";
		print_r($_POST);
		
		exit;*/
		//echo count($qty); exit;
		
		//$params = array();
		$pModel = Mage::getSingleton('catalog/product')->load($proid);
		$cart = Mage::getModel('checkout/cart');
		$cart->init();
		$params = array(
					'qty' => $qty,
					'options' => array($_options => $_items,
									$_options2 => $_items2,
									$_optionId => $_optionvalId,
									$_txtinsId => $_txtinsvalId
					)
				);
				
		//echo "<pre>";
		//print_r($params);
		
		
		$cart->addProduct($pModel, $params);
		$cart->save();
		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
		
		$response['status'] = 0;
		if (!Mage::getSingleton('checkout/session')->getNoCartRedirect(true)) {
			//echo "Yeppi";
			if (!$cart->getQuote()->getHasError()){
				//$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($_product->getName()));
				
				//$response['message'] = $message;
				$count = Mage::helper('checkout/cart')->getSummaryCount();
				$response['count'] = $count;
				//New Code Here
				//$this->loadLayout();
				//$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
				$this->loadLayout();
				$minicart = $this->getLayout()->getBlock('minicart_head')->toHtml();
				//$sidebar = Mage::app()->getLayout()->createBlock("checkout/cart_minicart")->setTemplate("checkout/cart/minicart.phtml")->toHtml(); 
				//$response['toplink'] = $toplink;
				$response['minicart'] = $minicart;
				//return $response;
				return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
			}
		}else{
			echo "Oats";
		}
	}
}