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



class Mirasvit_Fpc_Model_Cache
{
    const CUSTOM_FPC_FOLDER = 'cache_fpc';

    protected static $_cache = null;
    public static $cacheDir = null;

    public static function getCacheInstance()
    {
        if (is_null(self::$_cache)) {
            $options = Mage::app()->getConfig()->getNode('global/fpc');
            if (!$options) {
                self::$_cache = Mage::app()->getCacheInstance();
                $backend = Mage::app()->getCacheInstance()->getFrontend()->getBackend();
                //if filesystem always keep cache in cache_fpc folder
                if ($backend instanceof Zend_Cache_Backend_File) {
                    $customFpcFolder = Mirasvit_Fpc_Model_Cache::CUSTOM_FPC_FOLDER;
                    self::$cacheDir = Mage::getBaseDir('var').DS.$customFpcFolder;
                    Mage::app()->getConfig()->getOptions()->createDirIfNotExists(self::$cacheDir);
                    $options = array('backend_options' => array(
                        'cache_dir' => self::$cacheDir,
                        'hashed_directory_level' => 2,
                    ));
                    self::$_cache = Mage::getModel('core/cache', $options);
                }
                return self::$_cache;
            }

            $options = $options->asArray();

            foreach (array('backend_options', 'slow_backend_options') as $tag) {
                if (!empty($options[$tag]['cache_dir'])) {
                    self::$cacheDir = Mage::getBaseDir('var').DS.$options[$tag]['cache_dir'];
                    $options[$tag]['cache_dir'] = self::$cacheDir;
                    Mage::app()->getConfig()->getOptions()->createDirIfNotExists($options[$tag]['cache_dir']);
                }
            }

            self::$_cache = Mage::getModel('core/cache', $options);
        }

        return self::$_cache;
    }

    public function getConfig()
    {
        return Mage::getSingleton('fpc/config');
    }

    public function cleanByLimits()
    {
        if ($this->getConfig()->isCleanOldCacheEnabled()) {
            Mage::helper('fpc/cache')->cleanOldCache();
        }
        if (Mage::helper('fpc')->getCacheSize() > Mage::getSingleton('fpc/config')->getMaxCacheSize()
            || Mage::helper('fpc')->getCacheNumber() > Mage::getSingleton('fpc/config')->getMaxCacheNumber()) {
                Mirasvit_Fpc_Model_Cache::getCacheInstance()->clean(Mirasvit_Fpc_Model_Processor::CACHE_TAG);
                /*@fpc cache clean*/
                if (Mage::getSingleton('fpc/config')->isDebugFlushCacheLogEnabled()) {
                    Mage::log('FPC flush cache by limits Max. Cache Size or Max. Number of Cache Files.', null, Mirasvit_Fpc_Model_Config::DEBUG_FLUSH_CACHE_LOG, true);
                }
        }

        return $this;
    }

    /* not used */
    public function clearAll()
    {
        try {
            $allTypes = Mage::app()->useCache();
            foreach ($allTypes as $type => $blah) {
                Mage::app()->getCacheInstance()->cleanType($type);
                /*@fpc cache clean*/
                if (Mage::getSingleton('fpc/config')->isDebugFlushCacheLogEnabled()) {
                    Mage::log('FPC flush all cache ( function clearAll ).', null, Mirasvit_Fpc_Model_Config::DEBUG_FLUSH_CACHE_LOG, true);
                }
            }
        } catch (Exception $e) {
        }
    }

    public function onCleanCache($observer)
    {
        try { //if we can't get Cache Tags Level
            $cacheTagslevelLevel = $this->getConfig()->getCacheTagslevelLevel();
            if ($cacheTagslevelLevel != Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_MINIMAL
                && $cacheTagslevelLevel != Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_EMPTY
                && $cacheTagslevelLevel != Mirasvit_Fpc_Model_Config::CACHE_TAGS_LEVEL_MINIMAL_PREFIX) {
                    self::getCacheInstance()->clean($observer->getTags());
                    /*@fpc cache clean*/
                    if (Mage::getSingleton('fpc/config')->isDebugFlushCacheLogEnabled()) {
                        Mage::log('FPC flush cache using event application_clean_cache (Mage/Core/Model/App.php).', null, Mirasvit_Fpc_Model_Config::DEBUG_FLUSH_CACHE_LOG, true);
                    }
            }
        } catch (Exception $e) { }

        return $this;
    }

    /**
     * @param array $fpcTags
     * @return void
     */
    public function clearCacheByTags($fpcTags)
    {
        self::getCacheInstance()->getFrontend()->clean('matchingAnyTag', $fpcTags);
        /*@fpc cache clean*/ //FPC create flush cache log in that place where FPC use this function

        return $this;
    }

    /**
     * @param string $cacheId
     * @return void
     */
    public function clearCacheById($cacheId)
    {
        $cache = self::getCacheInstance();
        $cache->remove($cacheId);
        /*@fpc cache clean*/  //FPC create flush cache log in that place where FPC use this function

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function onXtentoStockupdate($observer)
    {
        $tags = array();
        if ($modifiedStockItems = $observer->getEvent()->getModifiedStockItems()) {
            foreach ($modifiedStockItems as $stockItem) {
                $tags[] = Mirasvit_Fpc_Model_Config::CATALOG_PRODUCT_TAG . $stockItem;
            }
        }
        if ($tags) {
            $this->clearCacheByTags($tags);
            if (Mage::getSingleton('fpc/config')->isDebugFlushCacheLogEnabled()) {
                Mage::log('FPC flush cache using event xtento_stockimport_stockupdate_after', null, Mirasvit_Fpc_Model_Config::DEBUG_FLUSH_CACHE_LOG, true);
            }
        }
    }
}
