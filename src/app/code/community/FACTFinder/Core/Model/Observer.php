<?php

class FACTFinder_Core_Model_Observer
{
    const SEARCH_ENGINE = 'factfinder/search_engine';
    const DEFAULT_SEARCH_ENGINE = 'catalogsearch/fulltext_engine';


    /**
     * Replace the config entry with search engine when enabling the module
     *
     * @param $observer
     */
    public function setSearchEngine($observer)
    {
        $request = $observer->getControllerAction()->getRequest();
        if ($request->getParam('section') != 'factfinder') {
            return;
        }

        $groups = $request->getPost('groups');

        if (empty($groups['search']['fields']['enabled']['value'])) {
            Mage::app()->getConfig()->saveConfig('catalog/search/engine', self::DEFAULT_SEARCH_ENGINE);
            return;
        }

        Mage::app()->getConfig()->saveConfig('catalog/search/engine', self::SEARCH_ENGINE);

        // this also helps with module managing
        Mage::app()->cleanCache();
        if (class_exists('Enterprise_PageCache_Model_Cache')) {
            Enterprise_PageCache_Model_Cache::getCacheInstance()
                ->clean(Enterprise_PageCache_Model_Processor::CACHE_TAG);
        }
    }


    /**
     * Reset current search layer for further use in the block
     *
     * @param $observer
     */
    public function resetCurrentSearchLayer($observer)
    {
        if (!Mage::helper('factfinder')->isEnabled()) {
            return;
        }

        Mage::register('current_layer', Mage::getSingleton('factfinder/catalogSearch_layer'));
    }


    /**
     * Remove all layered navigation filters on search page
     *
     * @param $observer
     */
    public function removeLayerFilters($observer)
    {
        if (!Mage::helper('factfinder')->isEnabled()) {
            return;
        }

        $block = $observer->getBlock();

        if (!$block instanceof Mage_CatalogSearch_Block_Layer) {
            return;
        }

        $block->unsetChildren();
        $block->setData('_filterable_attributes', array());
    }


    /**
     * Manage modules availability
     * Enable them only if they were enable in core configuration
     *
     * @param $observer
     */
    public function manageModules($observer)
    {
        $config = Mage::getConfig();
        $modules = $config->getNode('modules');

        $mustCleanCache = false;
        foreach ($modules->children() as $module => $data) {
            if (strpos($module, 'FACTFinder_') === 0 && $module !== 'FACTFinder_Core') {
                $configName = strtolower(str_replace('FACTFinder_', '', $module));
                $isActivated = Mage::helper('factfinder')->isEnabled($configName);

                if ($config->getNode("modules/{$module}/active") == $isActivated) {
                    continue;
                }

                Mage::helper('factfinder')->updateModuleState($module, $isActivated);
                $mustCleanCache = true;
            }
        }

        if ($mustCleanCache) {
            Mage::app()->cleanCache();
        }
    }

}