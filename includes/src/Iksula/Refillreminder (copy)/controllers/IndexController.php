<?php
class Iksula_Refillreminder_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction() {
      $this->loadLayout();
      $this->getLayout()->getBlock("head")->setTitle($this->__("Refill Reminder"));
      $this->renderLayout();
      /*$block = $this->getLayout()->createBlock("core/template")->setTemplate("refillreminder/index.phtml");
      echo $block->toHtml();*/
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
        //print_r($data);
        try {
          $model->setData($data)->save();
          $templateId = 32;
          $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
          $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name

          $storeId = Mage::app()->getStore()->getId(); 

          $sender = array('name'  => $from_name, 'email' => $from_email);

          $mailTemplate = Mage::getModel('core/email_template')->load($templateId);
          $translate  = Mage::getSingleton('core/translate');
          $mailTemplate->sendTransactional($templateId, $sender, $CustomerMail, $CustomerMail, "", $storeId);

          if (!$mailTemplate->getSentSuccess()) {

            throw new Exception(); 
          } 
          $translate->setTranslateInline(true);

          echo " Product has been successfully added in Refill Reminder.";
        } catch (Exception $e) {
          echo $e->getMessage();
        }
      }
      //exit;
      

    }

    public function testAction() {
      $this->sendProductReminder();
      $this->sendOrderReminder();
    }

    public function sendProductReminder() {
        $model = Mage::getModel('refillreminder/refillreminder'); //model to process reminder checking data

        $collection = $model->getCollection();
        $collection->getSelect()->where("remind_flag = 1 AND reminder_days = ABS(DATEDIFF(DATE(NOW()), DATE(last_mail_sent)))");
        
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
        #echo "<pre>"; print_r($userArray); exit;
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
            $configurableProductId = $productModel->getIdBySku($skuAndPackSize[0]); //load configurable product using sku
            $configurableProduct = $productModel->load($configurableProductId);
            $productUrl = Mage::getUrl().$configurableProduct->getUrlPath();

            $singleProductId = $productModel->getIdBySku($sku); //load configurable product using sku
            $singleProduct = $productModel->load($singleProductId);

            
            //echo "<pre>"; print_r(get_class_methods($this));
            $tbl.="<tr>";
            $tbl.='<td style="width:140px; padding:5px"><a href="'.$productUrl.'"><img width="100px" src="'.$configurableProduct->getImageUrl().'"/></a></td>';
            $tbl.='<td style="padding:10px"><p><a href="'.$productUrl.'">'.$configurableProduct->getName().'</a></p>';
            $tbl.='<p>'.$skuAndPackSize[1].' '.$configurableProduct->getAttributeText('pharmaceutical_form').' - US$'.$singleProduct->getPrice().'</p>';
            if($configurableProduct->getDescription()) {
              $tbl.='<p>'.substr($configurableProduct->getDescription(), 0, 200).'...</p>';
            }
            $tbl.='<p><a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.$productUrl.'">View Details</a> <a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.Mage::getBaseUrl().'refillreminder/view/">Edit Refill</a></p>';
            $tbl.='</td>';
            $tbl.="</tr>";
          }
          
          
          try {
            $templateId = 32;
            $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
            $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name

            $storeId = Mage::app()->getStore()->getId(); 

            $sender = array('name'  => $from_name, 'email' => $from_email);

            $mailTemplate = Mage::getModel('core/email_template')->load($templateId);
            $translate  = Mage::getSingleton('core/translate');
            //$vars = array('querytype' => $post['querytype'], 'custname' => $post['name'], 'custemail' => $post['email'], 'telephone' => $post['telephone'], 'ordernumber' => $post['ordernumber'], 'comment' => $post['comment']);
            $vars = array('productTable'=>$tbl, 'productTitle'=>$product_s); //for replacing the variables in email with data
            
            $mailTemplate->sendTransactional($templateId, $sender, $mail, $CustomerMail, $vars, $storeId);

            if (!$mailTemplate->getSentSuccess()) { 
              throw new Exception(); 
            } 
            $translate->setTranslateInline(true);

            echo "Success";
          } catch (Exception $e) {
            echo $e->getMessage();
          }
        }
    }

    public function sendOrderReminder() {
        $model = Mage::getModel('refillreminder/orderreminder'); //model to process reminder checking data

        $collection = $model->getCollection();
        //print_r($collection->getData());
        $collection->getSelect()->where("remind_flag = 1 AND reminder_days = DATEDIFF(DATE(NOW()),DATE(last_mail_sent))");
    
        if($collection->getSize()) {
            $userArray = array();
            foreach($collection->getData() as $allReminderData) {
                //print_r($allReminderData);
                //$userArray[$allReminderData['customer_email']][] = $allReminderData['product_sku'];

                $productArray = explode(',', $allReminderData['product_sku']);

                foreach($productArray as $proArray) {
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
            $configurableProductId = $productModel->getIdBySku($skuAndPackSize[0]); //load configurable product using sku
            $configurableProduct = $productModel->load($configurableProductId);
            $productUrl = Mage::getUrl().$configurableProduct->getUrlPath();

            $singleProductId = $productModel->getIdBySku($sku); //load configurable product using sku
            $singleProduct = $productModel->load($singleProductId);

            
            //echo "<pre>"; print_r(get_class_methods($this));
            $tbl.="<tr>";
            $tbl.='<td style="width:140px; padding:5px"><a href="'.$productUrl.'"><img width="100px" src="'.$configurableProduct->getImageUrl().'"/></a></td>';
            $tbl.='<td style="padding:10px"><p><a href="'.$productUrl.'">'.$configurableProduct->getName().'</a></p>';
            $tbl.='<p>'.$skuAndPackSize[1].' '.$configurableProduct->getAttributeText('pharmaceutical_form').' - US$'.$singleProduct->getPrice().'</p>';
            if($configurableProduct->getDescription()) {
              $tbl.='<p>'.substr($configurableProduct->getDescription(), 0, 200).'...</p>';
            }
            $tbl.='<p><a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.$productUrl.'">View Details</a> <a style="background: #719102; border-radius: 5px; padding: 5px 10px; text-decoration: none; color: #fff;" href="'.Mage::getBaseUrl().'refillreminder/view/">Edit Refill</a></p>';
            $tbl.='</td>';
            $tbl.="</tr>";
          }
          
          
          try {
            $templateId = 32;
            $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
            $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name

            $storeId = Mage::app()->getStore()->getId(); 

            $sender = array('name'  => $from_name, 'email' => $from_email);

            $mailTemplate = Mage::getModel('core/email_template')->load($templateId);
            $translate  = Mage::getSingleton('core/translate');
            //$vars = array('querytype' => $post['querytype'], 'custname' => $post['name'], 'custemail' => $post['email'], 'telephone' => $post['telephone'], 'ordernumber' => $post['ordernumber'], 'comment' => $post['comment']);
            $vars = array('productTable'=>$tbl, 'productTitle'=>$product_s); //for replacing the variables in email with data
            
            $mailTemplate->sendTransactional($templateId, $sender, $mail, $CustomerMail, $vars, $storeId);

            if (!$mailTemplate->getSentSuccess()) { 
              throw new Exception(); 
            } 
            $translate->setTranslateInline(true);

            echo "Success";
          } catch (Exception $e) {
            echo $e->getMessage();
          }
        }
        $orderModel = Mage::getModel('refillreminder/orderreminder'); //model to process reminder checking data
        $collection = $orderModel->getCollection();
        exit;
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
        $customerMail = $this->getRequest()->getPost('txtmail');
        $refillModel = Mage::getModel('refillreminder/orderreminder'); //model to process reminder checking data
        $session= Mage::getSingleton('checkout/session');
        $productModel = Mage::getModel('catalog/product');

        #print_r($this->getRequest()->getPost()); exit;

        if(empty($days) || empty($custPhone) || empty($customerMail)) {
            echo "Error, please check data.";
            exit;
        }
        $cartItems = $session->getQuote()->getAllItems();

        foreach($cartItems as $item) {
            $allSkus[] = $item->getSku();
            $allSkunames[] = $item->getName();
        }

        $collection = $refillModel->getCollection();
        $collection->addFieldToFilter('product_sku', array('in'=>$allSkus));
        $collection->addFieldToFilter('customer_email', $customerMail);
        $refillItems = $collection->getData();
        foreach($refillItems as $allReminderData) {
            $reminderIdofSku[$allReminderData['product_sku']] = $allReminderData['reminder_id'];
            $foundInReminder[] = $allReminderData['product_sku'];
        }
        // print_r($foundInReminder);
        // print_r($allSkus);
        
            $allproductname =array(); 
            $allproductsku =array(); 
        foreach($cartItems as $item) {
            $productsku = $item->getSku();
            $splitSku = split("-", $productsku);
            $productData = $productModel->loadByAttribute('sku', $splitSku[0]);
            $allproductname[]=$productData->getName(); 
            $allproductsku[]=$productsku; 
            // if(in_array($productsku, $foundInReminder)) {
                
            //     /*$data = array('reminder_days'=>$days, 'last_mail_sent'=>NOW(), 'mail_sent_count'=>mail_sent_count+1); //primary key of reminder table which will update
            //     $UpdateRefill = $refillModel->load()->addData($data);
            //     try {
            //         $UpdateRefill->setId($reminderIdofSku[$productsku])->save();
            //         echo "Data has been updated successfully.";
            //     } catch (Exception $e){
            //         echo $e->getMessage();
            //     }*/
            // } else {
                
            //     $data = array('customer_email'=>$customerMail,
            //         'product_sku'=>$productsku,
            //         'reminder_days'=>$days,
            //         'remind_flag'=>0,
            //         'customer_telephone'=>$custPhone,
            //         'comment'=>'',
            //         'product_name'=>$productData->getName(),
            //         'created_date'=>'',
            //         'modified_date'=>'',
            //         'last_mail_sent'=>'',
            //         'mail_sent_count'=>0);
                    
            //     try {
            //         $refillModel->setData($data)->save();
            //     } catch (Exception $e) {
            //         echo $e->getMessage();
            //     }
            // }
            
        }

        $allproductsku = implode(',', $allproductsku);
        $allproductname = implode(',', $allproductname);

        #####################################################################

        $data = array('customer_email'=>$customerMail,
                    'product_sku'=>$allproductsku,
                    'reminder_days'=>$days,
                    'remind_flag'=>0,
                    'customer_telephone'=>$custPhone,
                    'comment'=>'',
                    'product_name'=>$allproductname,
                    'created_date'=>date('Y-m-d H:i:s', time()),
                    'modified_date'=>date('Y-m-d H:i:s', time()),
                    'last_mail_sent'=>date('Y-m-d H:i:s', time()),
                    'next_mail_on'=>date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', time()). '+ '.$days.' days ')),
                    'status'=>1,
                    'mail_sent_count'=>0);
                    
                try {
                    $refillModel->setData($data)->save();
                    Mage::getSingleton('core/session')->setLatestEntry($refillModel->getId()); 
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
        unset($reminderIdofSku);
        echo "Success";
    }
    public function OrderEditAction() {
      echo "Hello"; exit;
    }
}