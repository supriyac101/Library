<?php

class Best4Mage_FrontendConfigurableProductMatrix_Block_System_Config_Source_Condition extends Mage_Core_Block_Html_Select {

	public function _toHtml() {
		$this->addOption('0', 'Select Condition');
		$this->addOption('1', '>=');
		$this->addOption('2', '<=');

		return parent::_toHtml();
	}

	public function setInputName($value) {
		return $this->setName($value);
	}
}