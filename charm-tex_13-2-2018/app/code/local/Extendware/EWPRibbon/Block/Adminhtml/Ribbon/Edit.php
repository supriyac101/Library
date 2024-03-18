<?php

class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->addFormScript("
        	function manageImages(){
                window.open(" . json_encode($this->getUrl('*/ewpribbon_image/')) . ");
            }
        ");
        
        $this->addFormScript("
        	function categoryImageChooserCallback(params){
        		$('category_image_preview').update(" . json_encode('[' . $this->__('No Image') . ']') . ");
        		if (typeof params != 'undefined') {
        			var src = " . json_encode($this->mHelper('internal_api')->getMediaUrl()) . " + '/' + params.label;
        			$('category_image_preview').update('<img src=\"' + src + '\">');
        			alert(" . json_encode($this->__('You can use an "image style" to change the dimensions of the image')) . ");
        		}
            }
        ");
        
        $this->addFormScript("
        	function productImageChooserCallback(params){
        		$('product_image_preview').update(" . json_encode('[' . $this->__('No Image') . ']') . ");
        		if (typeof params != 'undefined') {
        			var src = " . json_encode($this->mHelper('internal_api')->getMediaUrl()) . " + '/' + params.label;
        			$('product_image_preview').update('<img src=\"' + src + '\">');
        			alert(" . json_encode($this->__('You can use an "image style" to change the dimensions of the image')) . ");
        		}
            }
        ");
        
        $this->addFormScript("
        	varienGrid.addMethods({
				rowMouseClick: function(event) {
					var element = Event.findElement(event, 'tr');
				    if(['img'].indexOf(Event.element(event).tagName.toLowerCase())!=-1) {
				        return;
				    }
		
				    if(this.rowClickCallback){
			            try{
			                this.rowClickCallback(this, event);
			            }
			            catch(e){}
			        }
			        varienGlobalEvents.fireEvent('gridRowClick', event);
				}
			});
		");
    }

    public function getHeaderText()
    {
        return $this->__('Product Ribbon');
    }
}
