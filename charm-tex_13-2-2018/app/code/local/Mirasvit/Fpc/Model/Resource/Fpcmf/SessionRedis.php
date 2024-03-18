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



class Mirasvit_Fpc_Model_Resource_Fpcmf_SessionRedis extends Cm_RedisSession_Model_Session
{
    public function getLifeTime()
    {
        $stores = Mage::app()->getStores();
        if (empty($stores)) {
            return 604800;
        }

        return parent::getLifeTime();
    }
}
