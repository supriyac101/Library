<?php
class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Product_Ribbon extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Form
{
    protected function _prepareForm()
    {    	
        $form = new Extendware_EWCore_Block_Varien_Data_Form();
		
        $fieldset = $form->addFieldset('main', array(
        	'legend' => $this->__('General Information'),
        ));
      	
        $fieldset->addField('product_status', 'select', array(
        	'name'      => 'product_status',
			'label'     => $this->__('Status'),
        	'values'	=> $this->getRibbon()->getProductStatusOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getProductStatus() ? $this->getRibbon()->getProductStatus() : 'enabled',
			'required'  => true,
        	'note'		=> $this->__(''),
        ));
        
        $specialVariables = implode(', ', $this->mHelper('adminhtml')->getTextSpecialVariables());
        $fieldset->addField('product_text', 'textarea', array(
        	'name'      => 'product_text',
        	'value'		=> $this->getRibbon()->getProductText(),
            'label'     => $this->__('Text'),
        	'style'		=> 'height: auto',
        	'note'		=> $this->__('This text will appear over the product image. Special variables are available'),
        	'ewhelp'	=> $this->__('New lines are converted to line breaks. You may enter special variables with <i>{{var [name]}}</i> where you replace <i>[name]</i> with the name of variable. For example, {{var price}} will output the price. You can format the output using <i>sprintf</i> formatting and using other value filters (look at the user guide). Here are two examples of using filters: <br/><br/><ul><li><b>{{var ew:discount_percent|sprintf:%s}}</b> - format the discount percent to only show leading digits</li><li><b>{{var ew:seconds_since_creation|divide:86400|round|max:1}}</b> - convert the seconds since creation to days since creation</li></ul> <br/><b>List of Variable Names</b><br/>%s<br/><br/><b>Note:</b> If a variable is not showing in the frontend then you need to make sure the product attribute is available to the loaded product. This usually means the attribute must be set to "Used in Product Listing" in Catalog -> Attributes.', '%d', $specialVariables),
        	'ewhelp_max_width' => '1500px',
        ))->setCols(3);
        
        $fieldset->addField('product_position', 'select', array(
        	'name'      => 'product_position',
			'label'     => $this->__('Position'),
        	'values'	=> $this->getRibbon()->getProductPositionOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getProductPosition() ? $this->getRibbon()->getProductPosition() : 'top_left',
			'required'  => true,
        	'note'		=> $this->__('The general location the ribbon will appear. You can customize further with styles if needed.'),
        ));

        $fieldset = $form->addFieldset('image', array(
        	'legend' => $this->__('Image Information'),
        ));
      	
        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
        $button->setOnClick('manageImages()');
        $button->setType('button');
        $button->setClass('add');
        $button->setLabel($this->__('Manage Images'));
        
        $fieldset->setHeaderBar($button->toHtml());
        
        $fieldset->addType('chooser_widget', 'Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Form_Element_Widget');
        
        $fieldset->addField('product_image_chooser', 'chooser_widget', array(
			'name' => 'product_image_chooser', 'layout' => $this->getLayout(), 'fieldset' => $fieldset,
			'label' => $this->__('Image'),
			'text' => $this->getRibbon()->hasProductImageId() ? $this->getRibbon()->getProductImage()->getPath() : null,
			'value' => $this->getRibbon()->getProductImageId(),
        	'can_delete' => true,
        	'javascript_callback' => 'productImageChooserCallback',
        	'bold'	=> true,
		));
		
		$fieldset->addType('image', 'Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Product_Element_Image');
        $fieldset->addField('product_image_preview', 'image', array(
			'name' => 'product_image_preview',
			'label' => $this->__('Preview'),
			'value' => $this->getRibbon()->hasProductImage() ? '<img src="' . Mage::helper('ewpribbon/internal_api')->getMediaUrl() . '/' . $this->getRibbon()->getProductImage()->getPath() . '">' : '[' . $this->__('No Image') . ']',
        ));
        
        $fieldset = $form->addFieldset('style', array(
        	'legend' => $this->__('Style Information'),
        ));
      	
        $fieldset->addField('product_text_style', 'text', array(
        	'name'      => 'product_text_style',
        	'value'		=> $this->getRibbon()->getProductTextStyle(),
            'label'     => $this->__('Text Style'),
        	'note'		=> $this->__('CSS style to modify ribbon text (color, size, positioning, etc)'),
        ));
        
        $fieldset->addField('product_image_style', 'text', array(
        	'name'      => 'product_image_style',
        	'value'		=> $this->getRibbon()->getProductImageStyle(),
            'label'     => $this->__('Image Style'),
        	'note'		=> $this->__('CSS style to modify ribbon image (width, height, border, etc)'),
        ));
        
        $fieldset->addField('product_container_style', 'text', array(
        	'name'      => 'product_container_style',
        	'value'		=> $this->getRibbon()->getProductContainerStyle() ? $this->getRibbon()->getProductContainerStyle() : 'z-index: 9999999',
            'label'     => $this->__('Container Style'),
        	'note'		=> $this->__('CSS style to modify outer container. Useful for positioning and z-index')
        ));
        
        $fieldset->addField('product_inner_container_style', 'text', array(
        	'name'      => 'product_inner_container_style',
        	'value'		=> $this->getRibbon()->getProductInnerContainerStyle(),
            'label'     => $this->__('Inner Container Style'),
        	'note'		=> $this->__('CSS style to modify inner container')
        ));
        
        $fieldset = $form->addFieldset('advanced', array(
        	'legend' => $this->__('Advanced Information'),
        ));
        
        $fieldset->addField('product_ref_selectors', 'textarea', array(
        	'name'      => 'product_ref_selectors',
        	'value'		=> $this->getRibbon()->getProductRefSelectors(),
            'label'     => $this->__('Reference Positino CSS Selectors'),
        	'note'		=> $this->__('Input one per line. The first matching one on the page will be used in order to help position the ribbons'),
        	'ewhelp'	=> $this->__('This is normally one of the main div containers on the page such as div.main or div.catalog-products, etc.'),
        ));
        
        $form->addValues($this->getAction()->getPersistentData('form_data_product_ribbon', true));
        $form->addFieldNameSuffix('product_ribbon');
		$form->setUseContainer(false);
        $this->setForm($form);
        
		return parent::_prepareForm();
	}
    
	public function getRibbon() {
        return Mage::registry('ew:current_ribbon');
    }
}
