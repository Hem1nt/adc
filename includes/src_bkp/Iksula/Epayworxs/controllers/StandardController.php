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
 * @category   Mage
 * @package    Iksula_Epayworxs
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * epayworxs Standard Front Controller
 *
 * @category   Mage
 * @package    Iksula_Epayworxs
 * @name       Iksula_Epayworxs_StandardController
 * @author     Magento Core Team <core@magentocommerce.com>
*/

require('Rc43.php');

class Iksula_Epayworxs_StandardController extends Mage_Core_Controller_Front_Action
{
	
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return Mage::getSingleton('epayworxs/config')->getDebug();
    }

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses epayworxs on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
		
		
        $session = Mage::getSingleton('checkout/session');
        $session->setepayworxsStandardQuoteId($session->getQuoteId());

        $order = $this->getOrder();
		
        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('epayworxs')->__('Customer was redirected to epayworxs')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('epayworxs/standard_redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }

    /**
     *  Success response from Secure Ebs
     *
     *  @return	  void
     */
    public function  successAction()
    {
	
	
	
	$response = $this->getRequest()->getPost();
	//echo '<pre>';
	//print_r($response);exit;
     if(isset($_GET['DR'])) {
		 $DR = preg_replace("/\s/","+",$_GET['DR']);	 	 
	 
	     $secret_key = Mage::getSingleton('epayworxs/config')->getSecretKey(); // Your Secret Key

	 	 $rc4 = new Crypt_RC4($secret_key);
 	     $QueryString = base64_decode($DR);
	     $rc4->decrypt($QueryString);
	     $QueryString = split('&',$QueryString);

	 $response = array();
	 
 
	 foreach($QueryString as $param){
	 	$param = split('=',$param);
		$response[$param[0]] = urldecode($param[1]);
	 }
	 
	}
       if($response['ResponseCode']== 0)
       {
			$session = Mage::getSingleton('checkout/session');
			$session->setQuoteId($session->getepayworxsStandardQuoteId());
			$session->unsepayworxsStandardQuoteId();
			
			//add code
				$id=Mage::getSingleton('checkout/session')->getLastRealOrderId();
				$order = Mage::getModel('sales/order')->loadByIncrementId($id);
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
			//end code
		
			
			//echo $order->getStatus();exit;
			if (!$order->getId()) {
				$this->norouteAction();
				return;
			}

			$order->addStatusToHistory(
				$order->getStatus(),
				Mage::helper('epayworxs')->__('Customer successfully returned from epayworxs')
			);
			
			$order->save();
			$order->sendNewOrderEmail();
			
			
			$this->_redirect('checkout/onepage/success');
       }
       else
       {
       	$this->_redirect('checkout/onepage/failure');
       }
    }
     
    /**
     *  Notification Action from Secure Ebs
     *
     *  @param    none
     *  @return	  void
     */
    public function notifyAction ()
    {
        $postData = $this->getRequest()->getPost();

        if (!count($postData)) {
            $this->norouteAction();
            return;
        }

        if ($this->getDebug()) {
            $debug = Mage::getModel('epayworxs/api_debug');
            if (isset($postData['cs2']) && $postData['cs2'] > 0) {
                $debug->setId($postData['cs2']);
            }
            $debug->setResponseBody(print_r($postData,1))->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId(Mage::helper('core')->decrypt($postData['cs1']));
        if ($order->getId()) {
            $result = $order->getPayment()->getMethodInstance()->setOrder($order)->validateResponse($postData);

            if ($result instanceof Exception) {
                if ($order->getId()) {
                    $order->addStatusToHistory(
                        $order->getStatus(),
                        $result->getMessage()
                    );
                    $order->cancel();
                }
                Mage::throwException($result->getMessage());
                return;
            }

            $order->sendNewOrderEmail();

            $order->getPayment()->getMethodInstance()->setTransactionId($postData['transaction_id']);

            if ($this->saveInvoice($order)) {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            }
            $order->save();
        }
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice (Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
            return true;
        }

        return false;
    }

    /**
     *  Failure response from epayworxs
     *
     *  @return	  void
     */
    public function failureAction ()
    {
        $errorMsg = Mage::helper('epayworxs')->__('There was an error occurred during paying process.');

        $order = $this->getOrder();
	//$order->setStatus('pending');
       
        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }
		
		$con = mysql_connect("localhost","root","abcd!234");
		mysql_select_db("reliablerx_new", $con);
		$query = "SELECT responses FROM  test_responses ORDER BY id DESC LIMIT 1";
		$result = mysql_query($query);
		$rows = mysql_fetch_row($result);
		$row = $rows[0];
		$row1 = explode("[STATUS] =>", $row);
		$row2 = explode("[CONTROL] =>", $row1[1]);
		$status = $row2[0];
		$status = trim($status);
		
		$order->setStatus("Incomplete Payment Process");
		
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            //$order->addStatusToHistory($order->getStatus(), $errorMsg);
            //$order->cancel();
            $order->save();
        }
		
        //$this->loadLayout();
        //$this->renderLayout();

        //Mage::getSingleton('checkout/session')->unsLastRealOrderId();
		
		$this->_redirect('checkout/onepage/worksfailure');
    }

}

