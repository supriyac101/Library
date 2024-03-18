<?php
class Anowave_Price_Block_Form_Global extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	* Prepare form before rendering HTML
	*
	* @return Mage_Adminhtml_Block_Widget_Form
	*/
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form
        (
            array
            (
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array
                (
                	'id' => $this->getRequest()->getParam('id'))
                ),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
        $form->setUseContainer(false);
        $this->setForm($form);

        $fieldset = $form->addFieldset
        (
            'base_fieldset', array
            (
                'legend' => $this->__('Global discount'),
            )
        );

        $fieldset->addField
        (
            'price_global_discount', 'text',array
            (
                'label' 				=> $this->__('Global Discount in %'),
                'name' 					=> 'price_global_discount',
                'after_element_html' 	=> '<p class="nm"><small>% between 0% - 100%. The discount entered will be applied for the entire product catalog.</small></p>'
            )
        );
        
        
        defined('ANOWAVE_DATIME_FORMAT') || define('ANOWAVE_DATIME_FORMAT','dd-MM-yyyy hh:mm');
        

        $fieldset->addField('price_global_valid_from', 'date', array
        (
            'label'     	=> $this->__('Valid from'),
            'name'      	=> 'price_global_valid_from',
            'format'    	=> ANOWAVE_DATIME_FORMAT,
            'input_format'	=> ANOWAVE_DATIME_FORMAT,
        	'time'      	=> true,
            'image'     	=> $this->getSkinUrl('images/grid-cal.gif')
        ));
        
        $fieldset->addField('price_global_valid_to', 'date', array
        (
            'label'     	=> $this->__('Valid until'),
            'name'      	=> 'price_global_valid_to',
            'format'    	=> ANOWAVE_DATIME_FORMAT,
            'input_format'	=> ANOWAVE_DATIME_FORMAT,
        	'time'      	=> true,
            'image'     	=> $this->getSkinUrl('images/grid-cal.gif')
        ));
        
        $form->setValues
        (
        	Mage::getModel('price/price_global')->loadCustomer
	        (
	        	$this->getRequest()->getParam('id')
	        )->getData()
        );

        return parent::_prepareForm();
    }
}