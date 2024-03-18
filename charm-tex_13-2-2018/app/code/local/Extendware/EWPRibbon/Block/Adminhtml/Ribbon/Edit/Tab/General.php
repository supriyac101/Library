<?php
class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_General extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Form
{
    protected function _prepareForm()
    {    	
        $form = new Extendware_EWCore_Block_Varien_Data_Form();
		
        $fieldset = $form->addFieldset('main', array(
        	'legend' => $this->__('General Information'),
        ));
      	
        $fieldset->addField('status', 'select', array(
        	'name'      => 'status',
			'label'     => $this->__('Status'),
        	'values'	=> $this->getRibbon()->getStatusOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getStatus() ? $this->getRibbon()->getStatus() : 'enabled',
			'required'  => true,
        	'note'		=> $this->__(''),
        ));
        
        $fieldset->addField('name', 'text', array(
        	'name'      => 'name',
        	'value'		=> $this->getRibbon()->getName(),
            'label'     => $this->__('Name'),
            'required'  => true,
        ));
        
        $fieldset->addField('hide_status', 'select', array(
        	'name'      => 'hide_status',
			'label'     => $this->__('Hide Status'),
        	'values'	=> $this->getRibbon()->getHideStatusOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getHideStatus() ? $this->getRibbon()->getHideStatus() : 'disabled',
			'required'  => true,
        	'note'		=> $this->__('Hide if ribbon already exists in this position'),
        	'ewhelp'	=> $this->__('If enabled, this ribbon will not be shown if another ribbon with a higher priority is shown in the same position. Position is defined on the ribbon page (e.g., top right, middle center, etc)'),
        ));
        
		$fieldset->addField('rule_processing_mode', 'select', array(
        	'name'      => 'rule_processing_mode',
			'label'     => $this->__('Processing Mode'),
        	'values'	=> $this->getRibbon()->getRuleProcessingModeOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getRuleProcessingMode() ? $this->getRibbon()->getRuleProcessingMode() : 'continue',
			'required'  => true,
        	'ewhelp'	=> $this->__('If set to "stop" no more ribbons will be added to a product after this ribbon is added. Set to "always continue" if there will be multiple ribbons shown on a single product.'),
        ));
        
        $fieldset->addField('priority', 'text', array(
        	'name'      => 'priority',
        	'value'		=> (int)$this->getRibbon()->getPriority(),
            'label'     => $this->__('Priority'),
            'required'  => true,
        	'note'		=> $this->__('A lower number is a higher priority'),
        	'ewhelp'	=> $this->__('Ribbons are processed according to priority. Ribbons with a higher priority will appear on top of other ribbons if they are in the same position on the product image.'),
        ));
        
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => $this->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
        	'value' => $this->getRibbon()->getFromDate(),
        	'note' => $this->__('Only set this if you only want the ribbon active within a certain date range. Current time is %s', now()),
        	'time' => true,
        ));
        
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => $this->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
        	'value' => $this->getRibbon()->getToDate(),
        	'note' => $this->__('Only set this if you only want the ribbon active within a certain date range. Current time is %s', now()),
        	'time' => true,
        ));
        
        $fieldset->addField('store_ids', 'multiselect', array(
			'label' => $this->__('Store View'), 
			'name' => 'store_ids', 
			'value' => $this->getRibbon()->getId() > 0 ? $this->getRibbon()->getStoreIds() : Mage::getResourceModel('core/store_collection')->getAllIds(),
			'values' => Mage::getModel('adminhtml/system_store')->getStoreValuesForForm(),
        	'note' => $this->__('Ribbon will only be shown in these stores. Use CTRL+Click to select more than store'),
		));
		
        $form->addValues($this->getAction()->getPersistentData('form_data_general', true));
        $form->addFieldNameSuffix('general');
		$form->setUseContainer(false);
        $this->setForm($form);
        
		return parent::_prepareForm();
	}
    
	public function getRibbon() {
        return Mage::registry('ew:current_ribbon');
    }
}
