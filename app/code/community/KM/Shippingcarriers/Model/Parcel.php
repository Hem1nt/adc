<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @author      Kalpesh Mehta
 * @category    KM
 * @package     KM_Shippingcarriers
 * @copyright   Copyright (c) 2012 Kalpesh Mehta (http://ka.lpe.sh)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class KM_Shippingcarriers_Model_Parcel extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'parcel';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }

        $handling = Mage::getStoreConfig('carriers/' . $this->_code . '/handling_fee');
        $result = Mage::getModel('shipping/rate_result');
        $show = true;
        if ($show) { // This if condition is just to demonstrate how to return success and error in shipping methods
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setMethod($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethodTitle($this->getConfigData('name'));
            $method->setPrice($this->getConfigData('handling_fee'));
            $method->setCost($this->getConfigData('handling_fee'));
            $result->append($method);
        } else {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('name'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }
        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        //we only have one method so just return the name from the admin panel
        return array('parcel' => $this->getConfigData('title'));
    }

    public function isTrackingAvailable() {
        return true;
    }

    public function getTrackingInfo($tracking_number) {
        $tracking_result = $this->getTracking($tracking_number);

        if ($tracking_result instanceof Mage_Shipping_Model_Tracking_Result) {
            if ($trackings = $tracking_result->getAllTrackings()) {
                return $trackings[0];
            }
        } elseif (is_string($tracking_result) && !empty($tracking_result)) {
            return $tracking_result;
        }

        return false;
    }

    public function getTracking($tracking_number) {
        $tracking_result = Mage::getModel('shipping/tracking_result');

        $tracking_status = Mage::getModel('shipping/tracking_result_status');
        $tracking_status->setCarrier($this->_code);
        $tracking_status->setCarrierTitle($this->getConfigData('carrier_title'));
        $tracking_status->setTracking($tracking_number);
        //Getting xml of shippment bu curl
        $path = $this->getConfigData('gateway_url') . '?ShipmentNumber=' . $tracking_number;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $retValue = curl_exec($ch);
        curl_close($ch);

        try {
            $xmlobject = new SimpleXMLElement($retValue);
            $xmlarray = Mage::helper('shippingcarriers/data')->simpleXMLToArray($xmlobject);
            
            $status = "";
            if (isset($xmlarray['HAWBDetails'])) {
                $ignore = array('HAWBHistory', 'HAWBAttachments', 'ShipperReference2', 'SRNNo', 'ThirdPartyNumber', 'ThirdPartyReference', 'ThirdPartyReference2');
                foreach ($xmlarray['HAWBDetails'] as $k => $v) {
                    if (in_array($k, $ignore))
                        continue;
                    if (is_array($v)) {
                        foreach ($v as $kk => $vv) {
                            $status .= "$k -- \t\t" . $kk . ": " . $vv . "<br/>";
                        }
                    } else {
                        $status .= "<strong>" . $k . "</strong>: " . $v . "<br/>";
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $status = "Something went wrong while getting tracking information";
        }
        $tracking_status->addData(
                array(
                    'status' => $status
                )
        );
        $tracking_result->append($tracking_status);

        return $tracking_result;
    }

}
?>
