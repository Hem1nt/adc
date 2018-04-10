<?php
class Manoj_Abandoned_Helper_Data extends Mage_Core_Helper_Abstract
{

  /*public function Synchronizeffff(){

    $collection = Mage::getResourceModel('reports/quote_collection');
    $collection->prepareForAbandonedReport(array(1));
    $collection->load();
    $collection->addOrder('updated_at','asc');
    // echo '<pre>';
    // print_r($collection->getSelect());
    // exit;
    $items = Mage::getModel('checkout/cart');
    $salesQuoteItemObj = Mage::getModel('sales/quote_item');
    $abandonedcart = Mage::getModel('abandoned/abandoned');
    $quote_collectionArray = array();
    $quote_ItemArray = array();

    foreach ($collection as $quote) {
      $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
      $quote['email'] = $quote->getData('customer_email');
      $quote['updated_at'] = $quote->getData('updated_at');
      $abandonedcartCollection->addFieldToFilter('email_id',$quote['email']);
      $quotedetails = $abandonedcartCollection->getData();
      if(count($quotedetails)>0){
        foreach ($quotedetails as $quotevalue) {
          if($quotevalue['update_time']!=$quote['updated_at']){
           $abundantcart_id = $quotevalue['abandoned_cart_id']; 
           $this->updateAbandonedCart($quote,$abundantcart_id);
                }//end of inner if
            }//end of inner foreach            
          }//end of if
          else{
            $this->saveAbandonedCart($quote);
          }
        }

      }*/

      public function synchCart(){
        //echo date('Y-m-d H:i:s')."=========";
       $last =date('Y-m-d H:i:s');
       $first =date('Y-m-d H:i:s', strtotime('-1 hour'));



       $collection = Mage::getResourceModel('reports/quote_collection');
       $collection->addFieldToFilter('items_count', array('neq' => '0'))
       ->addFieldToFilter('main_table.is_active', '1')
       ->addFieldToFilter('main_table.customer_email', array('neq' => ''))
       ->addFieldToFilter('main_table.updated_at', array('to' => $last,'from' => $first))
       ->addOrder('updated_at','desc');

       $items = Mage::getModel('checkout/cart');
       $salesQuoteItemObj = Mage::getModel('sales/quote_item');
       $abandonedcart = Mage::getModel('abandoned/abandoned');
       $quote_collectionArray = array();
       $quote_ItemArray = array();
       foreach ($collection->getData() as $quote) {
        // echo "<pre>"; print_r($quote); exit();
        $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();

        $abandonedcartCollection->addFieldToFilter('email_id',$quote['customer_email']);
        $quotedetails = $abandonedcartCollection->getData();

        if(count($quotedetails)>0){
          foreach ($quotedetails as $quotevalue) {
            if($quotevalue['update_time']!=$quote['updated_at']){
              $newcollection = Mage::getResourceModel('reports/quote_collection');
              $newcollection->addFieldToFilter('customer_email', $quote['customer_email']);
                // ->addOrder($quote['updated_at'],'desc');
              $customerQuoteCollection = $newcollection->setOrder('updated_at', 'DESC');
              if(count($customerQuoteCollection->getData())>1){
               $abundantcart_id = $quotevalue['abandoned_cart_id']; 
               $this->updateMultipleAbandonedCart($quote,$abundantcart_id);
             }else{
              $abundantcart_id = $quotevalue['abandoned_cart_id']; 
              $this->updateAbandonedCart($quote,$abundantcart_id);
            }                
              }//end of inner if
            }
          //end of inner foreach            
        }else{//end of if
          $this->saveAbandonedCart($quote);

        }

      }
    }

