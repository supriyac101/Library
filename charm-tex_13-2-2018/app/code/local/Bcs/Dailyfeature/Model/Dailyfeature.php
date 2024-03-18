<?php

class Bcs_Dailyfeature_Model_Dailyfeature extends Mage_Core_Model_Abstract
{
	public $hourDiff = -4;
	public $currentDate = null;
    public function _construct(){
        parent::_construct();
        $this->_init('dailyfeature/dailyfeature');
		$this->currentDate = date("Y-m-d",mktime(date("H")+$this->hourDiff, date("i"), date("s")  , date("m"), date("d"), date("Y")));
		//$this->yesterday = date("Y-m-d",mktime(date("H")+$this->hourDiff, date("i"), date("s")  , date("m"), date("d")-1, date("Y")));
		$this->previousDay = date("Y-m-d",mktime(date("H")+$this->hourDiff, date("i"), date("s")  , date("m"), date("d")-3, date("Y")));
    }


	public function getTodaysDeal() {
	        
			$_now=Mage::getModel('core/date')->timestamp(time());
			$_currentDate=date('Y-m-d',$_now);
				       
			$collection = Mage::getModel('dailyfeature/dailyfeature')->getCollection();
			$collection->setOrder("deal_date_from","ASC");
			$_todays = null;
			$_previousDay = null;		
			foreach ($collection as $item) {			
			//echo $item->getdealdate().'='.$this->currentDate.'<br/>';
				if (strtotime($item->getData('deal_date_from')) <= strtotime($this->currentDate) && strtotime($item->getData('deal_date_to')) >= strtotime($this->currentDate)) {					
					$_todays = $item;
					break;
				}else if(strtotime($item->getData('deal_date_from')) <= strtotime($this->previousDay) && strtotime($item->getData('deal_date_to')) >= strtotime($this->previousDay)){
					$_previousDay = $item;
				}
			}
		
			if(empty($_todays))
		     $_todays=$_previousDay;
		
		if(empty($_todays) && empty($_previousDay)){
		  //Mage::throwException($this->__('No deal product found')); 
//		  echo $this->__('No deal product found');
		  exit;
		}
			
		return $_todays; 	

	}



    public function getTodaysDeal2() {
                try {
                        $i = 0;
#                        $collection = Mage::getModel('dailyfeature/dailyfeature')->getCollection();
#echo '2';
#exit();


				$todays= $i;



                        return $todays;
                } catch (Exception $e) {

		}
}


########################


	function test() {
		//$this->setProductPrice($this->getTodaysDeal());
		echo "got through";
	}
	private function setProductPrice($item) {
		
		//loading product
###			$product = Mage::getModel('catalog/product')->load($item->getProduct());
			
			// getting data
###			$oldPrice = $product->getSpecialPrice();
			//$oldFromDate = $product->getSpecialFromDate();
			//$oldToDate = $product->getSpecialToDate();
###			// storing data
###			$this->dbSetPrice($item->getProduct(),$oldPrice);
###			$item -> setOldSpecialPrice(serialize(array($oldPrice,$oldFromDate,$oldToDate)));
###			$item -> save();
			
			// setting new data
###			$this->dbSetPrice($item->getProduct(),$item->getSpecialprice());
			//$product->setSpecialPrice($item->getSpecialprice());
			//$product->setSpecialFromDate(date ("Y-m-d H:i:s", mktime(0, 0, 0 , date("j"), date("n"), date("Y"))));
			//$product->setSpecialToDate(date ("Y-m-d H:i:s", mktime(0, 0, 0 , date("d"), date("m")+1, date("Y"))));
			
###			// saving
###			$product->save();
		
	}
	private function setProductPriceBack($item) {
		
		//loading product
###		try {
			//$product = Mage::getModel('catalog/product')->load($item->getProduct());
			
			// getting old data
####			$data = unserialize($item -> getOldSpecialPrice());
			
			// setting data back
###			if ($data[0] > 0)
###				$this->dbSetPrice($item->getProduct(),$data[0]);
			//$product->setSpecialPrice($data[0]);
			//$product->setSpecialFromDate($data[1]);
			//$product->setSpecialToDate($data[2]);
			
			// saving
			//$product->save();
##		} catch (Exception $e) {
			
###		}
	}
	private function dbSetPrice($id,$price) {
######		mysql_query("UPDATE catalog_product_entity_decimal SET value='$price' WHERE attribute_id='60' AND entity_id='$id'");
	}
}
