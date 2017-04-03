<?php
// include_once("Mage/Adminhtml/controllers/IndexController.php");
// require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'IndexController.php';
require_once "Mage/Adminhtml/controllers/IndexController.php";
class Iksula_Overrides_Adminhtml_IndexController extends Mage_Adminhtml_IndexController{

    protected $_publicActions = array('redirecttocustomer');
    /**
     * Render specified template
     *
     * @param string $tplName
     * @param array $data parameters required by template
     */
    protected function _outTemplate($tplName, $data = array())
    {
        $this->_initLayoutMessages('adminhtml/session');
        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate("$tplName.phtml");
        foreach ($data as $index => $value) {
            $block->assign($index, $value);
        }
        $html = $block->toHtml();
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        $this->getResponse()->setBody($html);
    }

    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $session = Mage::getSingleton('admin/session');
        $url = $session->getUser()->getStartupPageUrl();
        if ($session->isFirstPageAfterLogin()) {
            // retain the "first page after login" value in session (before redirect)
            $session->setIsFirstPageAfterLogin(true);
        }
        $this->_redirect($url);
    }

    /**
     * Administrator login action
     */
    public function loginAction()
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $this->_redirect('*');
            return;
        }
        $loginData = $this->getRequest()->getParam('login');
        $username = (is_array($loginData) && array_key_exists('username', $loginData)) ? $loginData['username'] : null;

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Administrator logout action
     */
    public function logoutAction()
    {
        /** @var $adminSession Mage_Admin_Model_Session */
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());
        $adminSession->addSuccess(Mage::helper('adminhtml')->__('You have logged out.'));

        $this->_redirect('*');
    }

    /**
     * Custom Search Action for Customer
     */

    public function customerSearchAction()
    {
        $params = $this->getRequest()->getParams();
        $query = $params['query'];

        $model = Mage::getSingleton('customer/customer');
        $items = array();

        $result = $model->getCollection()
        ->setPageSize(10)
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('email', array('like' => "%$query%"));
        if (count($result) > 0) {

            $i = 0;
            foreach ($result as $value) {
              $firstname = $value->getData('firstname');
              $lastname = $value->getData('lastname');
              $entity_id = $value->getData('entity_id');
              $email = $value->getData('email');
              $url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/", array("id"=>$entity_id));

              $items[$i]['email'] = $email;
              $items[$i]['name'] = $firstname .' '.$lastname;
              $items[$i]['url'] = $url;
              $i++;
            }
        } else {
            $query = explode(" ",$query);
            if(count($query) > 1) {

                $firstname = $query[0];
                $lastname = $query[1];

                $collection_name = $model->getCollection()
                ->setPageSize(10)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('firstname', array('like' => "%$firstname%"))
                ->addAttributeToFilter('lastname', array('like' => "%$lastname%"));

            } else {
                $name = $query[0];

                $collection_name = $model->getCollection()
                ->setPageSize(10)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('firstname', array('like' => "%$name%"));

                if(count($collection_name) == 0) {
                    $collection_name = $model->getCollection()
                    ->setPageSize(10)
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('lastname', array('like' => "%$name%"));
                }
            }

            $i = 0;
            foreach ($collection_name as $value) {
                $firstname = $value->getData('firstname');
                $lastname = $value->getData('lastname');
                $entity_id = $value->getData('entity_id');
                $email = $value->getData('email');
                $url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/", array("id"=>$entity_id));

                $items[$i]['email'] = $email;
                $items[$i]['name'] = $firstname .' '.$lastname;
                $items[$i]['url'] = $url;
                $i++;
            }
        }

        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('system/customerautocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Custom Search Action for Order
     */

    public function orderSearchAction()
    {
        $params = $this->getRequest()->getParams();
        $query = $params['query'];
        $result = array();

        $query = explode(" ",$query);

        //check result in order Id first
        $model = Mage::getSingleton('sales/order');

        if(count($query) == 1 && preg_match('#[0-9]#',$query[0])) {
            $id = $query[0];
            $increment_id_result = $model->getCollection()
            ->setPageSize(10)
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('increment_id')
            ->addFieldToFilter('increment_id', array('like' => "%$id%"));

            $result['increment_id_result'] = $increment_id_result->getData();

            // check the query in custom order Id
            if(count($increment_id_result) <= 0) {
                $customer_order_increment_id_result = $model->getCollection()
                    ->setPageSize(10)
                    ->addAttributeToSelect('entity_id')
                    ->addAttributeToSelect('increment_id')
                    ->addFieldToFilter(
                            'customer_order_increment_id', array('like' => "%$id%")
                    );

                    $result['customer_order_increment_id_result'] = $customer_order_increment_id_result->getData();
            }
        } else {
            // check if query count is 1 or 2
            if(count($query) > 1) {

                $firstname = $query[0];
                $lastname = $query[1];
                // check for customer name
                    $customer_name_result = $model->getCollection()
                        ->setPageSize(10)
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToSelect('increment_id')
                        ->addAttributeToFilter('customer_firstname', array('like' => "%$firstname%"))
                        ->addAttributeToFilter('customer_lastname', array('like' => "%$lastname%"));

                $result['customer_name_result'] = $customer_name_result->getData();

                // check for customer billing name

                if(count($customer_name_result) <= 0) {
                    $itemsCollection = Mage::getModel('sales/order')->getCollection()
                            ->setPageSize(10)
                            ->addAttributeToSelect('increment_id')
                            ->addAttributeToSelect('entity_id');

                    $itemsCollection->getSelect()->join(
                            array(
                                'address'=>Mage::getSingleton('core/resource')->getTableName('sales/order_address')),
                                'main_table.entity_id = address.parent_id',
                                array('address.country_id','address.telephone','address.region','address.city','address.street','address.region_id')
                            )->where("address.firstname = '$firstname' AND address.lastname = '$lastname'");

                    $result['billing_customer_name_result'] = $itemsCollection->getData();
                }

            } else {
                $name = $query[0];

                $firstname_result = $model->getCollection()
                ->setPageSize(10)
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('increment_id')
                ->addAttributeToFilter('customer_firstname', array('like' => "%$name%"));

                $result['firstname_result'] = $firstname_result->getData();

                if(count($firstname_result) <= 0) {
                   $lastname_result = $model->getCollection()
                  ->setPageSize(10)
                  ->addAttributeToSelect('entity_id')
                  ->addAttributeToSelect('increment_id')
                  ->addAttributeToFilter('customer_lastname', array('like' => "%$name%"));

                    $result['lastname_result'] = $lastname_result->getData();
                }

                if(count($lastname_result) <= 0) {
                    $firstitemsCollection = $model->getCollection()
                            ->setPageSize(10)
                            ->addAttributeToSelect('increment_id')
                            ->addAttributeToSelect('entity_id');

                    $firstitemsCollection->getSelect()->join(
                            array(
                                'address'=>Mage::getSingleton('core/resource')->getTableName('sales/order_address')),
                                'main_table.entity_id = address.parent_id',
                                array('address.country_id','address.telephone','address.region','address.city','address.street','address.region_id')
                            )->where("address.firstname = '$name'");

                    $result['billing_firstname_result'] = $firstitemsCollection->getData();
                }

                if(count($firstitemsCollection->getData()) <= 0) {
                    $lastitemsCollection = $model->getCollection()
                            ->setPageSize(10)
                            ->addAttributeToSelect('increment_id')
                            ->addAttributeToSelect('entity_id');
                    $lastitemsCollection->getSelect()->join(
                            array(
                                'flat_address'=>Mage::getSingleton('core/resource')->getTableName('sales/order_address')),
                                'main_table.entity_id = flat_address.parent_id',
                                array('flat_address.country_id','flat_address.telephone','flat_address.region','flat_address.city','flat_address.street','flat_address.region_id')
                            )->where("flat_address.lastname = '$name'");

                    $result['billing_lastname_result'] = $lastitemsCollection->getData();
                }
            }
        }


        $flat_array = call_user_func_array('array_merge', $result);
        $merge_array = array_map("unserialize", array_unique(array_map("serialize", $flat_array)));

        $items = $this->convertArray($merge_array);

        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('system/orderautocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());

    }


    public function convertArray($array) {
        $newUserList = array();
        $i=0;
        foreach ( $array as $value ) {
            $newUserList[$i]['entity_id'] = $value['entity_id'];
            $newUserList[$i]['increment_id'] = $value['increment_id'];
            $i++;
        }

        return $newUserList;
    }

    /**
     * Global Search Action
     */
    public function globalSearchAction()
    {
        $searchModules = Mage::getConfig()->getNode("adminhtml/global_search");
        $items = array();

        if (!Mage::getSingleton('admin/session')->isAllowed('admin/global_search')) {
            $items[] = array(
                'id' => 'error',
                'type' => Mage::helper('adminhtml')->__('Error'),
                'name' => Mage::helper('adminhtml')->__('Access Denied'),
                'description' => Mage::helper('adminhtml')->__('You have not enough permissions to use this functionality.')
            );
            $totalCount = 1;
        } else {
            if (empty($searchModules)) {
                $items[] = array(
                    'id' => 'error',
                    'type' => Mage::helper('adminhtml')->__('Error'),
                    'name' => Mage::helper('adminhtml')->__('No search modules were registered'),
                    'description' => Mage::helper('adminhtml')->__('Please make sure that all global admin search modules are installed and activated.')
                );
                $totalCount = 1;
            } else {
                $start = $this->getRequest()->getParam('start', 1);
                $limit = $this->getRequest()->getParam('limit', 10);
                $query = $this->getRequest()->getParam('query', '');
                foreach ($searchModules->children() as $searchConfig) {

                    if ($searchConfig->acl && !Mage::getSingleton('admin/session')->isAllowed($searchConfig->acl)){
                        continue;
                    }

                    $className = $searchConfig->getClassName();

                    if (empty($className)) {
                        continue;
                    }
                    $searchInstance = new $className();
                    $results = $searchInstance->setStart($start)
                        ->setLimit($limit)
                        ->setQuery($query)
                        ->load()
                        ->getResults();
                    $items = array_merge_recursive($items, $results);
                }
                $totalCount = sizeof($items);
            }
        }

        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('system/autocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Example action
     */
    public function exampleAction()
    {
        $this->_outTemplate('example');
    }

    /**
     * Test action
     */
    public function testAction()
    {
        echo $this->getLayout()->createBlock('core/profiler')->toHtml();
    }

    /**
     * Change locale action
     */
    public function changeLocaleAction()
    {
        $locale = $this->getRequest()->getParam('locale');
        if ($locale) {
            Mage::getSingleton('adminhtml/session')->setLocale($locale);
        }
        $this->_redirectReferer();
    }

    /**
     * Denied JSON action
     */
    public function deniedJsonAction()
    {
        $this->getResponse()->setBody($this->_getDeniedJson());
    }

    /**
     * Retrieve response for deniedJsonAction()
     */
    protected function _getDeniedJson()
    {
        return Mage::helper('core')->jsonEncode(array(
            'ajaxExpired' => 1,
            'ajaxRedirect' => $this->getUrl('*/index/login')
        ));
    }

    /**
     * Denied IFrame action
     */
    public function deniedIframeAction()
    {
        $this->getResponse()->setBody($this->_getDeniedIframe());
    }

    /**
     * Retrieve response for deniedIframeAction()
     */
    protected function _getDeniedIframe()
    {
        return '<script type="text/javascript">parent.window.location = \''
            . $this->getUrl('*/index/login') . '\';</script>';
    }

    /**
     * Forgot administrator password action
     */
    public function forgotpasswordAction()
    {
        $email = (string) $this->getRequest()->getParam('email');
        $params = $this->getRequest()->getParams();

        if (!empty($email) && !empty($params)) {
            // Validate received data to be an email address
            if (Zend_Validate::is($email, 'EmailAddress')) {
                $collection = Mage::getResourceModel('admin/user_collection');
                /** @var $collection Mage_Admin_Model_Resource_User_Collection */
                $collection->addFieldToFilter('email', $email);
                $collection->load(false);

                if ($collection->getSize() > 0) {
                    foreach ($collection as $item) {
                        $user = Mage::getModel('admin/user')->load($item->getId());
                        if ($user->getId()) {
                            $newResetPasswordLinkToken = Mage::helper('admin')->generateResetPasswordLinkToken();
                            $user->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                            $user->save();
                            $user->sendPasswordResetConfirmationEmail();
                        }
                        break;
                    }
                }
                $this->_getSession()
                    ->addSuccess(Mage::helper('adminhtml')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('adminhtml')->escapeHtml($email)));
                $this->_redirect('*/*/login');
                return;
            } else {
                $this->_getSession()->addError($this->__('Invalid email address.'));
            }
        } elseif (!empty($params)) {
            $this->_getSession()->addError(Mage::helper('adminhtml')->__('The email address is empty.'));
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display reset forgotten password form
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
     */
    public function resetPasswordAction()
    {
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $userId = (int) $this->getRequest()->getQuery('id');
        try {
            $this->_validateResetPasswordLinkToken($userId, $resetPasswordLinkToken);
            $data = array(
                'userId' => $userId,
                'resetPasswordLinkToken' => $resetPasswordLinkToken
            );
            $this->_outTemplate('resetforgottenpassword', $data);
        } catch (Exception $exception) {
            $this->_getSession()->addError(Mage::helper('adminhtml')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/forgotpassword', array('_nosecret' => true));
        }
    }

    /**
     * Reset forgotten password
     *
     * Used to handle data recieved from reset forgotten password form
     */
    public function resetPasswordPostAction()
    {
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $userId = (int) $this->getRequest()->getQuery('id');
        $password = (string) $this->getRequest()->getPost('password');
        $passwordConfirmation = (string) $this->getRequest()->getPost('confirmation');

        try {
            $this->_validateResetPasswordLinkToken($userId, $resetPasswordLinkToken);
        } catch (Exception $exception) {
            $this->_getSession()->addError(Mage::helper('adminhtml')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/');
            return;
        }

        $errorMessages = array();
        if (iconv_strlen($password) <= 0) {
            array_push($errorMessages, Mage::helper('adminhtml')->__('New password field cannot be empty.'));
        }
        /** @var $user Mage_Admin_Model_User */
        $user = Mage::getModel('admin/user')->load($userId);

        $user->setNewPassword($password);
        $user->setPasswordConfirmation($passwordConfirmation);
        $validationErrorMessages = $user->validate();
        if (is_array($validationErrorMessages)) {
            $errorMessages = array_merge($errorMessages, $validationErrorMessages);
        }

        if (!empty($errorMessages)) {
            foreach ($errorMessages as $errorMessage) {
                $this->_getSession()->addError($errorMessage);
            }
            $data = array(
                'userId' => $userId,
                'resetPasswordLinkToken' => $resetPasswordLinkToken
            );
            $this->_outTemplate('resetforgottenpassword', $data);
            return;
        }

        try {
            // Empty current reset password token i.e. invalidate it
            $user->setRpToken(null);
            $user->setRpTokenCreatedAt(null);
            $user->setPasswordConfirmation(null);
            $user->save();
            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Your password has been updated.'));
            $this->_redirect('*/*/login');
        } catch (Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
            $data = array(
                'userId' => $userId,
                'resetPasswordLinkToken' => $resetPasswordLinkToken
            );
            $this->_outTemplate('resetforgottenpassword', $data);
            return;
        }
    }

    /**
     * Check if password reset token is valid
     *
     * @param int $userId
     * @param string $resetPasswordLinkToken
     * @throws Mage_Core_Exception
     */
    protected function _validateResetPasswordLinkToken($userId, $resetPasswordLinkToken)
    {
        if (!is_int($userId)
            || !is_string($resetPasswordLinkToken)
            || empty($resetPasswordLinkToken)
            || empty($userId)
            || $userId < 0
        ) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Invalid password reset token.'));
        }

        /** @var $user Mage_Admin_Model_User */
        $user = Mage::getModel('admin/user')->load($userId);
        if (!$user || !$user->getId()) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Wrong account specified.'));
        }

        $userToken = $user->getRpToken();
        if (strcmp($userToken, $resetPasswordLinkToken) != 0 || $user->isResetPasswordLinkTokenExpired()) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Your password reset link has expired.'));
        }
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }

}