    public function updateMultipleAbandonedCart($quote,$abundantcart_id){
        //UPDATE 
      Mage::log($quote['customer_email'].'---------------upadte multiple',null,'multipleupdate.log');
      $abandonedcart = Mage::getModel('abandoned/abandoned');
      $quote_collectionArray = array();
      $quote_ItemArray = array();
      $salesQuoteItemObj = Mage::getSingleton('sales/quote_item');
      $price = 0;
      $discount_amount = 0;
      $totalsingleprice = 0;
      $salesQuoteItem = $salesQuoteItemObj->getCollection()->addFieldToFilter('quote_id', $quote['entity_id'])->getData();
      foreach ($salesQuoteItem as $items) {
        $price += $items['price'];
        $discount_amount += $items['discount_amount'];
        $qty = (int)$items['qty'];
        $quote_ItemArray[]=$items['product_id'].'_'.$qty;
        $totalsingleprice += $items['price']*$items['qty'];
      }
      $prod_price = sprintf("%.2f",$price);
      $discount_amount = sprintf("%.2f",$discount_amount);
      if(!empty($quote_ItemArray)){
        // $subtotal = round(($totalsingleprice - $discount_amount),2);
       $subtotal = round((($price*$qty) - $discount_amount),2);
       $loadData = $abandonedcart->load($abundantcart_id);
       $quote_collectionArray['email_id'] = $quote['customer_email'];
       $quote_collectionArray['quote_id'] = $quote['entity_id'];
       $quote_collectionArray['abandoned_page_capture'] = $quote['abandoned_page_capture'];
       $quote_collectionArray['created_time'] = $quote['created_at'];
       $quote_collectionArray['update_time'] = $quote['updated_at'];
       $quote_collectionArray['product_ids'] =implode(',', $quote_ItemArray); 
       $quote_collectionArray['subtotal'] = $subtotal;
       $loadDatahh = $loadData->addData($quote_collectionArray)->save();
     }

   }

   public function updateAbandonedCart($quote,$abundantcart_id){
        //UPDATE 
    Mage::log($quote['customer_email'].'---------------upadte send',null,'update.log');
    $abandonedcart = Mage::getModel('abandoned/abandoned');
    $quote_collectionArray = array();
    $quote_ItemArray = array();
    $salesQuoteItemObj = Mage::getSingleton('sales/quote_item');
    $price = 0;
    $discount_amount = 0;
    $totalsingleprice = 0;
    $salesQuoteItem = $salesQuoteItemObj->getCollection()->addFieldToFilter('quote_id', $quote['entity_id'])->getData();
    foreach ($salesQuoteItem as $items) {
      $price += $items['price'];
      $discount_amount += $items['discount_amount'];
      $qty = (int)$items['qty'];
      $quote_ItemArray[]=$items['product_id'].'_'.$qty;
      $totalsingleprice += $items['price']*$items['qty'];
    }
    $prod_price = sprintf("%.2f",$price);
    $discount_amount = sprintf("%.2f",$discount_amount);
    if(!empty($quote_ItemArray)){
        // $subtotal = round(($totalsingleprice - $discount_amount),2);
     $subtotal = round((($price*$qty) - $discount_amount),2);
     $loadData = $abandonedcart->load($abundantcart_id);
     $quote_collectionArray['email_id'] = $quote['customer_email'];
     $quote_collectionArray['quote_id'] = $quote['entity_id'];
     $quote_collectionArray['abandoned_page_capture'] = $quote['abandoned_page_capture'];
     $quote_collectionArray['is_email_send'] = 0;
     $quote_collectionArray['is_purchase'] = 0;
     $quote_collectionArray['created_time'] = $quote['created_at'];
     $quote_collectionArray['update_time'] = $quote['updated_at'];
     $quote_collectionArray['product_ids'] =implode(',', $quote_ItemArray); 
     $quote_collectionArray['subtotal'] = $subtotal;
     $loadDatahh = $loadData->addData($quote_collectionArray)->save();
   }

 }


