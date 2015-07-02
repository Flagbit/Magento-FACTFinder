<?php
/**
 * FACTFinder_Campaigns
 *
 * @category Mage
 * @package FACTFinder_Campaigns
 * @author Flagbit Magento Team <magento@flagbit.de>
 * @copyright Copyright (c) 2015, Flagbit GmbH & Co. KG
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link http://www.flagbit.de
 */

/**
 * Class FACTFinder_Campaigns_Block_Cart_Feedback
 *
 * Provides feedback text to the cart page
 *
 * @category Mage
 * @package FACTFinder_Campaigns
 * @author Flagbit Magento Team <magento@flagbit.de>
 * @copyright Copyright (c) 2015, Flagbit GmbH & Co. KG
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link http://www.flagbit.de
 */
class FACTFinder_Campaigns_Block_Cart_Feedback extends FACTFinder_Campaigns_Block_Abstract
{

    /**
     * @var string
     */
    protected $_template = 'factfinder/campaigns/cart/feedback.phtml';


    /**
     * Handler used to get data from ff
     *
     * @var FACTFinder_Campaigns_Model_Handler_Cart
     */
    protected $_handler;


    /**
     * Preparing global layout
     *
     * @return FACTFinder_Campaigns_Block_Cart_Feedback
     */
    protected function _prepareLayout()
    {
        if (Mage::helper('factfinder')->isEnabled('campaigns')) {
            $this->_handler = Mage::getSingleton('factfinder_campaigns/handler_cart');
        }

        return parent::_prepareLayout();
    }


    /**
     * Get campaign questions and answers
     *
     * @return array
     */
    public function getActiveFeedback()
    {
        $feedback = array();

        $_campaigns = $this->_handler->getCampaigns();

        if ($_campaigns && $_campaigns->hasFeedback()) {

            $label = $this->getLabel();

            $feedback = $_campaigns->getFeedback($label);
        }

        return $feedback;
    }


}