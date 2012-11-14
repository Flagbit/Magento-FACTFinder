<?php
/**
 * Flagbit_FactFinder
 *
 * @category  Mage
 * @package   Flagbit_FactFinder
 * @copyright Copyright (c) 2010 Flagbit GmbH & Co. KG (http://www.flagbit.de/)
 */

/**
 * Overwritten block.
 *
 * Replaces the crosssell block. Gets data from FACT-Finder instead of product link collection.
 *
 * @category  Mage
 * @package   Flagbit_FactFinder
 * @copyright Copyright (c) 2010 Flagbit GmbH & Co. KG (http://www.flagbit.de/)
 * @author    Michael Türk <tuerk@flagbit.de>
 * @version   $Id: Processor.php 647 2011-03-21 10:32:14Z rudolf_batt $
 */
class Flagbit_FactFinder_Block_Product_List_Crosssell extends Mage_Catalog_Block_Product_List_Crosssell
{
    protected $_recommendationsHandler;

    protected function _prepareLayout()
    {
        $productIds = array(
            Mage::registry('product')->getData(Mage::helper('factfinder/search')->getIdFieldName())
        );
        $this->_recommendationsHandler = Mage::getSingleton('factfinder/handler_recommendations', $productIds);
        return parent::_prepareLayout();
    }
    /**
     * Method overwritten. Data is not read from product link collection but from FACT-Finder interface instead.
     */
    protected function _prepareData()
    {

        if (!Mage::getStoreConfigFlag('factfinder/activation/crosssell')) {
            return parent::_prepareData();
        }
        try {
            $product = Mage::registry('product');
            /* @var $product Mage_Catalog_Model_Product */

            $searchHelper = Mage::helper('factfinder/search');
            $idFieldName = $searchHelper->getIdFieldName();

            $this->_itemCollection = Mage::getResourceModel('factfinder/product_recommendation_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addStoreFilter();

            $recommendations = $this->_recommendationsHandler->getRecommendations();

            $this->_itemCollection->setRecommendations($recommendations);

            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

            $this->_itemCollection->load();

            foreach ($this->_itemCollection as $product) {
                $product->setDoNotUseCategoryId(true);
            }
        }
        catch (Exception $e) {
            Mage::logException($e);
        	$this->_itemCollection = new Varien_Data_Collection();
        }

        return $this;
    }
}