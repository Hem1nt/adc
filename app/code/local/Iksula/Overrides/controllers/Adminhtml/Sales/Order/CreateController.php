<?php

require_once 'Mage/Adminhtml/controllers/Sales/Order/CreateController.php' ;

class Iksula_Overrides_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Sales_Order_CreateController
{
    public function saveAction(){
        try {
            /* Code Start For Blacklisting */    
            $paramdata = $this->getRequest()->getParams();        
            $return_address_val = $this->blackListAddress($paramdata["order"]);
            $return_phone_val = $this->blackListPhoneNumber($paramdata["order"]["billing_address"]["telephone"],$paramdata["order"]["shipping_address"]["telephone"]);
            $return_email_val = $this->blackListEmail($paramdata["order"]["account"]["email"]);
            
            if($return_email_val == 1 || $return_phone_val == 1 || $return_address_val ==1){
                $this->_redirect('*/sales_order/index');
                // print_r($return_phone_val);exit;
            }
            else{
                /* Code End For Blacklisting */
                $agent_name = $this->getRequest()->getPost("agent_name");
                $this->_processActionData('save');
                if ($paymentData = $this->getRequest()->getPost('payment')) {
                    $this->_getOrderCreateModel()->setPaymentData($paymentData);
                    $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
                }

                $order = $this->_getOrderCreateModel()
                    ->setIsValidate(true)
                    ->importPostData($this->getRequest()->getPost('order'))
                    ->createOrder();
                $orderagent = Mage::getModel('sales/order')->load($order->getId());
                $orderagent->setAgentName($agent_name)->save();
                $detail = array(
                        'order' => $order->getId(),
                    );
                Mage::dispatchEvent('adminhtml_add_reward_point', $detail);

                $this->_getSession()->clear();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been created.'));
                $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
            }
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        }
        catch (Exception $e){
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }

    public function donotcallAction(){
        $data = $this->getRequest()->getParams();
        $order = Mage::getModel("sales/order")->load($data["orderid"]);
        $order->setDonotcall($data["value"])->save();
    }
    protected function blackListPhoneNumber($bill_telephone,$ship_telephone){
        $billtelephone = str_replace(" ", "", $bill_telephone); 
        $shiptelephone = str_replace(" ", "", $ship_telephone); 
        foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_phonenumber")) as $mapping) {
            if(!empty($mapping['phonenumber'])){
                 $phone = str_replace(" ", "", $mapping['phonenumber']);
                 if (preg_match('/'.$phone.'/',$billtelephone) || preg_match('/'.$billtelephone.'/',$phone) || $billtelephone == $phone){
                    $this->_getSession()->clear();
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot Place Order as User Phone Number is Blacklisted.'));
                    return 1;
                }
                else if($shiptelephone != ""){
                    if (preg_match('/'.$phone.'/',$shiptelephone) || preg_match('/'.$shiptelephone.'/',$phone) || $shiptelephone == $phone){
                        $this->_getSession()->clear();
                        Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot Place Order as User Shipping Phone Number is Blacklisted.'));
                        return 1;
                    }
                }
            }
     
        }
        // exit;
    }

    protected function blackListAddress($order){
        $billorder = $order["billing_address"];
        $shiporder = $order["shipping_address"];
        foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_address")) as $mapping) {
            if(!empty($mapping['address1'])){
                if ((strtolower($billorder["street"][0]) == strtolower($mapping['address1'])) && (strtolower($billorder["street"][1]) == strtolower($mapping['address2'])) && (strtolower($billorder["country_id"]) == strtolower($mapping['country'])) && (strtolower($billorder["city"]) == strtolower($mapping['city'])) && (strtolower($billorder["postcode"]) == strtolower($mapping['zipcode']))) {
                    $this->_getSession()->clear();
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot Place Order as User Address is Blacklisted.'));
                    return 1;
                }
                else if((strtolower($shiporder["street"][0]) == strtolower($mapping['address1'])) && (strtolower($shiporder["street"][1]) == strtolower($mapping['address2'])) && (strtolower($shiporder["country_id"]) == strtolower($mapping['country'])) && (strtolower($shiporder["city"]) == strtolower($mapping['city'])) && (strtolower($shiporder["postcode"]) == strtolower($mapping['zipcode']))){
                    $this->_getSession()->clear();
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot Place Order as User Shipping Address is Blacklisted.'));
                    return 1;
                }
            }
            
        } 
    }

    protected function blackListEmail($email){
        foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_email")) as $mapping) {
            if($mapping['email'] == $email && !empty($mapping['email'])){
                $this->_getSession()->clear();
                Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot Place Order as User Email is Blacklisted.'));
                return 1;
            }
        }
    }
}
