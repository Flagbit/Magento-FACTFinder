<?php
class FACTFinder_Tracking_Block_Init extends Mage_Core_Block_Template
{


    /**
     * Get Product Result Collection
     *
     * @return FACTFinder_Core_Model_Resource_Search_Collection
     */
    protected function _getProductResultCollection()
    {
        return Mage::getSingleton('factfinder/catalogSearch_layer')->getProductCollection();
    }


    /**
     * Get Product URL to ID Mapping JSON Object
     *
     * @return string
     */
    public function getJsonUrlToIdMappingObject()
    {
        $data = array();
        foreach($this->_getProductResultCollection() as $product){
            $data[$product->getProductUrl()] = $product->getId();
        }
        return Mage::helper('core')->jsonEncode($data);
    }


    /**
     * Get Product and Search Details by ID as JSON Object
     *
     * @return string
     */
    public function getJsonDataObject()
    {
        $searchHelper = Mage::helper('factfinder/search');
        $idFieldName = $searchHelper->getIdFieldName();

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        if ($customerId) {
            $customerId = md5('customer_' . $customerId);
        }

        $dataTemplate = array(
            'query'         => $searchHelper->getQuery()->getQueryText(),
            'page'          => $searchHelper->getCurrentPage(),
            'sid'           => md5(Mage::getSingleton('core/session')->getSessionId()),
            'pageSize'      => $searchHelper->getPageLimit(),
            'origPageSize'  => $searchHelper->getDefaultPerPageValue(),
            'channel'       => Mage::getStoreConfig('factfinder/search/channel'),
            'userId'        => $customerId,
            'event'         => 'click'
        );

        $data = array();
        foreach($this->_getProductResultCollection() as $product){
            $key = $product->getId();

            $data[$key] = array(
                'id' => $product->getData($idFieldName),
                'masterid' => $product->getData($idFieldName),
                'pos'      => $product->getPosition(),
                'origPos'  => $product->getOriginalPosition() ? $product->getOriginalPosition() : $product->getPosition(),
                'title'    => $product->getName(),
                'simi'     => $product->getSimilarity()
            );

            $data[$key] += $dataTemplate;
        }

        return Mage::helper('core')->jsonEncode($data);
    }


}
