<?php
class FACTFinder_Campaigns_Model_Observer
{
    public function handleCampaignsRedirect($observer)
    {
        if (!Mage::registry('current_layer') && !Mage::registry('current_product')) {
            return;
        }

        if (Mage::registry('current_product')) {
            $product = Mage::registry('current_product');
            $ids = array($product->getData(Mage::helper('factfinder/search')->getIdFieldName()));
            $handler = Mage::getModel('factfinder_campaigns/handler_product', $ids);
        } else {
            $handler = Mage::getSingleton('factfinder_campaigns/handler_search');
        }

        $redirect = $handler->getRedirect();

        if ($redirect) {
            // handle relative urls
            if (!Zend_Uri::check($redirect)) {
                $redirect = Mage::getBaseUrl() . $redirect;
            }

            $response = Mage::app()->getResponse();
            $response->setRedirect($redirect);
            $response->sendResponse();
            exit;
        }
    }

}