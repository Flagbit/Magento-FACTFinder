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
 * Status Enabled Config Field Backend
 * 
 * @category  Mage
 * @package   Flagbit_FactFinder
 * @copyright Copyright (c) 2010 Flagbit GmbH & Co. KG (http://www.flagbit.de/)
 * @author    Joerg Weller <weller@flagbit.de>
 * @version   $Id$
 */
class Flagbit_FactFinder_Model_System_Config_Backend_Enabled extends Mage_Core_Model_Config_Data {
	
	/**
     * validate new data. it will print error message if not
     *
     * @return Flagbit_FactFinder_Model_System_Config_Backend_Enabled
     */
    protected function _beforeSave()
    {
		if($this->getValue()){
			
			$data = new Varien_Object($this->getFieldsetData());		
		    $errors  = array();
		    
	        if (stripos($data->getAddress(), 'http://') === 0 || strpos($data->getAddress(), '/') !== false) {
	            $errors[] = Mage::helper('factfinder')->__('servername should only contain the IP address or the domain - no "http://" or any slashes!');
	        }
	        
	        if ($data->getPort() == '') {
	            $port = 80;
	        } else if (!is_numeric($data->getPort())) {
	            $errors[] = Mage::helper('factfinder')->__('the value for "port" must be numeric!');
	        } else if(intval($data->getPort()) < 80) { //is there any http port lower 80?
	            $errors[] = Mage::helper('factfinder')->__('the value for "port" must be a number greater or equals 80!');
	        }

	        if ($data->getAuthPassword() != '' && $data->getAuthUser() == '') {
	            $errors[] = Mage::helper('factfinder')->__('there must be a username, if a password should be used');
	        }
	        
	        $conflicts = Mage::helper('factfinder/debug')->getRewriteConflicts();
	        if(count($conflicts)){
	        	foreach($conflicts as $moduleClass => $externalClass){
	        		$errors[] = Mage::helper('factfinder')->__('There is a Class Rewrite Conflict: "%s" already overwritten by "%s"', $moduleClass, $externalClass);
	        	}
	        }
	        
	        if (count($errors) == 0) {
	            $adapter = Mage::getSingleton('factfinder/adapter');
	            if(!$adapter->checkStatus($this->getFieldsetData())){
	                $errors[] = Mage::helper('factfinder')->__('WARNING: was not able to connect to FACT-Finder.');
	            }
	        }
	        
	        if (count($errors) > 0) {
	            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('factfinder')->__('FACT-Finder cannot be activated:').' <br/>'. implode('<br/>', $errors));
            	$this->setValue('0');
	        }else{
	        	Mage::app()->cleanCache(array(Flagbit_FactFinder_Model_Processor::CACHE_TAG));
	        }			
		}
				
        return $this;
    }
}
