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
class Flagbit_FactFinder_Model_Layer_Filter_Attribute extends Mage_CatalogSearch_Model_Layer_Filter_Attribute {
	
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
        if(!Mage::helper('factfinder/search')->getIsEnabled()){
    		return parent::apply($request, $filterBlock);
    	}     	
    	
		$this->_getItemsData();
		$_attributeCode = $filterBlock->getAttributeModel()->getAttributeCode();
        if (isset($this->_selectedFilterItems[$_attributeCode])
        	&& is_array($this->_selectedFilterItems[$_attributeCode])) {
			
        	foreach($this->_selectedFilterItems[$_attributeCode] as $option){
            	$this->getLayer()->getState()->addFilter($this->_createItem($option['label'], $option['value']));
        	}
        }
        return $this;
    }

	
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if(!Mage::helper('factfinder/search')->getIsEnabled()){
    		return parent::_getItemsData();
    	}    	
    	
    	if($this->_filterItems === null){
	        $attribute = $this->getAttributeModel();
	        $this->_requestVar = $attribute->getAttributeCode();
	
	        $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
	        $data = $this->getLayer()->getAggregator()->getCacheData($key);
					
			$options = $attribute->getItems();
			$optionsCount = $attribute->getCount();
			$this->_filterItems = array();
			if(is_array($options)){
				foreach ($options as $option) {
					
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
	
}