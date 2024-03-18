<?php

class Potato_PrintAnyOrder_Model_Source_TextAlign
{
    const LEFT_VALUE  = 1;
    const RIGHT_VALUE = 2;

    const LEFT_LABEL  = 'Left';
    const RIGHT_LABEL = 'Right';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::LEFT_VALUE,
                'label' => Mage::helper('po_pao')->__(self::LEFT_LABEL),
            ),
            array(
                'value' => self::RIGHT_VALUE,
                'label' => Mage::helper('po_pao')->__(self::RIGHT_LABEL),
            ),
        );
    }
}