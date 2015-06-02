<?php

class FACTFinder_Core_Block_Adminhtml_Form_Field_Attribute extends Mage_Core_Block_Html_Select
{
    /**
     * Attributes cache
     *
     * @var array
     */
    protected $_attributes = array();


    /**
     * Retrieve attributes array
     *
     * @return array|string
     */
    protected function _getAttributes()
    {
        if (empty($this->_attributes)) {
            $collection = Mage::getModel('eav/entity_attribute')->getCollection();
            $collection->setEntityTypeFilter(Mage::getSingleton('eav/config')->getEntityType('catalog_product'));
            foreach ($collection as $item) {
                /* @var $item Mage_Core_Model_Store */
                $this->_attributes[$item->getAttributeCode()] = $item->getFrontendLabel().' ('.$item->getAttributeCode().')';
            }
        }

        return $this->_attributes;
    }


    /**
     * Set name of the html input
     *
     * @param string $value
     *
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }


    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getAttributes() as $id => $label) {
                $this->addOption($id, addslashes($label));
            }
        }

        return parent::_toHtml();
    }
}
