<?php

class Potato_PrintAnyOrder_Model_Sales_Pdf_Page extends Zend_Pdf_Page
{
    protected $_arabicLib = null;
    protected $_sortedContent = array();

    /**
     * Draw a line of text at the specified position.
     *
     * @param string $text
     * @param float $x
     * @param float $y
     * @param string $charEncoding (optional) Character encoding of source text.
     *   Defaults to current locale.
     * @throws Zend_Pdf_Exception
     * @return Zend_Pdf_Canvas_Interface
     */
    public function drawText($text, $x, $y, $charEncoding = '')
    {
        $text = $this->_reverseHebrew($text);
        $text = $this->_reverseArabic($text);

        if (Mage::helper('po_pao/config')->getTextAlign() == Potato_PrintAnyOrder_Model_Source_TextAlign::LEFT_VALUE) {
            //if align left -> return default behavior
            return parent::drawText($text, $x, $y, $charEncoding);
        }
        //collect text
        if (!array_key_exists($y, $this->_sortedContent)) {
            $this->_sortedContent[$y] = array();
        }
        $this->_sortedContent[$y][$x] = array('text' => $text, 'charset' => $charEncoding);
        return $this;
    }

    public function flushDrawText()
    {
        foreach ($this->_sortedContent as $y => $xData) {
            //get row elements keys
            $rowElements = array_keys($xData);
            foreach ($xData as $data) {
                //default right position
                $feed = 565;
                if (count($this->_sortedContent[$y]) > 1) {
                    //if on x more than one element get next x
                    $nextX = (int)next($rowElements);
                    if ($nextX) {
                        //set margin between elements
                        $feed = $nextX - 15;
                    }
                }
                //calculate text width
                $textWidth = Mage::getModel('po_pao/sales_pdf_order')->widthForStringUsingFontSize($data['text'], $this->getFont(), $this->getFontSize());
                //calculate new x position for element
                $x = $feed - $textWidth;
                //draw text
                parent::drawText($data['text'], $x, $y, $data['charset']);
            }
        }
        $this->_sortedContent = array();
        return $this;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function _reverseHebrew($text)
    {
        preg_match_all(
            "/[\p{Hebrew}\s]+/ui", $text, $matches, PREG_OFFSET_CAPTURE
        );
        foreach ($matches[0] as $match) {
            $hebrewString = $match[0];
            if (!trim($hebrewString)) {
                continue;
            }
            //$this->_rightTextAlignFlag = true;
            $strlen = strlen($hebrewString);
            $left = $strlen - strlen(ltrim($hebrewString));
            $right = $strlen - strlen(rtrim($hebrewString));

            preg_match_all('/./us', trim($hebrewString), $ar);
            $newStr = str_repeat(' ', $left) . implode(array_reverse($ar[0])) . str_repeat(' ', $right);

            $text = substr_replace(
                $text, $newStr, $match[1], strlen($newStr)
            );
        }
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function _reverseArabic($text)
    {
        $arabicLib = $this->_getArabicLib();

        $textLen = mb_strlen($text);
        $prevCharIsArabic = false;
        $arabicString = '';
        $convertedString = '';
        for ($i = 0; $i < $textLen; $i++) {
            $char = mb_substr($text, $i, 1, 'utf-8');
            if (preg_match('/\p{Arabic}/u', $char) && !in_array(ord($char), array(194, 226)) && mb_strpos('٫,#&€', $char) === false || $char == ' ' && $prevCharIsArabic && $arabicString) {
                $arabicString .= $char;
                $prevCharIsArabic = true;
                //$this->_rightTextAlignFlag = true;
            } else {
                $prevCharIsArabic = false;
                if ($arabicString) {
                    if (mb_substr($arabicString, 0, 1, 'utf-8') == ' ') {
                        $convertedString .= ' ';
                    }
                    $convertedString .= $arabicLib->utf8Glyphs($arabicString);
                    if (mb_substr($arabicString, mb_strlen($arabicString) - 1, 1, 'utf-8') == ' ') {
                        $convertedString .= ' ';
                    }
                    $arabicString = '';
                }
                $convertedString .= $char;
            }
        }
        if ($arabicString) {
            $convertedString .= $arabicLib->utf8Glyphs($arabicString);
        }
        return $convertedString;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function _reverseString($string)
    {
        $textLen = mb_strlen($string);
        $utfString = '';
        for ($i = $textLen - 1; $i >= 0; $i--) {
            $utfString .= mb_substr($string, $i, 1, 'utf-8');
        }
        return $string;
    }

    /**
     * @return I18N_Arabic
     */
    protected function _getArabicLib()
    {
        if ($this->_arabicLib === null) {
            $this->_arabicLib = new I18N_Arabic('Glyphs');
        }
        return $this->_arabicLib;
    }
}