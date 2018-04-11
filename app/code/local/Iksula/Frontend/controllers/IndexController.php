<?php
class Iksula_Frontend_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

	  $this->loadLayout();
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Frontend"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("frontend", array(
                "label" => $this->__("Frontend"),
                "title" => $this->__("Frontend")
		   ));

      $this->renderLayout();

    }

    public function getAllBrandAction(){
        $brand_data = new Iksula_Frontend_Block_Homesearch();
        $allBrandData['brands'] = $brand_data->allbrands(); 
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($allBrandData)); 
    } 

    public function allbrandsAction(){
        // $brand_data = new Iksula_Frontend_Block_Homesearch();
        // echo $brand_data->allbrands(); 
        $this->loadLayout();
        $this->renderLayout();
        // $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($allBrandData)); 
    }   

    public function testCountryAction(){
        $time = time();
        echo $to = date('Y-m-d H:i:s', $time);
        $lastTime = $time - 600;
        echo $from = date('Y-m-d H:i:s', $lastTime);
         $model = Mage::getModel('overrides/cron');
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->load();
        foreach ($orders as $order) {
                $model->updatecountry($order);
        }

    }

    public function testCountry2Action() {
        $orders = Mage::getModel('sales/order')->getCollection();
        $model = Mage::getModel('overrides/cron');
        foreach ($orders as $order) {
            $model->updatecountry($order);
        }
        
        // exit();
    }

   // homepage tab actions
    public function OffersAction() {

        $OffersProductId = Mage::getStoreConfig('custom_snippet/homepage_tabs/offers_product');

        $products = $this->getLayout()->createBlock('catalog/product_list')
        ->setCategoryId($OffersProductId)
        ->setTemplate('homepage/tab_products/offers.phtml')
        ->toHtml();

        $this->getResponse()->setBody($products);
        return;

    }

    public function FeaturedAction() {
      $FeaturedProductId = Mage::getStoreConfig('custom_snippet/homepage_tabs/featured_product');

      $featuredproducts = $this->getLayout()->createBlock('catalog/product_list')
        ->setCategoryId($FeaturedProductId)
        ->setTemplate('homepage/tab_products/featured.phtml')
        ->toHtml();

        $this->getResponse()->setBody($featuredproducts);
        return;

    }

    public function CategoryAction() {

      $categoryProductId = Mage::getStoreConfig('custom_snippet/homepage_tabs/category_product');
      $categoryproducts = $this->getLayout()->createBlock('catalog/product_list')
        ->setCategoryId($categoryProductId)
        ->setTemplate('homepage/tab_products/category.phtml')
        ->toHtml();

        $this->getResponse()->setBody($categoryproducts);
        return;

    }




    public function trustAction(){
      $_helper = Mage::helper('frontend/data');
      $orderCollection = Mage::getModel('sales/order')->getCollection()->setPageSize(20)->setCurPage(1);
      foreach ($orderCollection as $key => $order) {
        echo $_helper->getTrustedReviewLink($order);
        echo '<br/>';
      }

    }
    public function trustAction2(){
//         echo "<pre>";
//         $data= array('responce' => array('dd'));
// // print_r($data);
//         if(is_array($data['responce'])){
//             $data2 = implode(',',$data['responce']);
//             var_dump($data2);
//         }else{
//             echo 'Not Array';
//         }
//         exit();
        //http://trustedcompany.com/review-invite/%5B1%5D/ul/%5B2%5D?a=[3]&b=[4]&c=[5]

        $baseUrl  = 'http://trustedcompany.com/review-invite';
        $companyId = '569653';
        $companySecretKey = '2e2d62f821565f17f63ff66559f39de93d12f2e1';
        $email_address = 'manojiksula@gmail.com';
        $encodedEmail = base64_encode($email_address);
        $customerName = urlencode('Manoj Chaurasia');
        $orderIncrementId = 'ADC100146921';
        $orderId = 1;

        $hashKey = SHA1($companyId + $companySecretKey + $email_address + $orderId);
        $masterUrl = $baseUrl.'/'.$companyId.'/ul/'.$hashKey.'?a='.$encodedEmail.'&b='.$customerName.'&c='.$orderIncrementId;

        echo $masterUrl;
        exit();


        $urlId = (int)$this->getRequest()->getParam('order_id');
        if(!empty($urlId)) {
            $orderId = $urlId;
        }

        // Load the order
        $order = Mage::getModel('sales/order')->load($orderId);
        echo '<pre>';
        print_r($order->getData());exit();
        // Order on the order

    }
    public function successAction()
    {
        // Check access
        // if(Mage::helper('frontend')->hasAccess() == false) {
        //     die('Access denied');
        // }
        // echo 'test';exit;

        // Load the order ID
        $orderId = 119184;//(int)Mage::helper('frontend')->getOrderId();
        $urlId = (int)$this->getRequest()->getParam('order_id');
        if(!empty($urlId)) {
            $orderId = $urlId;
        }

        // Load the order
        $order = Mage::getModel('sales/order')->load($orderId);
        // echo '<pre>';
        // print_r($order);exit();
        // Order on the order
        if(!$order->getId() > 0) {
            die('Invalid order ID');
        }

        // Load the session
        Mage::getModel('checkout/session')->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());

        // Load the layout
        $this->loadLayout();

        // Optionally dispatch an event
        // if((bool)Mage::getStoreConfig('frontend/settings/checkout_onepage_controller_success_action')) {
            Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($orderId)));
        // }

        // Render the layout
        $this->renderLayout();
    }

    public function affiliateAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function affiliatesubmitAction()
    {
        $firstname = $this->getRequest()->getPost('firstname');
        $lastname = $this->getRequest()->getPost('lastname');
        $email = $this->getRequest()->getPost('email');
        $emailAddress = $this->getRequest()->getPost('email');
        $gendervalue = $this->getRequest()->getPost('gender');
        if($gendervalue == 1) {
            $gender = 'Male';
        } else {
            $gender = 'Female';
        }

        $dob = $this->getRequest()->getPost('dob');
        $password = $this->getRequest()->getPost('password');

        // mail sending

        $senderName = 'AllDayChemist';
        $senderEmail = 'alldaychemist.com';

        $sender = array(
                    'name' => $senderName,
                    'email' => $senderEmail
                    );

        // Set recepient information
        $recepientEmailID = Mage::getStoreConfig('custom_snippet/affiliate_tabs/affiliate_email');

        $recepientEmail = $recepientEmailID;
        $recepientName = 'Alldaychemist';

        $templateId = 49;
        // Get Store ID
        $storeId = Mage::app()->getStore()->getId();

        // Set variables that can be used in email template
        $vars = array(
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'emailAddress' => $emailAddress,
                    'gender' => $gender,
                    'dob' => $dob,
                    'password' => $password
                    );

        // print_r($vars);exit();
        $translate  = Mage::getSingleton('core/translate');

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

        $translate->setTranslateInline(true);

        $usersender = array(
                    'name' => 'AllDaychemist',
                    'email' => $recepientEmailID
                    );

        // Set recepient information

        $userrecepientEmail = $email;
        $userrecepientName = 'Alldaychemist';

        $usertemplateId = 50;
        // Get Store ID
        $userstoreId = Mage::app()->getStore()->getId();



        // Set variables that can be used in email template
        $uservars = array('firstname' => $firstname,
                    'lastname' => $lastname
                    );

        $usertranslate  = Mage::getSingleton('core/translate');

        // Send Transactional Email
        Mage::getModel('core/email_template')
            ->sendTransactional($usertemplateId, $usersender, $userrecepientEmail, $userrecepientName, $uservars, $userstoreId);

        $usertranslate->setTranslateInline(true);

        Mage::getSingleton('core/session')->addSuccess('Thankyou for submitting your details');
        $this->_redirect('frontend/index/affiliate');
    }
    public function generateJsonAction()
    {
        $mediaUrl = Mage::getBaseDir('media');
        $filePath = $mediaUrl.'/equivalent_search2.json';

        $homeSearchJson = array();
        $collection = Mage::getSingleton('catalog/product')
        ->getCollection()
        ->addAttributeToSort('name', 'ASC')
        ->addAttributeToFilter(
            'status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('us_brand_name');
        $collection->addAttributeToSelect('active_ingridients');
        $collection->addAttributeToSelect('generic_name');
        $collection->addAttributeToFilter('type_id', array('eq' => 'configurable'));
        // echo '<pre>';print_r($collection->getData());exit();
        foreach ($collection as $product) {
            $data = $product->getData('us_brand_name');
            $data = str_replace('cream','',$data);
            $brand_active[]= trim($data);
        }

        $finalBrand_active=array_unique(array_filter($brand_active));
        asort($finalBrand_active);

        foreach ($collection as $product) {
            $data = $product->getData('generic_name');
            $data = str_replace('cream','',$data);
            $active[]= trim($data);
        }

        $final_active=array_unique(array_filter($active));
        asort($final_active);

        $homeSearchJson['us_brand_name'] = $finalBrand_active;
        $homeSearchJson['generic_name'] = $final_active;

        $fp = fopen($filePath, 'w+');

        fwrite($fp, json_encode($homeSearchJson));
        fclose($fp);
        echo "successfully generated file";
    }
    /*Captcha validation*/
    public function captchaAction(){

        $captchaSession=$this->getRequest()->getPost('captcha');
        $captchsSessionValue=Mage::getSingleton('core/session')->setCaptchaValue($captchaSession);
    }

    public function hearfromusAction()
    {
        $data = $this->getRequest()->getParams();
        $valueId = $data['attribute'];
        $cart = Mage::getSingleton('checkout/session')->getQuote();
        $quote = Mage::getModel('sales/quote')->load($cart->getData('entity_id'));
        if(array_key_exists('message',$data)){
            $message = $data['message'];
            $quote->setFindUs($valueId);
            $quote->setFindUsOther($message);
            $quote->save();
        }else{
            $quote->setFindUs($valueId);
            $quote->save();
        }
    }

}
