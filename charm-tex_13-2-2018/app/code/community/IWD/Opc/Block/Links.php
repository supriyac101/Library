<?php
class IWD_Opc_Block_Links extends Mage_Core_Block_Template{
    

    /**
     * Add link on checkout page to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCheckoutLink(){
    	
        

        $parentBlock = $this->getParentBlock();
        $text = $this->__('Checkout');
       if ($parentBlock = $this->getParentBlock()) {
         $text = $this->__('Checkout');
         $parentBlock->addLink($text, 'checkout', $text, true, array(), 60, null, 'class="top-link-checkout"');
     }
     return $this;
 }
}