 public function saveAbandonedCart($quote){
  Mage::log($quote['customer_email'].'-----------------save send',null,'save.log');
  $quote_collectionArray = array();
  $quote_ItemArray = array();
  $salesQuoteItemObj = Mage::getSingleton('sales/quote_item');      
  $salesQuoteItem = $salesQuoteItemObj->getCollection()->addFieldToFilter('quote_id', $quote['entity_id'])->getData();
  $price = 0;
  $discount_amount = 0;
  $totalsingleprice = '';
  foreach ($salesQuoteItem as $items) {
    $qty = (int)$items['qty'];
    $price += $items['price'];
    $discount_amount += $items['discount_amount'];
    $quote_ItemArray[]=$items['product_id'].'_'.$qty;
    $totalsingleprice += $items['price']*$items['qty'];
  }     

  if(!empty($quote_ItemArray)){
    $subtotal = round((($price*$qty) - $discount_amount),2);
    $quote_collectionArray['subtotal'] = $subtotal;
    $quote_collectionArray['email_id'] = $quote['customer_email'];
    $quote_collectionArray['quote_id'] = $quote['entity_id'];
    $quote_collectionArray['abandoned_page_capture'] = $quote['abandoned_page_capture'];
    $quote_collectionArray['is_email_send'] = 0;
    $quote_collectionArray['is_purchase'] = 0;
    $quote_collectionArray['created_time'] = $quote['created_at'];
    $quote_collectionArray['update_time'] = $quote['updated_at'];
    $quote_collectionArray['product_ids'] =implode(',', $quote_ItemArray);  
    $quote_collectionArray['subtotal'] = $subtotal;

    Mage::getSingleton('abandoned/abandoned')->setData($quote_collectionArray)->save();
  }

}

public function sendemailAbandonedCart($cust_email_id){

  $productmodelobj = Mage::getModel('catalog/product');

  $nextdate =date('l, jS F Y', strtotime('+1 week'));
  $to = $cust_email_id;
  $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
  $dataCollection = $abandonedcartCollection->addFieldToFilter('email_id',$cust_email_id)->getData();
  $customer_email = $cust_email_id;
  $customer = Mage::getModel("customer/customer");
  $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($customer_email); //load customer by email id
        $customername = ucfirst(strtolower($customer->getData('firstname')));
        $adclogourl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'adclogomailer.jpg';
        $welcomemsg = "Dear ".$customername.','.'<br/><br/>
        Thank you for your recent visit to AllDayChemist!<br/><br/>
        <b>We have noticed that you have an unfinished purchase and we have saved it for you !</b><br/><br<br/><br/>Here is the order currently saved in your cart<br/><br/>';
        $htmlnew = '';
        foreach ($dataCollection as $prodcuthtml) {

         $subtotal = $prodcuthtml['subtotal'];
         $includingsubtotal = 0;
         //$shippingcost = 25;
         $shippingcost = Mage::getStoreConfig('carriers/abandoned/abandoned_price');
         $subtotalwithshipping = $subtotal + $shippingcost;
         $ids = explode(',', $prodcuthtml['product_ids']);
         $baseUrl = Mage::getBaseUrl().'abandoned/index/cartreturn?key='.base64_encode($to).'?utm_source=email-cart&utm_medium=email-cart&utm_campaign=email';
         $adcimageurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'abandoned.jpg';

         foreach ($ids as $value) {
          $ids = explode('_', $value);
          $orderedaty = $ids[1];  
          $_product = $productmodelobj->load($ids[0]);
          /* pack size start */
          $pro_pack_size = $_product->getData('pack_size');
          $pro_pharmaceutical_form = $_product->getData('pharmaceutical_form');
          $attr = $productmodelobj->getResource()->getAttribute("pack_size");
          $pharmaceutical_form_attr = $productmodelobj->getResource()->getAttribute("pharmaceutical_form");

          if ($attr->usesSource()) {
           $pro_pack_size_label = $attr->getSource()->getOptionText($pro_pack_size);
           $pharmaceutical_form_label = $pharmaceutical_form_attr->getSource()->getOptionText($pro_pharmaceutical_form);
         }
         $simple_pack_size = $pro_pack_size_label.' '.$pharmaceutical_form_label;

         /*pack size end*/
         $pro_price = $_product->getPrice();
         $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
         $parentobj = $productmodelobj->load($parentIds[0]);
         $pro_strength = $parentobj->getData('configurable_attribute');
         $pro_product_name = $parentobj->getName();
         $pro_special_price = $parentobj->getData('special_price');
         $pro_url_path = $parentobj->getData('url_path').'?utm_source=email-cart&utm_medium=email-cart&utm_campaign=email';
         $pro_small_image = $parentobj->getData('small_image');

         $imageurlpath = Mage::helper('catalog/image')->init($parentobj, 'small_image')->resize(150);
         $productimagehtml = "<img src='".$imageurlpath."' />";
         $productnamehtml = '<h3>'.$pro_product_name.' '.$pro_strength.'</h3>';
         $productpricehtml = sprintf("%.2f",$pro_price);
         $totalprice = sprintf("%.2f",$pro_price*$orderedaty);
         $includingsubtotal += $totalprice;
         $html ='<tr>
                  <td align="center" valign="top" style="padding:14px 18px 16px 18px; background-color:#fff;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="113" style=" border-right:2px solid #cccccc; border-left:2px solid #cccccc; ">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td>
                                  <img src="<?php echo $productimagehtml; ?>" width="129" alt="<?php echo $productnamehtml?>" style="display:inline-block; border:none;" >
                              </td>
                            </tr>
                            <tr>
                              <td align="center" style="font-family:'Trebuchet MS'; font-size:15px; color:#666666; padding:0 0px;">
                                <?php echo $productnamehtml; ?>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td width="113" align="center" style="font-family:'Trebuchet MS'; font-size:15px; color:#666666; padding:0 0px; border-right:2px solid #cccccc;">
                          <?php echo $simple_pack_size; ?>
                        </td>
                        <td width="113" align="center" style="font-family:'Trebuchet MS'; font-size:15px; color:#666666; padding:0 0px; border-right:2px solid #cccccc;">
                          <?php echo "US$ ". $productpricehtml;?>
                        </td>
                        <td width="113" align="center" style="font-family:'Trebuchet MS'; font-size:15px; color:#666666; padding:0 0px; border-right:2px solid #cccccc;">
                          <?php echo $orderedaty;?>
                        </td>
                        <td width="113" align="center" style="font-family:'Trebuchet MS'; font-size:15px; color:#666666; padding:0 0px; border-right:2px solid #cccccc;">
                           <?php echo "US$ ". $totalprice;?>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                    <td align="center" valign="top" width="1" height="1" style="padding:0px 0px 0px 0px;">
                        <table width="565" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                              <td style="border-bottom:1px solid #666666; " ></td>
                          </tr>
                        </table>
                    </td>
                </tr>';
         $htmlnew.= $html;
       }
     }
     $subtotalwithshipping = $includingsubtotal +$shippingcost;
     $baseUrl = Mage::getBaseUrl().'abandoned/index/cartreturn?key='.base64_encode($to).'?utm_source=email-cart&utm_medium=email-cart&utm_campaign=email';
     $message = $htmlnew;
     $status = '';
     $this->sendMail($cust_email_id,$status,'233703',$customername,$message,$customername,$subtotalwithshipping,$baseUrl,$nextdate);
     // $this->sendMail('manoj.chowrasiya@iksula.com',$status,'233703',$customername.$cust_email_id,$message,$customername,$subtotalwithshipping,$baseUrl,$nextdate);
   }

   

