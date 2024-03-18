<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.54
 * @build     780
 * @copyright Copyright (C) 2017 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Fpc_Helper_Action extends Mage_Core_Helper_Abstract
{
    /**
     * @param string $action
     * @param bull|int $storeId
     * @return string
     */
    public function getActionTag($action, $storeId = false) {
        $tag = str_replace('/', '_', $action);
        $tag = strtoupper($tag);        
        if ($storeId) {
            $tag .= '_' . $storeId; 
        }

        $tag = array(Mirasvit_Fpc_Model_Config::CACHE_PREFIX . $tag);

        return $tag;
    }
}
