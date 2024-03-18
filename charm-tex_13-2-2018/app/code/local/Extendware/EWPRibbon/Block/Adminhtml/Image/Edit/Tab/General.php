<?php
class Extendware_EWPRibbon_Block_Adminhtml_Image_Edit_Tab_General extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Form
{
    protected function _prepareForm()
    {    	
        $form = new Extendware_EWCore_Block_Varien_Data_Form();
		
        $fieldset = $form->addFieldset('main', array(
        	'legend' => $this->__('General Information'),
        ));
      	
        if (!$this->getImage()->getId()) {
			$fieldset->addField('file', 'file', array(
	        	'name'      => 'file',
	            'label'     => $this->__('Image File'),
	            'required'  => true,
	        	'bold'		=> true,
				'note'		=> $this->__('Do not upload duplicate images'),
	        ));
        } else  if ($this->getImage()->getId() > 0) {
	        $fieldset->addField('namespace', 'label', array(
	        	'name'      => 'namespace',
	        	'value'		=> $this->getImage()->getNamespaceLabel(),
	            'label'     => $this->__('Namespace'),
	            'required'  => true,
	        	'bold'		=> true,
	        ));
	        
	        $fieldset->addField('path', 'label', array(
	        	'name'      => 'path',
	        	'value'		=> $this->getImage()->getPath(),
	            'label'     => $this->__('Path'),
	            'required'  => true,
	        	'bold'		=> true,
	        ));
	        
	        $fieldset->addType('image', 'Extendware_EWPRibbon_Block_Adminhtml_Image_Edit_Tab_General_Element_Image');
	        $fieldset->addField('image_preview', 'image', array(
				'name' => 'image_preview',
				'label' => $this->__('Preview'),
				'value' => $this->getImage()->getPath() ? '<img src="' . $this->mHelper('internal_api')->getMediaUrl() . '/' . $this->getImage()->getPath() . '">' : '[' . $this->__('No Image') . ']',
	        ));
        
	        $fieldset->addField('created_at', 'date_label', array(
	        	'name'      => 'created_at',
	        	'value'		=> $this->getImage()->getCreatedAt(),
	            'label'     => $this->__('Created'),
	            'required'  => true,
	        	'bold'		=> true,
	        ));
        }
        
        /*$fieldset->addField('type', 'select', array(
        	'name'      => 'type',
			'label'     => $this->__('Type'),
        	'values'	=> $this->getRibbon()->getTypeOptionModel()->toFormSelectOptionArray(true),
			'value'		=> $this->getRibbon()->getType() ? $this->getRibbon()->getType() : 'exclusive',
			'required'  => true,
        	'note'		=> $this->__(''),
        ));*/
        
        
		
        $form->addValues($this->getAction()->getPersistentData('form_data_general', true));
        $form->addFieldNameSuffix('general');
		$form->setUseContainer(false);
        $this->setForm($form);
        
		return parent::_prepareForm();
	}
    
	public function getImage() {
        return Mage::registry('ew:current_image');
    }
}
