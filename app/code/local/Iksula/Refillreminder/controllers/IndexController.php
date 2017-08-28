<?php
class Iksula_Refillreminder_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function indexAction() {
     
      $block = $this->getLayout()->createBlock("core/template")->setTemplate("refillreminder/index.phtml");
      echo $block->toHtml();
      // $this->renderLayout();
    //     $order_id = $this->getRequest()->getParam('pid'); 
    //     //var_dump($order_id);die;
    //     $name=$this->getRequest()->getParam('name');
    // $mail=$this->getRequest()->getParam('txtmail');
    // $phone=$this->getRequest()->getParam('txtphone');
    // //$ProductId = $this->getRequest()->getPost('txtproduct_id');
    // $Days = $this->getRequest()->getPost('remind_days');
    // //var_dump($Days);die;


    // $model = Mage::getModel('refillreminder/refillreminder');


    //    $data = array('customer_email'=>$mail,
    //                 'customer_name'=>$name,
    //                 'reminder_days'=>$Days,
    //                 'remind_flag'=>1,
    //                 'customer_telephone'=>$phone,
    //                 //'order_id'=>$orderIncrementId,
    //                 );
    //         try {
    //                 $model->setData($data)->save();
    //                 // print_r($model);exit();

    //             } catch (Exception $e) {
    //                 echo $e->getMessage();
    //             }

    }

    public function postAction() {

      $ProductId = $this->getRequest()->getPost('txtproduct_id');
      $Days = $this->getRequest()->getPost('remind_days');
      $CustomerMail = $this->getRequest()->getPost('txtmail');
      $custPhone = $this->getRequest()->getPost('txtphone');
      $ProductSku = Mage::getModel('catalog/product')->load($ProductId)->getSku();

      $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($ProductId);
      $productObj  = Mage::getModel('catalog/product')->load($parent_ids[0]);
      $ProductName = $productObj->getName();
      $ProductStrength = $productObj->getConfigurableAttribute();
      $productDetails = $ProductName.' '.$ProductStrength;

      $model = Mage::getModel('refillreminder/refillreminder');
      $collection = $model->getCollection();
      $collection->addFieldToFilter('product_sku', $ProductSku);
      $collection->addFieldToFilter('customer_email', $CustomerMail);
      // echo ; exit;
      if($collection->getSize()) {
        echo "Already subscribed this product.";
      } else {
        $data = array('customer_email'=>$CustomerMail,
        'product_sku'=>$ProductSku,
        'reminder_days'=>$Days,
        'remind_flag'=>0,
        'customer_telephone'=>$custPhone,
        'comment'=>'',
        'product_name'=>$productDetails,
        'created_date'=>date('Y-m-d H:i:s', time()),
        'modified_date'=>date('Y-m-d H:i:s', time()),
        'last_mail_sent'=>date('Y-m-d H:i:s', time()),
        'next_mail_on'=>date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', time()). '+ '.$Days.' days ')),
        'status'=>1,
        'mail_sent_count'=>0);


        $product_sku = $data['product_sku'];
        $date_of_reminder = date('dS F Y', strtotime(date('Y-m-d H:i:s', time()). '+ '.$Days.' days '));

        $customer_email = $data['customer_email'];
        $product_name = $data['product_name'];
        $helperdata = Mage::helper("refillreminder");

        $newdata = $helperdata->getParentproduct($product_sku);

        $productObj = Mage::getSingleton('catalog/product');

        $productId = $productObj->getIdBySku($product_sku);

        if($productId) {
         $childproduct =  $productObj->load($productId);
         $packsize = $childproduct->getData('pack_size');
        }

        $attr = $childproduct->getResource()->getAttribute("pack_size");
        if ($attr->usesSource()) {
          $packsize_label = $attr->getSource()->getOptionText($packsize);
        }
        $parentProductCollection = Mage::getSingleton('catalog/product')->load($newdata[0]);
        $productUrl = Mage::getUrl().$parentProductCollection->getUrlPath().'?utm_source=email-rr&utm_medium=email&utm_campaign=email-rr';

        $singleProductId = $parentProductCollection->getIdBySku($product_sku); //load configurable product using sku
        $singleProduct = $parentProductCollection->load($singleProductId);

        $generic_name = $parentProductCollection->getData('generic_name');
        $configurable_attribute = $parentProductCollection->getData('configurable_attribute');

        $tbl.="<tr>";
        $tbl.='<td style="width:140px; padding:5px"><a href="'.$productUrl.'"><img width="100px" src="'.$parentProductCollection->getImageUrl().'"/></a></td>';
        $tbl.='<td style="padding:10px"><p><a style="font-weight: bold; font-size: 20px; color: #719102;" href="'.$productUrl.'">'.$product_name.'</a></p>';
        $tbl.='<p>'.$packsize_label.' '.$parentProductCollection->getAttributeText('pharmaceutical_form').' -<span style="font-size: 18px; font-weight: bold;"> US$'.$singleProduct->getPrice().'</span></p>';
        if($parentProductCollection->getDescription()) {
        $tbl.='<p>'.substr($parentProductCollection->getDescription(), 0, 200).'...</p>';
        }
        $tbl.='<p><a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.$productUrl.'">View Details</a> <a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.Mage::getBaseUrl().'refillreminder/view/">Edit Refill</a></p>';
        $tbl.='</td>';

        $tbl.="</tr>";
        try{
             $model->setData($data)->save();
        } catch (Exception $e) {
            echo $e->getMessage();
       }

       // $customer_email = "nilesh@sharklasers.com";
       $customer = Mage::getModel("customer/customer");
       $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
       $customer->loadByEmail($customer_email); //load customer by email id

       if($customer->getFirstName()){
         $fname = $customer->getFirstName();
       }else{
         $fname = 'Customer';
       }

        $message = $tbl;
        $status = 1;
        $eid = 234364;
        $producttitle = 'Your Product';
        $refillreminder = Mage::helper('refillreminder');
        $refillreminder->sendMail($CustomerMail,$status,$eid,$fname,$message,$producttitle,$date_of_reminder);
        echo "Product has been successfully added in Refill Reminder.";
      }      //exit;


    }

    public function testAction() {
      $this->sendProductReminder();
       $this->sendOrderReminder();
      // echo 'hJJJi';
    }

    public function sendProductReminder() {
        $model = Mage::getModel('refillreminder/refillreminder'); //model to process reminder checking data

        $collection = $model->getCollection();
        $collection->getSelect()->where("remind_flag = 1 AND mail_sent_count< 1 AND reminder_days = ABS(DATEDIFF(DATE(NOW()), DATE(last_mail_sent)))");
         // print_r($collection->getData());
        // exit;
        if($collection->getSize()) {
            $userArray = array();
            foreach($collection->getData() as $allReminderData) {
                $userArray[$allReminderData['customer_email']][] = $allReminderData['product_sku'];
                $data = array('last_mail_sent' => date('Y-m-d H:i:s', time()), 'next_mail_on' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', time()). '+ '.$allReminderData['reminder_days'].' days ')), 'mail_sent_count' => mail_sent_count+1); //primary key of reminder table which will update

               $UpdateModel = $model->load()->addData($data);
                try {
                   $UpdateModel->setId($allReminderData['reminder_id'])->save();
                    echo "Data has been updated successfully.";
                } catch (Exception $e){
                    echo $e->getMessage();
                }
            }
        }
        // echo "<pre>"; print_r($userArray); exit;
        $productModel = Mage::getModel('catalog/product');
        foreach($userArray as $mail=>$skus) {
          //$tbl = '<table border="1" style="border-collapse:collapse;"><tr><td style="padding:10px">Product SKU</td><td style="padding:10px">Pack Size</td><td style="padding:10px">Product Name</td><td style="padding:10px">Product Image</td></tr>';
          $tbl="";
          $skuCount = count($skus);
          $product_s = "Your Products";
          if($skuCount==1) {
            $product_s = "Your Product";
          }
          foreach($skus as $sku) {
            $skuAndPackSize = explode("-", $sku);
            // echo $sku;
            $configurableProductId = $productModel->getIdBySku($skuAndPackSize[0]); //load configurable product using sku
            $configurableProduct = $productModel->load($configurableProductId);
            $productUrl = Mage::getUrl().$configurableProduct->getUrlPath().'?utm_source=email-refill&utm_medium=email&utm_campaign=email-refill';

            $singleProductId = $productModel->getIdBySku($sku); //load configurable product using sku
            $singleProduct = $productModel->load($singleProductId);

            $generic_name = $configurableProduct->getData('generic_name');
            $configurable_attribute = $configurableProduct->getData('configurable_attribute');
             $helperdata = Mage::helper("refillreminder");

            $newdata = $helperdata->getParentproduct($sku);
            // print_r($newdata[0]);//exit;
            // echo '<pre>';
            $parentProductCollection = $productModel->load($newdata[0]);
            // print_r($parentProductCollection->getData());
            // exit;
            $tbl.="<tr>";
            $tbl.='<td style="width:140px; padding:5px"><a href="'.$productUrl.'"><img width="100px" src="'.$parentProductCollection->getImageUrl().'"/></a></td>';
            $tbl.='<td style="padding:10px"><p><a style="font-weight: bold; font-size: 20px; color: #719102;" href="'.$productUrl.'">'.$parentProductCollection->getName().' '.$configurable_attribute.'</a></p>';
            $tbl.='<p>'.$skuAndPackSize[1].' '.$configurableProduct->getAttributeText('pharmaceutical_form').' -<span style="font-size: 18px; font-weight: bold;"> US$'.sprintf("%.2f",$singleProduct->getPrice()).'</span></p>';
            if($parentProductCollection->getDescription()) {
             // $tbl.='<p>'.substr($parentProductCollection->getDescription(), 0, 200).'...</p>';
            }
            $tbl.='<p><a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.$productUrl.'">View Details</a> <a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.Mage::getBaseUrl().'refillreminder/view/">Edit Refill</a></p>';
            $tbl.='</td>';
            $tbl.="</tr>";
        }

       // $customer_email = $allReminderData['customer_email'];
       // $customer_email = $allReminderData['customer_email'];
       $customer = Mage::getModel("customer/customer");
       // $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
       $customer->loadByEmail($mail); //load customer by email id
       // print_r($customer->getData('firstname'));
       // exit;
       if($customer->getData('firstname')){
         $fname = $customer->getData('firstname');
       }else{
         $fname = 'Customer';
       }
       // echo $fname;

       // exit;

          // $email = $mail;
          // $email = $allReminderData['customer_email'];
          $status = 1;
          $eid = 234262; //refill reminder 2 234262
          // $fname = $post['name'];
          // $message = $tbl;
          // $producttitle = $product_s;
          $productreminder = Mage::helper('refillreminder');
          // $orderreminder->sendMailReminder($tbl);
          $productreminder->sendMailReminder($mail,$status,$eid,$fname,$tbl,$product_s);
          //$productreminder->sendMailReminder('manoj.chowrasiya@iksula.com',$status,$eid,$fname,$message,$product_s);
          $tbl='';
        }

    }

    public function sendOrderReminder() {

        $model = Mage::getModel('refillreminder/orderreminder'); //model to process reminder checking data

        $collection = $model->getCollection();
        //print_r($collection->getData());
        $collection->getSelect()->where("remind_flag = 1 AND mail_sent_count< 1 AND reminder_days = DATEDIFF(DATE(NOW()),DATE(last_mail_sent))");
        // print_r($collection->getData());
        // exit;
        if($collection->getSize()) {
            $userArray = array();
            foreach($collection->getData() as $allReminderData) {

                //print_r($allReminderData);
                //$userArray[$allReminderData['customer_email']][] = $allReminderData['product_sku'];
                $itemtotaldetails =array();
                $productArray = explode(',', $allReminderData['product_sku']);
                $orderObj = Mage::getModel('sales/order')->loadByIncrementId($allReminderData['order_id']);
                // echo '<pre>';
                // print_r($orderObj->getData('customer_firstname'));
                // exit;
                $name_of_customer = $orderObj->getData('customer_firstname');
                  foreach ($orderObj->getAllItems() as $value) {
                  // print_r($value->getData('qty_ordered'));
                  $itemsku = $value->getData('sku');
                  $itemprice = $value->getData('price');
                  $qty_ordered = $value->getData('qty_ordered');
                  $itemtotaldetails[] = $itemsku.'_'.$qty_ordered.'_'.$itemprice.'_'.$name_of_customer;
                }
                foreach($itemtotaldetails as $proArray) {
                  $userArray[$allReminderData['customer_email']][] = $proArray;
                }

                $data = array('last_mail_sent' => date('Y-m-d H:i:s', time()), 'next_mail_on' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', time()). '+ '.$allReminderData['reminder_days'].' days ')), 'mail_sent_count' => mail_sent_count+1); //primary key of reminder table which will update
                $UpdateModel = $model->load()->addData($data);
                try {
                   $UpdateModel->setId($allReminderData['order_inc_id'])->save();
                    echo "Data has been updated successfully.";
                } catch (Exception $e){
                    echo $e->getMessage();
                }
            }

        }

        // print_r($userArray);
        // exit;

        $productModel = Mage::getModel('catalog/product');
        foreach($userArray as $mail=>$skus) {
          //$tbl = '<table border="1" style="border-collapse:collapse;"><tr><td style="padding:10px">Product SKU</td><td style="padding:10px">Pack Size</td><td style="padding:10px">Product Name</td><td style="padding:10px">Product Image</td></tr>';
          $tbl="";
          $skuCount = count($skus);
          $product_s = "Your Products";
          if($skuCount==1) {
            $product_s = "Your Product";
          }

          $orderprice = 0;
          $totalprice =0;
          foreach($skus as $sku) {
            // $orderprice = 0;
            $skuAndPackSize = explode("_", $sku);
            $ordersku = $skuAndPackSize[0];
            $itemqty = $skuAndPackSize[1];
            $unitprice = $skuAndPackSize[2];
            $name_of_customer = $skuAndPackSize[3];
            $totalprice = $itemqty*$unitprice;
            $orderprice += $totalprice;
            $skuAndPackSize = explode("-", $ordersku);

            // $skuAndPackSize = explode("-", $sku);
            $configurableProductId = $productModel->getIdBySku($skuAndPackSize[0]); //load configurable product using sku
            $configurableProduct = $productModel->load($configurableProductId);
            $productUrl = Mage::getUrl().$configurableProduct->getUrlPath().'?utm_source=email-order&utm_medium=email&utm_campaign=email-order';

            $singleProductId = $productModel->getIdBySku($ordersku); //load configurable product using sku
            $singleProduct = $productModel->load($singleProductId);

            $generic_name = $configurableProduct->getData('generic_name');
            $configurable_attribute = $configurableProduct->getData('configurable_attribute');

            $helperdata = Mage::helper("refillreminder");
            // print_r($sku);//exit;
            $newdata = $helperdata->getParentproduct($ordersku);
            // print_r($newdata[0]);//exit;

            $parentProductCollection = $productModel->load($newdata[0]);

            $tbl.="<tr>";
            $tbl.='<td align="center" style="padding:12px;border: 1px solid #ccc;"><img width="100px" src="'.$configurableProduct->getImageUrl().'"/></td>';
            $tbl.='<td align="center" style="font-size:15px; color:#333333;border: 1px solid #ccc;">'.$parentProductCollection->getName().' '.$configurable_attribute.'</td>';
            $tbl.='<td align="center" style="font-size:15px; color:#333333;border: 1px solid #ccc;">'.$skuAndPackSize[1].' '.$configurableProduct->getAttributeText('pharmaceutical_form').'</td>';
            $tbl.='<td align="center" style="font-size:15px; color:#333333;border: 1px solid #ccc;">'.sprintf("%.2f",$itemqty).'</td>';
            $tbl.='<td align="center" style="font-size:15px; color:#333333;border: 1px solid #ccc;">'.'US$'.sprintf("%.2f",$singleProduct->getPrice()).'</td>';
            $tbl.='<td align="center" style="font-size:15px; color:#333333;border: 1px solid #ccc;">'.'US$'.sprintf("%.2f",$totalprice).'</td>';
            $tbl.="</tr>";

          }

          // $email = $allReminderData['customer_email'];
          $status = 1;
          $eid = 234544;
          $fname = ucfirst(strtolower($name_of_customer));
          // $fname = 'Customer';
          $message = $tbl;
          $producttitle = $product_s;
          $order_price = 'US$ '.sprintf("%.2f",$orderprice);
          $orderreminder = Mage::helper('refillreminder');
          $orderreminder->sendMailOrderReminder($mail,$status,$eid,$fname,$tbl,$product_s,$order_price);
          // $orderreminder->sendMailOrderReminder('manoj.chowrasiya@iksula.com',$status,$eid,$fname,$tbl,$product_s,$order_price);
          $tbl='';
        }

    }

    function RemoveReAction() {
        $arr = array(2, 3, 4, 6, 7, 10, 11353, 12969, 12970, 12976, 12982, 12980);
        Mage::register('isSecureArea', true);
        $review = Mage::getModel('review/review');
        $collection = $review->getProductCollection();
        $collection->getData();

        $review->appendSummary($collection);

        foreach($collection as $product) {
            $data = $product->getData();
            if(!in_array($data['review_id'], $arr)) {
                echo $data['review_id'];
                $review->setId($data['review_id']);
                $review->delete();
                //exit;
            }
        }
    }

    function cartReminderAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Refill Reminder"));
        $this->renderLayout();
    }

    function OrderReminderPostAction() {
        Mage::getSingleton('core/session')->setOrderReminder("Yes");
        //echo Mage::getSingleton('core/session')->getOrderReminder(); exit;
        $days = $this->getRequest()->getPost('remind_days');
        $custPhone = $this->getRequest()->getPost('txtphone');
        $orderIncrementId = $this->getRequest()->getPost('txtproduct_id');
        $customerMail = $this->getRequest()->getPost('txtmail');
        $refillModel = Mage::getModel('refillreminder/orderreminder'); //model to process reminder checking data
        $session= Mage::getSingleton('checkout/session');
        $productModel = Mage::getModel('catalog/product');

        #print_r($this->getRequest()->getPost()); exit;

        if(empty($days) || empty($custPhone) || empty($customerMail)) {
            echo "Error, please check data.";
            exit;
        }
        // $cartItems = $session->getQuote()->getAllItems();


        // foreach($cartItems as $item) {
        //     $allSkus[] = $item->getSku();
        //     $allSkunames[] = $item->getName();
        // }

        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        #######################################################################################
        $orderData = Mage::getModel('sales/order')->load($order->getData('entity_id'));

        #get all items
        $items = $orderData->getAllItems();
        $itemcount= count($items);
        $data = array();
        // $i=0;
        #loop for all order items
        foreach ($items as $itemId => $item)
        {
          $data['sku'][] = $item->getSku();
          $data['name'][] = $item->getName();
        }

        $allproductsku = implode(",",$data['sku']);
        $allproductname = implode(",",$data['name']);


        $collection = $refillModel->getCollection();
        $collection->addFieldToFilter('product_sku', array('in'=>$allSkus));
        $collection->addFieldToFilter('customer_email', $customerMail);
        $refillItems = $collection->getData();
        foreach($refillItems as $allReminderData) {
            $reminderIdofSku[$allReminderData['product_sku']] = $allReminderData['reminder_id'];
            $foundInReminder[] = $allReminderData['product_sku'];
        }

        #####################################################################

        $data = array('customer_email'=>$customerMail,
                    'product_sku'=>$allproductsku,
                    'reminder_days'=>$days,
                    'remind_flag'=>1,
                    'customer_telephone'=>$custPhone,
                    'comment'=>'',
                    'product_name'=>$allproductname,
                    'created_date'=>date('Y-m-d H:i:s', time()),
                    'modified_date'=>date('Y-m-d H:i:s', time()),
                    'last_mail_sent'=>date('Y-m-d H:i:s', time()),
                    'next_mail_on'=>date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', time()). '+ '.$days.' days ')),
                    'order_id'=>$orderIncrementId,
                    'order_total'=>$order->getData('grand_total'),
                    'status'=>1,
                    'mail_sent_count'=>0);

                try {
                    $refillModel->setData($data)->save();
                    Mage::getSingleton('core/session')->setLatestEntry($refillModel->getId());
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
        unset($reminderIdofSku);

        // echo "Success";
    }
    public function OrderEditAction() {
      echo "Hello"; exit;
    }
    public function saveAction()

      {// $block = $this->getLayout()->createBlock("core/template")->setTemplate("refillreminder/index.phtml");
      // echo $block->toHtml();
    $order_id = $this->getRequest()->getParam('order_id');  
    $name=$this->getRequest()->getParam('name');
    $mail=$this->getRequest()->getParam('txtmail');
    $phone=$this->getRequest()->getParam('txtphone');
    //$ProductId = $this->getRequest()->getPost('txtproduct_id');
    $Days = $this->getRequest()->getPost('remind_days');
    //var_dump($Days);die;


    $model = Mage::getModel('refillreminder/refillreminder');


       $data = array('customer_email'=>$mail,
                    'customer_name'=>$name,
                    'reminder_days'=>$Days,
                    'order_Id'=>$order_id,
                    'remind_flag'=>1,
                    'customer_telephone'=>$phone,
                    'customer_telephone'=>$phone,
                    );
            try {
                    $model->setData($data)->save();
                   // print_r( $model->setData($data)->save());die;
                  
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                
    }
}
