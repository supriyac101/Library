<?php
class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Category_Ribbon extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Form
{
    protected function _prepareForm()
    {    	
        $form = new Extendware_EWCore_Block_Varien_Data_Form();
		
        $fieldset = $form->addFieldset('main', array(
        	'legend' => $this->__('General Information'),
        ));
      	
        $fieldset->addField('category_status', 'select', array(
        	'name'      => 'category_status',
			'label'     => $this->__('Status'),
        	'values'	=> $this->getRibbon()->getCategoryStatusOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getCategoryStatus() ? $this->getRibbon()->getCategoryStatus() : 'enabled',
			'required'  => true,
        	'note'		=> $this->__(''),
        ));
        
        $specialVariables = implode(', ', $this->mHelper('adminhtml')->getTextSpecialVariables());
        $fieldset->addField('category_text', 'textarea', array(
        	'name'      => 'category_text',
        	'value'		=> $this->getRibbon()->getCategoryText(),
            'label'     => $this->__('Text'),
        	'style'		=> 'height: auto',
        	'note'		=> $this->__('This text will appear over the product image. Special variables are available'),
        	'ewhelp'	=> $this->__('New lines are converted to line breaks. You may enter special variables with <i>{{var [name]}}</i> where you replace <i>[name]</i> with the name of variable. For example, {{var price}} will output the price. You can format the output using <i>sprintf</i> formatting and using other value filters (look at the user guide). Here are two examples of using filters: <br/><br/><ul><li><b>{{var ew:discount_percent|sprintf:%s}}</b> - format the discount percent to only show leading digits</li><li><b>{{var ew:seconds_since_creation|divide:86400|round|max:1}}</b> - convert the seconds since creation to days since creation</li></ul> <br/><b>List of Variable Names</b><br/>%s<br/><br/><b>Note:</b> If a variable is not showing in the frontend then you need to make sure the product attribute is available to the loaded product. This usually means the attribute must be set to "Used in Product Listing" in Catalog -> Attributes.', '%d', $specialVariables),
        	'ewhelp_max_width' => '1500px',
        ))->setCols(3);
        
        $fieldset->addField('category_position', 'select', array(
        	'name'      => 'category_position',
			'label'     => $this->__('Position'),
        	'values'	=> $this->getRibbon()->getCategoryPositionOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getCategoryPosition() ? $this->getRibbon()->getCategoryPosition() : 'top_left',
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
        $fieldset->addField('category_image_chooser', 'chooser_widget', array(
			'name' => 'category_image_chooser', 'layout' => $this->getLayout(), 'fieldset' => $fieldset,
			'label' => $this->__('Image'),
			'text' => $this->getRibbon()->hasCategoryImageId() ? $this->getRibbon()->getCategoryImage()->getPath() : null,
			'value' => $this->getRibbon()->getCategoryImageId(),
        	'can_delete' => true,
        	'javascript_callback' => 'categoryImageChooserCallback',
        	'bold'	=> true,
		));
		
		$fieldset->addType('image', 'Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Category_Element_Image');
        $fieldset->addField('category_image_preview', 'image', array(
			'name' => 'category_image_preview',
			'label' => $this->__('Preview'),
			'value' => $this->getRibbon()->hasCategoryImage() ? '<img src="' . $this->getRibbon()->getCategoryImage()->getUrl() . '">' : '[' . $this->__('No Image') . ']',
        ));
		
        $fieldset = $form->addFieldset('style', array(
        	'legend' => $this->__('Style Information'),
        ));
      	
        $fieldset->addField('category_text_style', 'text', array(
        	'name'      => 'category_text_style',
        	'value'		=> $this->getRibbon()->getCategoryTextStyle(),
            'label'     => $this->__('Text Style'),
        	'note'		=> $this->__('CSS style to modify ribbon text (color, size, positioning, etc)'),
        ));
        
         $fieldset->addField('category_image_style', 'text', array(
        	'name'      => 'category_image_style',
        	'value'		=> $this->getRibbon()->getCategoryImageStyle(),
            'label'     => $this->__('Image Style'),
         	'note'		=> $this->__('CSS style to modify ribbon image (width, height, border, etc)'),
        ));
        
        $fieldset->addField('category_container_style', 'text', array(
        	'name'      => 'category_container_style',
        	'value'		=> $this->getRibbon()->getCategoryContainerStyle() ? $this->getRibbon()->getCategoryContainerStyle() : 'z-index: 9999999',
            'label'     => $this->__('Container Style'),
        	'note'		=> $this->__('CSS style to modify outer container. Useful for positioning and z-index')
        ));
        
        $fieldset->addField('category_inner_container_style', 'text', array(
        	'name'      => 'category_inner_container_style',
        	'value'		=> $this->getRibbon()->getCategoryInnerContainerStyle(),
            'label'     => $this->__('Inner Container Style'),
        	'note'		=> $this->__('CSS style to modify inner container')
        ));
        
        $fieldset = $form->addFieldset('advanced', array(
        	'legend' => $this->__('Advanced Information'),
        ));
        
        $fieldset->addField('category_ref_selectors', 'textarea', array(
        	'name'      => 'category_ref_selectors',
        	'value'		=> $this->getRibbon()->getCategoryRefSelectors(),
            'label'     => $this->__('Reference Position CSS Selectors'),
        	'note'		=> $this->__('Input one per line. The first matching one on the page will be used in order to help position the ribbons'),
        	'ewhelp'	=> $this->__('This is normally one of the main div containers on the page such as div[class~="main"] or div[class~="category-products"], etc. It will be an element where the css position is set to "relative".'),
        ));
        
        $form->addValues($this->getAction()->getPersistentData('form_data_category_ribbon', true));
        $form->addFieldNameSuffix('category_ribbon');
		$form->setUseContainer(false);
        $this->setForm($form);
        
		return parent::_prepareForm();
	}
    
	public function getRibbon() {
        return Mage::registry('ew:current_ribbon');
    }
}
