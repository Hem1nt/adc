<?php
require_once "Mage/Contacts/controllers/IndexController.php";  
class Iksula_Contactcustom_Contacts_IndexController extends Mage_Contacts_IndexController{
	public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                //$mailTemplate = Mage::getModel('core/email_template');
                /*
                if($post['querytype'] == "General Enquiries" || $post['querytype'] == "Sales Enquiries" || $post['querytype'] == "Medication Requests" || $post['querytype']=="Affiliate Program")
                {
                    $email="David@alldaychemist.com";
                }
                else if($post['querytype'] == "Payment Status" || $post['querytype'] == "Billing Department (wrong charges on CC, etc.)" || $post['querytype'] == "Website (cart, lost password, broken links, etc.)" || $post['querytype']=="Refunds (order never arrived, wrong item received etc.)" || $post['querytype']=="Price Matching")
                {
                    $email="eric@alldaychemist.com";
                }
                else if($post['querytype'] == "Order Cancellations (within 12 hours of ordering)" || $post['querytype'] == "Order Status (3-4 days after order is placed)" || $post['querytype'] == "Address Change or Add to Cart (within 12 hours of ordering)" || $post['querytype'] == "Shipping Department (rates, durations etc.)")
                {
                    // $email="kevin@alldaychemist.com";
                    $email="info@alldaychemist.com";
                }
                else if($post['querytype'] == "Phone orders (Representative will contact you in 24 working hours)")
                {
                    $email="paul@alldaychemist.com";
                } 
                */
                // $email="manoj.chowrasiya@iksula.com";
                $email="info@alldaychemist.com";
                $sender = array('name' => $post["name"], 'email' => $post["email"]);
                $vars = array('querytype' => $post['querytype'], 'productname' => $post['productname'],'custname' => $post['name'], 'custemail' => $post['email'], 'telephone' => $post['telephone'], 'ordernumber' => $post['ordernumber'], 'comment' => $post['comment']);
				// $vars = array('querytype' => $post['querytype'], 'custname' => $post['name'], 'custemail' => $post['email'], 'telephone' => $post['telephone'], 'ordernumber' => $post['ordernumber'], 'comment' => $post['comment']);
				//echo "<pre>"; print_r(get_class_methods($mailTemplate));
				//exit;
				$mailTemplate = Mage::getModel('core/email_template')->load('25');
				$mailTemplate->setTemplateSubject($post['querytype'])->save();
				$mailTemplate->sendTransactional(25, $sender, $email, "AllDayChemist", $vars, 1);

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }
}
				