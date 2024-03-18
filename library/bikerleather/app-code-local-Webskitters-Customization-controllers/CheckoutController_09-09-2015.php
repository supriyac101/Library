<?php
class Webskitters_Customization_CheckoutController extends Mage_Core_Controller_Front_Action
{
    public function checkoutAction(){
	//echo "<pre>";
	//print_r($_POST);
	// Always set content-type when sending HTML email
	$headers='MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html;charset=iso-8859-1' . "\r\n";


	$to = Mage::getStoreConfig('trans_email/ident_general/email');
	$subject = "HTML email";   
	$_productoption1 = Mage::app()->getRequest()->getParam('entityid');
	$attributeValue = Mage::getModel('catalog/product')->load($_productoption1)->getEntityId();
	$attributeValue1 = Mage::getModel('catalog/product')->load($_productoption1)->getName();
	$attributeValue2 = Mage::getModel('catalog/product')->load($_productoption1)->getPrice();
	$message .= $attributeValue;
	$message .= $attributeValue1;
	$message .= $attributeValue2;
	$_productoption2 = Mage::app()->getRequest()->getParam('uploadedimg');
	$img_productoption2 = "<img src='".$_productoption2."' />";
	//$content = chunk_split(base64_encode(file_get_contents($_productoption2)));
	$_productoption3 = Mage::app()->getRequest()->getParam('designurl');
	$_productoption4 = Mage::app()->getRequest()->getParam('optionradio_1');
	$_productoption5 = Mage::app()->getRequest()->getParam('optionsLEATHER');
	$_productoption6 = Mage::app()->getRequest()->getParam('optionradio_2');
	$_productoption7 = Mage::app()->getRequest()->getParam('optionsHUMP');
	$_productoption8 = Mage::app()->getRequest()->getParam('optionradio_3');
	$_productoption9 = Mage::app()->getRequest()->getParam('optionsventilation');
	$_productoption10 = Mage::app()->getRequest()->getParam('optionradio_4');
	$_productoption11 = Mage::app()->getRequest()->getParam('optionslining');
	$_productoption12 = Mage::app()->getRequest()->getParam('optionradio_5');
	$_productoption13 = Mage::app()->getRequest()->getParam('optionsventilation');
	$_productoption14 = Mage::app()->getRequest()->getParam('optionradio_6');
	$_productoption15 = Mage::app()->getRequest()->getParam('optionsSliders_color');
	$_productoption16 = Mage::app()->getRequest()->getParam('optionradio_7');
	$_productoption17 = Mage::app()->getRequest()->getParam('optionsHard_CE_Armour_Inserts_or_Soft_padding');
	$_productoption18 = Mage::app()->getRequest()->getParam('optionradio_8');
	$_productoption19 = Mage::app()->getRequest()->getParam('optionsChoose_your_external_protections');
	$_productoption20 = Mage::app()->getRequest()->getParam('logopos1');
	$img_productoption20 = "<img src='".$_productoption20."' />";
	$_productoption21 = Mage::app()->getRequest()->getParam('logopos2');
	$img_productoption21 = "<img src='".$_productoption21."' />";
	$_productoption22 = Mage::app()->getRequest()->getParam('logopos3');
	$img_productoption22 = "<img src='".$_productoption22."' />";
	$_productoption23 = Mage::app()->getRequest()->getParam('logopos4');
	$img_productoption23 = "<img src='".$_productoption23."' />";
	$_productoption24 = Mage::app()->getRequest()->getParam('logopos5');
	$img_productoption24 = "<img src='".$_productoption24."' />";
	$_productoption25 = Mage::app()->getRequest()->getParam('logopos6');
	$img_productoption25 = "<img src='".$_productoption25."' />";
	$_productoption26 = Mage::app()->getRequest()->getParam('logopos7');
	$img_productoption26 = "<img src='".$_productoption26."' />";
	$_productoption27 = Mage::app()->getRequest()->getParam('logopos8');
	$img_productoption27 = "<img src='".$_productoption27."' />";
	$_productoption28 = Mage::app()->getRequest()->getParam('logopos9');
	$img_productoption28 = "<img src='".$_productoption28."' />";
	$_productoption29 = Mage::app()->getRequest()->getParam('logopos10');
	$img_productoption29 = "<img src='".$_productoption29."' />";
	$_productoption30 = Mage::app()->getRequest()->getParam('logopos11');
	$img_productoption30 = "<img src='".$_productoption30."' />";
	$_productoption31 = Mage::app()->getRequest()->getParam('logopos12');
	$img_productoption31 = "<img src='".$_productoption31."' />";
	$_productoption32 = Mage::app()->getRequest()->getParam('logopos13');
	$img_productoption32 = "<img src='".$_productoption32."' />";
	$_productoption33 = Mage::app()->getRequest()->getParam('logopos14');
	$img_productoption33 = "<img src='".$_productoption33."' />";
	$_productoption34 = Mage::app()->getRequest()->getParam('logopos15');
	$img_productoption34 = "<img src='".$_productoption34."' />";
	$_productoption35 = Mage::app()->getRequest()->getParam('logopos16');
	$img_productoption35 = "<img src='".$_productoption35."' />";
	$_productoption36 = Mage::app()->getRequest()->getParam('logopos17');
	$img_productoption36 = "<img src='".$_productoption36."' />";
	$_productoption37 = Mage::app()->getRequest()->getParam('wrist');
	$_productoption38 = Mage::app()->getRequest()->getParam('forearms');
	$_productoption39 = Mage::app()->getRequest()->getParam('biceps');
	$_productoption40 = Mage::app()->getRequest()->getParam('neck');
	$_productoption41 = Mage::app()->getRequest()->getParam('chest');
	$_productoption42 = Mage::app()->getRequest()->getParam('waist');
	$_productoption43 = Mage::app()->getRequest()->getParam('hipbottom');
	$_productoption44 = Mage::app()->getRequest()->getParam('name');
	$_productoption45 = Mage::app()->getRequest()->getParam('knee');
	$_productoption46 = Mage::app()->getRequest()->getParam('calf');
	$_productoption47 = Mage::app()->getRequest()->getParam('ankle');
	$_productoption48 = Mage::app()->getRequest()->getParam('shoulder');
	$_productoption49 = Mage::app()->getRequest()->getParam('necktowaistlength');
	$_productoption50 = Mage::app()->getRequest()->getParam('elbow');
	$_productoption51 = Mage::app()->getRequest()->getParam('insidecrotchtoanklelength');
	$_productoption52 = Mage::app()->getRequest()->getParam('kneecenttoanklebone');
	$_productoption53 = Mage::app()->getRequest()->getParam('outsidewaisttoanklebone');
	$_productoption54 = Mage::app()->getRequest()->getParam('shouldertowristlength');
	$_productoption55 = Mage::app()->getRequest()->getParam('name');
	$_productoption56 = Mage::app()->getRequest()->getParam('weight');
	$message ='
	<html>
	<head>
	  <title>Some title</title>
	</head>
	<body>
	  <table>
	 <tr><td>Product id</td><td>'.$attributeValue.'</td></tr>
	 <tr><td>Product Name</td><td>'.$attributeValue1.'</td></tr>
	 <tr><td>Product Price</td><td>'.$attributeValue2.'</td></tr>
	 <tr><td>Product Upload Image</td><td>'.$img_productoption2.'</td></tr>
	 <tr><td>Design Url</td><td>'.$_productoption3.'</td></tr>
	 <tr><td>'.$_productoption4.'</td><td>'.$_productoption5.'</td></tr>
	 <tr><td>'.$_productoption6.'</td><td>'.$_productoption7.'</td></tr>
	 <tr><td>'.$_productoption8.'</td><td>'.$_productoption9.'</td></tr>
	 <tr><td>'.$_productoption10.'</td><td>'.$_productoption11.'</td></tr>
	 <tr><td>'.$_productoption12.'</td><td>'.$_productoption13.'</td></tr>
	 <tr><td>'.$_productoption14.'</td><td>'.$_productoption15.'</td></tr>
	 <tr><td>'.$_productoption16.'</td><td>'.$_productoption17.'</td></tr>
	 <tr><td>'.$_productoption18.'</td><td>'.$_productoption19.'</td></tr>
	 <tr><td>Logo Position1</td><td>'.$img_productoption20.'</td></tr>
	 <tr><td>Logo Position2</td><td>'.$img_productoption21.'</td></tr>
	 <tr><td>Logo Position3</td><td>'.$img_productoption22.'</td></tr>
	 <tr><td>Logo Position4</td><td>'.$img_productoption23.'</td></tr>
	 <tr><td>Logo Position5</td><td>'.$img_productoption24.'</td></tr>
	 <tr><td>Logo Position6</td><td>'.$img_productoption25.'</td></tr>
	 <tr><td>Logo Position7</td><td>'.$img_productoption26.'</td></tr>
	 <tr><td>Logo Position8</td><td>'.$img_productoption27.'</td></tr>
	 <tr><td>Logo Position9</td><td>'.$img_productoption28.'</td></tr>
	 <tr><td>Logo Position10</td><td>'.$img_productoption29.'</td></tr>
	 <tr><td>Logo Position11</td><td>'.$img_productoption30.'</td></tr>
	 <tr><td>Logo Position12</td><td>'.$img_productoption31.'</td></tr>
	 <tr><td>Logo Position13</td><td>'.$img_productoption32.'</td></tr>
	 <tr><td>Logo Position14</td><td>'.$img_productoption33.'</td></tr>
	 <tr><td>Logo Position15</td><td>'.$img_productoption34.'</td></tr>
	 <tr><td>Logo Position16</td><td>'.$img_productoption35.'</td></tr>
	 <tr><td>Logo Position17</td><td>'.$img_productoption36.'</td></tr>
	 <tr><td>1 wrist circ.</td><td>'.$_productoption37.'</td></tr>
	 <tr><td>2 forearms circ.</td><td>'.$_productoption38.'</td></tr>
	 <tr><td>3 biceps circ.</td><td>'.$_productoption39.'</td></tr>
	 <tr><td>4 neck circ.</td><td>'.$_productoption40.'</td></tr>
	 <tr><td>5 chest circ.</td><td>'.$_productoption41.'</td></tr>
	 <tr><td>06 waist circ.</td><td>'.$_productoption42.'</td></tr>
	 <tr><td>07 hip-bottom circ</td><td>'.$_productoption43.'</td></tr>
	 <tr><td>08 thigh circ.</td><td>'.$_productoption44.'</td></tr>
	 <tr><td>09 knee circ.</td><td>'.$_productoption45.'</td></tr>
	 <tr><td>10 calf circ.</td><td>'.$_productoption46.'</td></tr>
	 <tr><td>11 ankle circ.</td><td>'.$_productoption47.'</td></tr>
	 <tr><td>12 shoulder width.</td><td>'.$_productoption48.'</td></tr>
	 <tr><td>13 neck to waist length.</td><td>'.$_productoption49.'</td></tr>
	 <tr><td>14 elbow to wrist</td><td>'.$_productoption50.'</td></tr>
	 <tr><td>15 inside crotch to ankle length</td><td>'.$_productoption51.'</td></tr>
	 <tr><td>16 kneecent to ankle bone</td><td>'.$_productoption52.'</td></tr>
         <tr><td>17 outside waist to ankle bone</td><td>'.$_productoption53.'</td></tr>
	 <tr><td>18 shoulder to wrist length</td><td>'.$_productoption54.'</td></tr>
	 <tr><td>19 height</td><td>'.$_productoption55.'</td></tr>
	 <tr><td>20 weight</td><td>'.$_productoption56.'</td></tr>
	  </table>
	</body>
	</html>';
		mail($to,$subject,$message,$headers);
	

	
    }
    public function reviewAction(){
	//echo "<pre>";
	//print_r($_POST);
	// Always set content-type when sending HTML email  
	$_productoption1 = Mage::app()->getRequest()->getParam('entityid');
	$attributeValue = Mage::getModel('catalog/product')->load($_productoption1)->getEntityId();
	$attributeValue1 = Mage::getModel('catalog/product')->load($_productoption1)->getName();
	$attributeValue2 = Mage::getModel('catalog/product')->load($_productoption1)->getPrice();
	$message .= $attributeValue;
	$message .= $attributeValue1;
	$message .= $attributeValue2;
	$_productoption2 = Mage::app()->getRequest()->getParam('uploadedimg');
	$img_productoption2 = "<img src='".$_productoption2."' />";
	//$content = chunk_split(base64_encode(file_get_contents($_productoption2)));
	$_productoption3 = Mage::app()->getRequest()->getParam('designurl');
	$_productoption4 = Mage::app()->getRequest()->getParam('optionradio_1');
	$_productoption5 = Mage::app()->getRequest()->getParam('optionsLEATHER');
	$_productoption6 = Mage::app()->getRequest()->getParam('optionradio_2');
	$_productoption7 = Mage::app()->getRequest()->getParam('optionsHUMP');
	$_productoption8 = Mage::app()->getRequest()->getParam('optionradio_3');
	$_productoption9 = Mage::app()->getRequest()->getParam('optionsventilation');
	$_productoption10 = Mage::app()->getRequest()->getParam('optionradio_4');
	$_productoption11 = Mage::app()->getRequest()->getParam('optionslining');
	$_productoption12 = Mage::app()->getRequest()->getParam('optionradio_5');
	$_productoption13 = Mage::app()->getRequest()->getParam('optionsventilation');
	$_productoption14 = Mage::app()->getRequest()->getParam('optionradio_6');
	$_productoption15 = Mage::app()->getRequest()->getParam('optionsSliders_color');
	$_productoption16 = Mage::app()->getRequest()->getParam('optionradio_7');
	$_productoption17 = Mage::app()->getRequest()->getParam('optionsHard_CE_Armour_Inserts_or_Soft_padding');
	$_productoption18 = Mage::app()->getRequest()->getParam('optionradio_8');
	$_productoption19 = Mage::app()->getRequest()->getParam('optionsChoose_your_external_protections');
	$_productoption20 = Mage::app()->getRequest()->getParam('logopos1');
	$img_productoption20 = "<img src='".$_productoption20."' />";
	$_productoption21 = Mage::app()->getRequest()->getParam('logopos2');
	$img_productoption21 = "<img src='".$_productoption21."' />";
	$_productoption22 = Mage::app()->getRequest()->getParam('logopos3');
	$img_productoption22 = "<img src='".$_productoption22."' />";
	$_productoption23 = Mage::app()->getRequest()->getParam('logopos4');
	$img_productoption23 = "<img src='".$_productoption23."' />";
	$_productoption24 = Mage::app()->getRequest()->getParam('logopos5');
	$img_productoption24 = "<img src='".$_productoption24."' />";
	$_productoption25 = Mage::app()->getRequest()->getParam('logopos6');
	$img_productoption25 = "<img src='".$_productoption25."' />";
	$_productoption26 = Mage::app()->getRequest()->getParam('logopos7');
	$img_productoption26 = "<img src='".$_productoption26."' />";
	$_productoption27 = Mage::app()->getRequest()->getParam('logopos8');
	$img_productoption27 = "<img src='".$_productoption27."' />";
	$_productoption28 = Mage::app()->getRequest()->getParam('logopos9');
	$img_productoption28 = "<img src='".$_productoption28."' />";
	$_productoption29 = Mage::app()->getRequest()->getParam('logopos10');
	$img_productoption29 = "<img src='".$_productoption29."' />";
	$_productoption30 = Mage::app()->getRequest()->getParam('logopos11');
	$img_productoption30 = "<img src='".$_productoption30."' />";
	$_productoption31 = Mage::app()->getRequest()->getParam('logopos12');
	$img_productoption31 = "<img src='".$_productoption31."' />";
	$_productoption32 = Mage::app()->getRequest()->getParam('logopos13');
	$img_productoption32 = "<img src='".$_productoption32."' />";
	$_productoption33 = Mage::app()->getRequest()->getParam('logopos14');
	$img_productoption33 = "<img src='".$_productoption33."' />";
	$_productoption34 = Mage::app()->getRequest()->getParam('logopos15');
	$img_productoption34 = "<img src='".$_productoption34."' />";
	$_productoption35 = Mage::app()->getRequest()->getParam('logopos16');
	$img_productoption35 = "<img src='".$_productoption35."' />";
	$_productoption36 = Mage::app()->getRequest()->getParam('logopos17');
	$img_productoption36 = "<img src='".$_productoption36."' />";
	$_productoption37 = Mage::app()->getRequest()->getParam('wrist');
	$_productoption38 = Mage::app()->getRequest()->getParam('forearms');
	$_productoption39 = Mage::app()->getRequest()->getParam('biceps');
	$_productoption40 = Mage::app()->getRequest()->getParam('neck');
	$_productoption41 = Mage::app()->getRequest()->getParam('chest');
	$_productoption42 = Mage::app()->getRequest()->getParam('waist');
	$_productoption43 = Mage::app()->getRequest()->getParam('hipbottom');
	$_productoption44 = Mage::app()->getRequest()->getParam('name');
	$_productoption45 = Mage::app()->getRequest()->getParam('knee');
	$_productoption46 = Mage::app()->getRequest()->getParam('calf');
	$_productoption47 = Mage::app()->getRequest()->getParam('ankle');
	$_productoption48 = Mage::app()->getRequest()->getParam('shoulder');
	$_productoption49 = Mage::app()->getRequest()->getParam('necktowaistlength');
	$_productoption50 = Mage::app()->getRequest()->getParam('elbow');
	$_productoption51 = Mage::app()->getRequest()->getParam('insidecrotchtoanklelength');
	$_productoption52 = Mage::app()->getRequest()->getParam('kneecenttoanklebone');
	$_productoption53 = Mage::app()->getRequest()->getParam('outsidewaisttoanklebone');
	$_productoption54 = Mage::app()->getRequest()->getParam('shouldertowristlength');
	$_productoption55 = Mage::app()->getRequest()->getParam('name');
	$_productoption56 = Mage::app()->getRequest()->getParam('weight');
	$message ='
	<html>
	<head>
	  <title>Some title</title>
	</head>
	<body>
	  <table>
	 <tr><td>Product id</td><td>'.$attributeValue.'</td></tr>
	 <tr><td>Product Name</td><td>'.$attributeValue1.'</td></tr>
	 <tr><td>Product Price</td><td>'.$attributeValue2.'</td></tr>
	 <tr><td>Product Upload Image</td><td>'.$img_productoption2.'</td></tr>
	 <tr><td>Design Url</td><td>'.$_productoption3.'</td></tr>
	 <tr><td>'.$_productoption4.'</td><td>'.$_productoption5.'</td></tr>
	 <tr><td>'.$_productoption6.'</td><td>'.$_productoption7.'</td></tr>
	 <tr><td>'.$_productoption8.'</td><td>'.$_productoption9.'</td></tr>
	 <tr><td>'.$_productoption10.'</td><td>'.$_productoption11.'</td></tr>
	 <tr><td>'.$_productoption12.'</td><td>'.$_productoption13.'</td></tr>
	 <tr><td>'.$_productoption14.'</td><td>'.$_productoption15.'</td></tr>
	 <tr><td>'.$_productoption16.'</td><td>'.$_productoption17.'</td></tr>
	 <tr><td>'.$_productoption18.'</td><td>'.$_productoption19.'</td></tr>
	 <tr><td>Logo Position1</td><td>'.$img_productoption20.'</td></tr>
	 <tr><td>Logo Position2</td><td>'.$img_productoption21.'</td></tr>
	 <tr><td>Logo Position3</td><td>'.$img_productoption22.'</td></tr>
	 <tr><td>Logo Position4</td><td>'.$img_productoption23.'</td></tr>
	 <tr><td>Logo Position5</td><td>'.$img_productoption24.'</td></tr>
	 <tr><td>Logo Position6</td><td>'.$img_productoption25.'</td></tr>
	 <tr><td>Logo Position7</td><td>'.$img_productoption26.'</td></tr>
	 <tr><td>Logo Position8</td><td>'.$img_productoption27.'</td></tr>
	 <tr><td>Logo Position9</td><td>'.$img_productoption28.'</td></tr>
	 <tr><td>Logo Position10</td><td>'.$img_productoption29.'</td></tr>
	 <tr><td>Logo Position11</td><td>'.$img_productoption30.'</td></tr>
	 <tr><td>Logo Position12</td><td>'.$img_productoption31.'</td></tr>
	 <tr><td>Logo Position13</td><td>'.$img_productoption32.'</td></tr>
	 <tr><td>Logo Position14</td><td>'.$img_productoption33.'</td></tr>
	 <tr><td>Logo Position15</td><td>'.$img_productoption34.'</td></tr>
	 <tr><td>Logo Position16</td><td>'.$img_productoption35.'</td></tr>
	 <tr><td>Logo Position17</td><td>'.$img_productoption36.'</td></tr>
	 <tr><td>1 wrist circ.</td><td>'.$_productoption37.'</td></tr>
	 <tr><td>2 forearms circ.</td><td>'.$_productoption38.'</td></tr>
	 <tr><td>3 biceps circ.</td><td>'.$_productoption39.'</td></tr>
	 <tr><td>4 neck circ.</td><td>'.$_productoption40.'</td></tr>
	 <tr><td>5 chest circ.</td><td>'.$_productoption41.'</td></tr>
	 <tr><td>06 waist circ.</td><td>'.$_productoption42.'</td></tr>
	 <tr><td>07 hip-bottom circ</td><td>'.$_productoption43.'</td></tr>
	 <tr><td>08 thigh circ.</td><td>'.$_productoption44.'</td></tr>
	 <tr><td>09 knee circ.</td><td>'.$_productoption45.'</td></tr>
	 <tr><td>10 calf circ.</td><td>'.$_productoption46.'</td></tr>
	 <tr><td>11 ankle circ.</td><td>'.$_productoption47.'</td></tr>
	 <tr><td>12 shoulder width.</td><td>'.$_productoption48.'</td></tr>
	 <tr><td>13 neck to waist length.</td><td>'.$_productoption49.'</td></tr>
	 <tr><td>14 elbow to wrist</td><td>'.$_productoption50.'</td></tr>
	 <tr><td>15 inside crotch to ankle length</td><td>'.$_productoption51.'</td></tr>
	 <tr><td>16 kneecent to ankle bone</td><td>'.$_productoption52.'</td></tr>
         <tr><td>17 outside waist to ankle bone</td><td>'.$_productoption53.'</td></tr>
	 <tr><td>18 shoulder to wrist length</td><td>'.$_productoption54.'</td></tr>
	 <tr><td>19 height</td><td>'.$_productoption55.'</td></tr>
	 <tr><td>20 weight</td><td>'.$_productoption56.'</td></tr>
	  </table>
	</body>
	</html>';
	$continue['content'] = $message;
	return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($continue));
    }
    
    public function addtocartAction(){
		ini_set('display_errors', 0);
        Varien_Profiler::enable();
		Mage::setIsDeveloperMode(true);
		echo "<pre>";
		//print_r($_POST);
		$uploadedimg = Mage::app()->getRequest()->getParam('uploadedimg');
		$_options = Mage::app()->getRequest()->getParam('options');
		//print_r($_options);
		/*foreach($_options as $_key => $_val):
			$params = array($_key => $_val);
		endforeach;
		print_r($params);*/
		$product_id = Mage::app()->getRequest()->getParam('entityid');
		$product_qty= 1;
		$_product = Mage::getSingleton('catalog/product')->load($product_id);
		//$additional_options = $_product->getProductOptions();
		//print_r($additional_options);
		$cart = Mage::getModel('checkout/cart');
		$cart->init();
		$cart->addProduct($_product,
			array('qty' => 1,
				'price' => 100,
				'options' => $_options
			)
		);
		//$cart->setCustomPrice($rush);
		//var_dump($cart);
		//exit;
		$cart->save();
		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	}
}