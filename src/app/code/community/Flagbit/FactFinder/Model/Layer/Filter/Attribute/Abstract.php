<?php
/**
 * Flagbit_FactFinder
 *
 * @category  Mage
 * @package   Flagbit_FactFinder
 * @copyright Copyright (c) 2010 Flagbit GmbH & Co. KG (http://www.flagbit.de/)
 */

/**
 * Model class
 * 
 * This helper class provides the Price export
 * 
 * @category  Mage
 * @package   Flagbit_FactFinder
 * @copyright Copyright (c) 2010 Flagbit GmbH & Co. KG (http://www.flagbit.de/)
 * @author    Joerg Weller <weller@flagbit.de>
 * @version   $Id$
 */
class Flagbit_FactFinder_Model_Layer_Filter_Attribute_Abstract extends Mage_Catalog_Model_Layer_Filter_Attribute {
	
	/**
	 * Array of Magento Layer Filter Items
	 * @var mixed
	 */
	protected $_filterItems = null;
	
	/**
	 * Array of Selected Layer Filters
	 * @var mixed
	 */
	protected $_selectedFilterItems = array();
	
    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if(!Mage::helper('factfinder/search')->getIsEnabled(false, 'asn')){
            return parent::apply($request, $filterBlock);
        }
        $this->_getItemsData();
        $_attributeCode = $filterBlock->getAttributeModel()->getAttributeCode();
        if (isset($this->_selectedFilterItems[$_attributeCode])
            && is_array($this->_selectedFilterItems[$_attributeCode])) {

            foreach($this->_selectedFilterItems[$_attributeCode] as $option){
                $this->getLayer()->getState()->addFilter(
                    $this->_createItem(
                        $option['label'],
                        $option['value'],
                        $option['count'],
                        $option['selected'],
                        isset($option['seoPath']) ? $option['seoPath'] : '',
                        $option['requestVar'],
                        $option['queryParams']
                    )
                );
            }
        }
        return $this;
    }   
	
    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @param   bool $selected
     * @param   string $seoPath
     * @param   string $requestVar
     * @param   array $queryParams
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0, $selected=false, $seoPath='', $requestVar='', $queryParams=array())
    {
        if (!Mage::helper('factfinder/search')->getIsEnabled(false, 'asn')) {
            return parent::_createItem($label, $value, $count);
        }

        return Mage::getModel('factfinder/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setSelected($selected)
            ->setSeoPath($seoPath)
            ->setRequestVar($requestVar)
            ->setQueryParams($queryParams);
    }
    
    
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if(!Mage::helper('factfinder/search')->getIsEnabled(false, 'asn')){
            return parent::_getItemsData();
        }

        if($this->_filterItems === null){
            $attribute = $this->getAttributeModel();
            $this->_requestVar = 'filter'.$attribute->getAttributeCode();

            $options = $attribute->getItems();
            $this->_filterItems = array();
            if(is_array($options)){
                foreach ($options as $option)
                {
                    if($option['selected'] == true){
                        $this->_selectedFilterItems[$attribute->getAttributeCode()][] = $option;
                        continue;
                    }
                    $this->_filterItems[] = $option;
                }
            }
        }

        return $this->_filterItems;
    }

    /**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['selected'],
                isset($itemData['seoPath']) ? $itemData['seoPath'] : '',
                $itemData['requestVar'],
                $itemData['queryParams']
            );
        }
        $this->_items = $items;
        return $this;
    }
}