   public function cartreturn()
   {
    $email = Mage::app()->getRequest()->getParam('id'); 
    $customer = Mage::getModel('customer/customer');
    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
    $customer->loadByEmail(trim($email));
    Mage::getSingleton('customer/session')->loginById($customer->getId());
    $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
  }

  public function abandonedcartemail($customer_email){
      // echo $customer_email;exit;
   $abandonedcollection = Mage::getModel('abandoned/abandoned');
   $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
   $abandonedcartCollection->addFieldToFilter('email_id',$customer_email);
   $dataCollection = $abandonedcartCollection->addFieldToFilter('is_email_send','0')->getData();

  // echo "<pre>"; print_r($dataCollection); exit();

   foreach ($dataCollection as $value) {
    $cartid = $value['abandoned_cart_id'];
     $updated_time = $value['update_time'];
     $page_capture = $value['abandoned_page_capture'];
   }
   //exit();
   if(count($dataCollection)>0){   

    $cart_responce = $this->checkcartinfo($customer_email); 
     // exit;
    if($cart_responce){
 
      if($page_capture == 'cartpage'){
      
          $abandonedcollection->load($cartid);
        $abandonedcollection->setData('is_email_send','1')->save();
        $this->sendemailAbandonedCart($customer_email);

      }

      if($page_capture == 'billing_medicalpage'){
          $abandonedcollection->load($cartid);
       $abandonedcollection->setData('is_email_send','1')->save();
       $this->sendemailAbandonedCart($customer_email);


              
     }

     if($page_capture == 'confirmaddress_paymentpage'){
             $abandonedcollection->load($cartid);
       $abandonedcollection->setData('is_email_send','1')->save();
       $this->sendemailAbandonedCart($customer_email);
     

       }

   }
       // print_r($abandonedcollection->getData());
 }else{
  echo 'mail not send';
} 

}

public function checkcartinfo($customer_email){
     //echo $customer_email;
    // exit;

  $abandonedcollection = Mage::getModel('abandoned/abandoned');
  $collection = Mage::getResourceModel('reports/quote_collection');
  $collection->prepareForAbandonedReport(array(1));

  $salesQuoteItem = $collection->addFieldToFilter('customer_email',$customer_email)->getData();

  //echo "<pre>"; print_r($salesQuoteItem); exit();

  if(count($salesQuoteItem)==0){
    $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
    $abandonedcartCollection->addFieldToFilter('email_id',$customer_email);
    $dataCollection = $abandonedcartCollection->getData();
    foreach ($dataCollection as $value) {
      $cartid = $value['abandoned_cart_id'];
    }
    if(count($dataCollection)>0){   
     // echo $customer_email;
    // exit;     
      $abandonedcollection->load($cartid);
      $abandonedcollection->delete();
    }
    return 0;
  }else{
    return 1;
  }

}



public function sendMail ($email,$status,$eid ,$fname,$message,$custname,$subtotalwithshipping,$shippingcost,$linktocart,$nextdate) 
{
  $this->curlRequest ($email,$status,$eid,$fname,$message,$custname,$subtotalwithshipping,$shippingcost,$linktocart,$nextdate);
}



public function curlRequest($email,$status,$eid,$fname,$message,$customername,$subtotalwithshipping,$shippingcost,$grandtotal,$linktocart,$nextdate){
  //echo "<textarea>".$message."</textarea>"; //exit; 
  Mage::log($email.'------------mail send',null,'abandonedmail.log');
  $login_cheetahmail_curi = Mage::getStoreConfig('general/cheetahmail/login');
  $login_param_name = Mage::getStoreConfig('general/cheetahmail/apiname');
  $login_param_cleartext = Mage::getStoreConfig('general/cheetahmail/apipassword');
  $login_ebmtrigger_uri = Mage::getStoreConfig('general/cheetahmail/api');
  $login_aid = Mage::getStoreConfig('general/cheetahmail/aid');
  $login_eid = Mage::getStoreConfig('general/cheetahmail/eid');//exit;

    echo "crulReuest Function: OK<br>";

    $login_uri = $login_cheetahmail_curi;

    $login_params = array(
     "name=".urlencode($login_param_name),
     "cleartext=".urlencode($login_param_cleartext)
     );
      //url-ify the POST parameters
    $param_string = implode('&', $login_params);
      //open a cURL connection
    $curl = curl_init($login_uri);
      //if cURL connection successfully opened...
    if ($curl){
        //set request method=POST
     curl_setopt($curl, CURLOPT_POST, true);
        //set cURL POST parameters
     curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
        //tell cURL to return server response for variable storage
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //store cookie in specified file
     curl_setopt($curl, CURLOPT_COOKIEJAR, "C:\\my_cookie_file");
        //execute login1 POST request and store server response
     $response = trim(curl_exec($curl));
        //if there was a cURL error, display it
     if (curl_errno($curl)) {
      echo "error: ".curl_error($curl);
    }
        //close the cURL connection
    curl_close($curl);
        //display server response
        //echo "login server response: $response\n";
    echo "login server response: $response\n<br>";
        //if authentication successful...
    if ($response == "OK"){
      $ebmtrigger_uri = $login_ebmtrigger_uri;
      $embtrigger_params = array(
       "aid=".$login_aid,
       "email=".$email,
       "eid=".$login_eid,
       "req=1",
       "FNAME=".$customername,
       "RETURNTOCART=".$linktocart,
       "SUBTOTAL=US$ ".$subtotal,
       "CARTAMOUNT=US$ ".$subtotalwithshipping,
       "VALIDDATE=".$nextdate,
       "SHIPPINGCHARGE=US$ ".$shippingcost,
       "CARTDETAIL=".urlencode($message)
       );
      $param_string = implode('&', $embtrigger_params);
      $curl = curl_init($ebmtrigger_uri);
      if ($curl){
       curl_setopt($curl, CURLOPT_POST, true);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //specify location of cookie file
       curl_setopt($curl, CURLOPT_COOKIEFILE, "C:\\my_cookie_file");
       $response = trim(curl_exec($curl));

       if (curl_errno($curl)) {
        echo "error: ".curl_error($curl); 
      }
      curl_close($curl);
      echo "ebmtrigger server response: $response\n"; 

    }
  }
}
}

public function mailsend2(){
  // echo 'manoj';
  Mage::log(date(),null,'abondent_mailsend2_function.log');  
    /*$abandonedtime = Mage::getStoreConfig('general/setting/abandonedtime');
    $timeduration = Mage::getStoreConfig('general/setting/time');
    $abondent_minutes = $abandonedtime;
    $storetime = Mage::getModel('core/date')->timestamp(time());
    $currentdate = date('Y-m-d H:i:s',$storetime);
    if($timeduration==0){
       $datetime_from = date("Y-m-d H:i:s",strtotime("-$abondent_minutes days",$storetime));
    }
    else{
       $datetime_from = date("Y-m-d H:i:s",strtotime("-$abondent_minutes hours",$storetime));
     }*/
     $last =date('Y-m-d H:i:s');
     $first =date('Y-m-d H:i:s', strtotime('-1 hour'));



     $abandonedcollection = Mage::getModel('abandoned/abandoned')->getCollection()->distinct(true)->addFieldToFilter('is_email_send',0);
    // echo '<pre>';
     $abandonedcollection->addFieldToFilter('update_time', array('gteq' =>$first));
     $abandonedcollection->addFieldToFilter('update_time', array('lteq' => $last));
    //print_r($abandonedcollection->getData());exit;

     $aband_mail_array =array();
     Mage::log($abandonedcollection->getData(),null,'mailsendlog_verify_1.log'); 

     foreach ($abandonedcollection as $emailsend) {  
      Mage::log($emailsend->getData(),null,'mailsendlog_verify_2.log');  

      if(!in_array($emailsend['email_id'],$aband_mail_array)){
       $aband_mail_array[]=$emailsend['email_id']; 
       $this->abandonedcartemail($emailsend['email_id']);
     }else{
       Mage::log($emailsend['email_id'].'------------ mail not send',null,'dupliacteemailid.log'); 
     }
   }
   // exit;
 }

/*public function testmailsend(){
    // echo 'maklklnoj';exit;
 // $this->Synchronize();  
  //echo $email = 'manojiksula@gmail.com';   
  $this->abandonedcartemail($email);
  $abandonedtime = Mage::getStoreConfig('general/setting/abandonedtime');
  $timeduration = Mage::getStoreConfig('general/setting/time');
  $abondent_minutes = $abandonedtime;
  $storetime = Mage::getModel('core/date')->timestamp(time());
  $currentdate = date('Y-m-d H:i:s',$storetime);
  $first =date('Y-m-d H:i:s', strtotime('-2 day'));
  $last =date('Y-m-d H:i:s', strtotime('-1 day'));
    //Mage::helper('core')->formatTime($time=null, $format='short', $showDate=true);
  if($timeduration==0){
   $datetime_from = date("Y-m-d H:i:s",strtotime("-$abondent_minutes days",$storetime));
 }
 else{
  $datetime_from = date("Y-m-d H:i:s",strtotime("-$abondent_minutes hours",$storetime));
}

$abandonedcollection = Mage::getModel('abandoned/abandoned')->getCollection()->addFieldToFilter('is_email_send',0);
    //->addFieldToFilter('is_purchase',0)
    // ->addFieldToFilter('update_time', array('lteq' => $datetime_from))->getData();
$abandonedcollection->addFieldToFilter('updated_at', array('gteq' =>$first));
$abandonedcollection->addFieldToFilter('updated_at', array('lteq' => $last));
print_r($abandonedcartemail->getData());
exit;
   // $this->abandonedcartemail($email);
foreach ($abandonedcollection as $emailsend) {    
    //call function which send email through chetaha mail
 //$this->abandonedcartemail($emailsend['email_id']);
}
}*/
}
