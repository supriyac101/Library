<?php

class Potato_PrintAnyOrder_Helper_Config extends Mage_Core_Helper_Abstract
{
    const GENERAL_TEXT_ALIGN      = 'po_pao/general/text_align';
    const GENERAL_REGULAR_FONT    = 'po_pao/general/regular_font';
    const GENERAL_BOLD_FONT       = 'po_pao/general/bold_font';
    const GENERAL_ITALIC_FONT     = 'po_pao/general/italic_font';

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return int
     */
    public function getTextAlign($store = null)
    {
        return (int)Mage::getStoreConfig(self::GENERAL_TEXT_ALIGN, $store);
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return null|string
     */
    public function getRegularFont($store = null)
    {
        return $this->_getFont(self::GENERAL_REGULAR_FONT, $store);
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return null|string
     */
    public function getBoldFont($store = null)
    {
        return $this->_getFont(self::GENERAL_BOLD_FONT, $store);
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return null|string
     */
    public function getItalicFont($store = null)
    {
        return $this->_getFont(self::GENERAL_ITALIC_FONT, $store);
    }

    /**
     * @return string
     */
    public function getFontFolderPath()
    {
        return Mage::getBaseDir('media') . DS . 'po_pao' . DS . 'fonts';
    }

    /**
     * @param string $configPath
     * @param Mage_Core_Model_Store|null $store
     *
     * @return null|string
     */
    protected function _getFont($configPath, $store = null)
    {
        $fontValue = Mage::getStoreConfig($configPath, $store);
        if (!$fontValue) {
            return null;
        }
        $fontPath = $this->getFontFolderPath() . DS . $fontValue;
        if (!file_exists($fontPath)) {
            return null;
        }
        return $fontPath;
    }
}