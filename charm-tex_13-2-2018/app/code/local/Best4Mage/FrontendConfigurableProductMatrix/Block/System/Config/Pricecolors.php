<?php

class Best4Mage_FrontendConfigurableProductMatrix_Block_System_Config_Pricecolors extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

	protected $_itemRenderer;

	public function __construct() {
		parent::__construct();
		$this->setTemplate('frontendconfigurableproductmatrix/system/config/form/field/array.phtml');
	}

	public function _prepareToRender() {
		$this->addColumn('qty', array(
			'label'	=>	Mage::helper('frontendconfigurableproductmatrix')->__('Qty'),
			'style'	=>	'width:50px',
		));
		$this->addColumn('condition', array(
			'label'	=>	Mage::helper('frontendconfigurableproductmatrix')->__('Condition'),
			'renderer'	=>	$this->_getRenderer(),
		));
		$this->addColumn('color', array(
			'label'	=>	Mage::helper('frontendconfigurableproductmatrix')->__('Color'),
			'style'	=>	'width:100px',
			'class'	=>	'color',
		));

		$this->_addAfter = false;
		$this->_addButtonLabel = Mage::helper('frontendconfigurableproductmatrix')->__('Add');
	}

	protected function _getRenderer() {
		if(!$this->_itemRenderer) {
			$this->_itemRenderer = $this->getLayout()->createBlock(
				'frontendconfigurableproductmatrix/system_config_source_condition',
				'',
				array('is_render_to_js_template' => true));
		}

		return $this->_itemRenderer;
	}

	protected function _prepareArrayRow(Varien_Object $row) {
		$row->setData(
			'option_extra_attr_' . $this->_getRenderer()->calcOptionHash($row->getData('condition')), 'selected="selected"'
		);
	}
}