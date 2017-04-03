<?php
require_once "Mage/Contacts/controllers/IndexController.php";
class Iksula_Contactcustom_Contacts_IndexController extends Mage_Contacts_IndexController{

	public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ($post) {

            $saveContact = array(
                'name' =>$post['name'],
                'email' =>$post['email'],
                'querytype' => $post['querytype'],
                'telephone' =>$post['telephone'],
                'ordernumber' =>$post['ordernumber'],
                'comment' =>$post['comment'],
                'productname' =>$post['productname'],
                'timetocall' =>$post['timetocall'],
                'timezone' =>$post['timezone'],
            );

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

                 /* Start to Fetch email and copy email id according to query type from configuration*/
                $queryEmail = array();
                $queryCopyEmail = array();
                $templateId = Mage::getStoreConfig("custom_snippet/snippet/contactus_template");
                $queryStatus = Mage::getStoreConfig("custom_snippet/snippet/query_mail_select");
                if($queryStatus == 1){
                    foreach (unserialize(Mage::getStoreConfig("custom_snippet/snippet/query_email")) as $mapping) {
                        if (array_key_exists('query_type_data', $mapping)) {
                         $queryEmail[$mapping['query_type_data']] = $mapping['email_id'];
                         $queryCopyEmail[$mapping['query_type_data']] = $mapping['copy_email_id'];
                        }
                    }
                    $email = $queryEmail[$post['querytype']];
                    if($email == ""){
                        $email = Mage::getStoreConfig("custom_snippet/snippet/default_email");
                    }
                    $emailcopy = $queryCopyEmail[$post['querytype']];
                }
                else{
                    $email = Mage::getStoreConfig("custom_snippet/snippet/default_email");
                }
                /* End to Fetch email and copy email id according to query type from configuration*/

                if($post['querytype']=='Request a Call Back'){
                    $timetocall =  $post['timetocall'].' '.$post['timezone'];
                }else{
                    $timetocall ='N/A';
                }
                if($post['querytype']=='Medication Requests'){
                    $productname =  $post['productname'];
                }else{
                    $productname ='';
                }
                
                $sender_name =  Mage::getStoreConfig('trans_email/ident_general/name'); 
                $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');

                $sender = array('name' => $sender_name, 'email' => $sender_email);

                $vars = array('querytype' => $post['querytype'], 'timetocall' => $timetocall,'productname' => $productname,'custname' => $post['name'], 'custemail' => $post['email'], 'telephone' => $post['telephone'], 'ordernumber' => $post['ordernumber'], 'comment' => $post['comment']);
  				$mailTemplate = Mage::getModel('core/email_template')->load($templateId);
				$mailTemplate->setTemplateSubject($post['querytype'])->save();
                if($emailcopy !=""){
                    $mailTemplate->addBcc($emailcopy);
                }
                $mailTemplate->setReplyTo($post['email']);
                $mailTemplate->sendTransactional($templateId, $sender, $email, "AllDayChemist", $vars, 1);

                $translate->setTranslateInline(true);

                Mage::getModel('querylogs/information')->setData($saveContact)->save();

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

    public function searchPostAction()
    {
        $post = $this->getRequest()->getPost();
        $url = $post['redirect_url'];
        if ($post) {

            $saveContact = array(
                'name' =>$post['name'],
                'email' =>$post['email'],
                'querytype' => $post['querytype'],
                'telephone' =>$post['telephone'],
                'ordernumber' =>$post['ordernumber'],
                'comment' =>$post['comment'],
                'productname' =>$post['productname'],
                'timetocall' =>$post['timetocall'],
                'timezone' =>$post['timezone'],
            );

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

                 /* Start to Fetch email and copy email id according to query type from configuration*/
                $queryEmail = array();
                $queryCopyEmail = array();
                $templateId = Mage::getStoreConfig("custom_snippet/snippet/contactus_template");
                $queryStatus = Mage::getStoreConfig("custom_snippet/snippet/query_mail_select");
                if($queryStatus == 1){
                    foreach (unserialize(Mage::getStoreConfig("custom_snippet/snippet/query_email")) as $mapping) {
                        if (array_key_exists('query_type_data', $mapping)) {
                         $queryEmail[$mapping['query_type_data']] = $mapping['email_id'];
                         $queryCopyEmail[$mapping['query_type_data']] = $mapping['copy_email_id'];
                        }
                    }
                    $email = $queryEmail[$post['querytype']];
                    if($email == ""){
                        $email = Mage::getStoreConfig("custom_snippet/snippet/default_email");
                    }
                    $emailcopy = $queryCopyEmail[$post['querytype']];
                }
                else{
                    $email = Mage::getStoreConfig("custom_snippet/snippet/default_email");
                }
                /* End to Fetch email and copy email id according to query type from configuration*/

                if($post['querytype']=='Request a Call Back'){
                    $timetocall =  $post['timetocall'].' '.$post['timezone'];
                }else{
                    $timetocall ='N/A';
                }
                if($post['querytype']=='Medication Requests'){
                    $productname =  $post['productname'];
                }else{
                    $productname ='';
                }
                
                $sender_name =  Mage::getStoreConfig('trans_email/ident_general/name'); 
                $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');

                $sender = array('name' => $sender_name, 'email' => $sender_email);

                $vars = array('querytype' => $post['querytype'], 'timetocall' => $timetocall,'productname' => $productname,'custname' => $post['name'], 'custemail' => $post['email'], 'telephone' => $post['telephone'], 'ordernumber' => $post['ordernumber'], 'comment' => $post['comment']);
                $mailTemplate = Mage::getModel('core/email_template')->load($templateId);
                $mailTemplate->setTemplateSubject($post['querytype'])->save();
                if($emailcopy !=""){
                    $mailTemplate->addBcc($emailcopy);
                }
                $mailTemplate->setReplyTo($post['email']);
                $mailTemplate->sendTransactional($templateId, $sender, $email, "AllDayChemist", $vars, 1);

                $translate->setTranslateInline(true);

                Mage::getModel('querylogs/information')->setData($saveContact)->save();

                Mage::getSingleton('core/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                // $this->_redirect('*/*/');
                $this->_redirectUrl($url);


                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('core/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                // $this->_redirect('*/*/');
                $this->_redirectUrl($url);

                return;
            }

        } else {
            // $this->_redirect('*/*/');
            $this->_redirectUrl($url);

        }
    }
}

