<?php
class Manoj_Abandoned_Helper_Data extends Mage_Core_Helper_Abstract
{

	 public function Test(){

      $collection = Mage::getResourceModel('reports/quote_collection');
      $collection->prepareForAbandonedReport(array(1));
      $collection->load();

      $items = Mage::getModel('checkout/cart');
      $salesQuoteItemObj = Mage::getModel('sales/quote_item');
      $abandonedcart = Mage::getModel('abandoned/abandoned');
      $quote_collectionArray = array();
      $quote_ItemArray = array();

      foreach ($collection->getData() as $quote) {
        // echo $quote['email'];
        $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
        // echo '<pre>';
      // print_r($abandonedcartCollection->getData());
        $abandonedcartCollection->addFieldToFilter('email_id',$quote['email']);
        $quotedetails = $abandonedcartCollection->getData();
        if(count($quotedetails)>0){
          foreach ($quotedetails as $quotevalue) {
              if($quotevalue['update_time']!=$quote['updated_at']){
                echo $abundantcart_id = $quotevalue['abandoned_cart_id']; 
                 // $loadData = $abandonedcart->load($abundantcart_id);
                // echo 'manoj'.$abundantcart_id.'$$$$$$$';exit;
                $this->updateAbandonedCart($quote,$abundantcart_id);
                  // echo 'updateAbandonedCart';exit;
              }//end of inner if
          }//end of inner foreach            
        }//end of if
        else{
          $this->saveAbandonedCart($quote);
           // echo 'saveAbandonedCart';exit;
        }
      }
     
    }


    public function saveAbandonedCart($quote){
      $quote_collectionArray = array();
      $quote_ItemArray = array();
      $salesQuoteItemObj = Mage::getSingleton('sales/quote_item');      
      $salesQuoteItem = $salesQuoteItemObj->getCollection()->addFieldToFilter('quote_id', $quote['entity_id'])->getData();
      // echo '<pre>';
      // print_r($salesQuoteItem);
      // exit;
      $price = 0;
      $discount_amount = 0;
      foreach ($salesQuoteItem as $items) {
        $qty = (int)$items['qty'];
        $price += $items['price'];
        $discount_amount += $items['discount_amount'];

        $quote_ItemArray[]=$items['product_id'].'_'.$qty;
      }     
          // echo $price.' '.$discount_amount;
       // exit;
     // $prod_price = sprintf("%.2f",$price);
      //$discount_amount = sprintf("%.2f",$discount_amount);
      // $subtotal = $price - $discount_amount;
      $subtotal = round(($price - $discount_amount),2);
      //$subtotal = sprintf("%.2f",$price) - sprintf("%.2f",$discount_amount);
      $quote_collectionArray['subtotal'] = $subtotal;
      $quote_collectionArray['email_id'] = $quote['email'];
      $quote_collectionArray['quote_id'] = $quote['entity_id'];
      $quote_collectionArray['is_email_send'] = 0;
      $quote_collectionArray['is_purchase'] = 0;
      $quote_collectionArray['created_time'] = $quote['created_at'];
      $quote_collectionArray['update_time'] = $quote['updated_at'];
      $quote_collectionArray['product_ids'] =implode(',', $quote_ItemArray);  
      echo $quote_collectionArray['subtotal'] = $subtotal;

      Mage::getSingleton('abandoned/abandoned')->setData($quote_collectionArray)->save();
    }

    public function sendemailAbandonedCart($cust_email_id){
    	//echo 'manoi';
      //$cust_email_id = 'manoj.chowrasiya@iksula.com'; 
      $productmodelobj = Mage::getModel('catalog/product');
      // $to = "manoj.chowrasiya@iksula.com";
      // $baseUrl
      // echo $date = strtotime("+7 day", date("l, jS F Y"));
      $nextdate =date('l, jS F Y', strtotime('+1 week'));
      $to = $cust_email_id;
      $subject = "Test email";
      $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
      $dataCollection = $abandonedcartCollection->addFieldToFilter('email_id',$cust_email_id)->getData();
    	$customer_email = $cust_email_id;
     $customer = Mage::getModel("customer/customer");
     $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
     $customer->loadByEmail($customer_email); //load customer by email id
     $customername = ucfirst($customer->getData('firstname'));
     $adclogourl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'adclogomailer.jpg';
      $welcomemsg = "Dear ".$customername.','.'<br/><br/>
      Thank you for your recent visit to AllDayChemist!<br/><br/>
      <b>We have noticed that you have an unfinished purchase and we have saved it for you !</b><br/><br<br/><br/>Here is the order currently saved in your cart<br/><br/>';

      // print($dataCollection);
      foreach ($dataCollection as $prodcuthtml) {
        // print_r($ids);
       $subtotal = $prodcuthtml['subtotal'];
       $shippingcost = 25;
       $subtotalwithshipping = $subtotal + $shippingcost;
        // exit;
        $ids = explode(',', $prodcuthtml['product_ids']);
        // echo '<br/>';
        $baseUrl = Mage::getBaseUrl().'abandoned/index/cartreturn?key='.base64_encode($to);
        $adcimageurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'abandoned.jpg';

        foreach ($ids as $value) {
          $ids = explode('_', $value);

          $orderedaty = $ids[1];  
          // echo '<pre>';
          $_product = $productmodelobj->load($ids[0]);
          // print_r($_product->getData());
          $pro_price = $_product->getPrice();
          $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
          $parentobj = $productmodelobj->load($parentIds[0]);
          $pro_strength = $parentobj->getData('configurable_attribute');
          $pro_product_name = $parentobj->getName();
          $pro_special_price = $parentobj->getData('special_price');
          $pro_url_path = $parentobj->getData('url_path');
          $pro_small_image = $parentobj->getData('small_image');
            // print_r($parentobj->getData());
            // exit;

          $imageurlpath = Mage::helper('catalog/image')->init($parentobj, 'small_image')->resize(150);
          $productimagehtml = "<img src='".$imageurlpath."' />";
          $productnamehtml = '<h3>'.$pro_product_name.' '.$pro_strength.'</h3>';
          $productpricehtml = sprintf("%.2f",$pro_price);
          $totalprice = sprintf("%.2f",$pro_price*$orderedaty);

         // $html = "<tr style='border:1px solid black'><td style='border:1px solid'>".$productimagehtml." ".$productnamehtml."</td> <td style='border:1px solid'>".$productpricehtml."</td> <td style='border:1px solid'>".$orderedaty."</td> <td style='border:1px solid'>".$totalprice."</td></tr>";
        $html = "<tr style='border:1px solid black;color:#000;'>
        <td  style='font-size:1em;width:47%;border:1px solid #ccc;padding:3px 7px 2px 7px;font-size:1.1em;text-align:center;padding-top:5px;padding-bottom:4px;'><table><tr><td>".$productimagehtml."</td><td style='text-align: left;'> ".$productnamehtml."</td></tr></table></td> 
        <td  style='font-size:1em;border:1px solid #ccc;padding:3px 7px 2px 7px;font-size:1.1em;text-align:center;padding-top:5px;padding-bottom:4px;'>US$".$productpricehtml."</td> 
        <td  style='font-size:1em;border:1px solid #ccc;padding:3px 7px 2px 7px;font-size:1.1em;text-align:center;padding-top:5px;padding-bottom:4px;'>".$orderedaty."</td> 
       <td  style='font-size:1em;border:1px solid #ccc;padding:3px 7px 2px 7px;font-size:1.1em;text-align:center;padding-top:5px;padding-bottom:4px;'>US$".$totalprice."</td>
        </tr>";
          $htmlnew.= $html;
        }
     }
      $baseUrl = Mage::getBaseUrl().'abandoned/index/cartreturn?key='.base64_encode($to);
      $message = $htmlnew;
   
      // $abandonedcart = Mage::helper('abandonedcart');
      $this->sendMail($cust_email_id,$status,'233703','Manoj',$message,$customername,$subtotalwithshipping,$baseUrl,$nextdate);
      // exit;
    }

    public function updateAbandonedCart($quote,$abundantcart_id){
      //UPDATE 
      $abandonedcart = Mage::getModel('abandoned/abandoned');
      $quote_collectionArray = array();
      $quote_ItemArray = array();
      $salesQuoteItemObj = Mage::getSingleton('sales/quote_item');
      $price = 0;
      $discount_amount = 0;
      $salesQuoteItem = $salesQuoteItemObj->getCollection()->addFieldToFilter('quote_id', $quote['entity_id'])->getData();
      foreach ($salesQuoteItem as $items) {
        $price += $items['price'];
        $discount_amount += $items['discount_amount'];
        $qty = (int)$items['qty'];
        $quote_ItemArray[]=$items['product_id'].'_'.$qty;
      }
      $prod_price = sprintf("%.2f",$price);
      $discount_amount = sprintf("%.2f",$discount_amount);
      // $subtotal = $price - $discount_amount;
      $subtotal = round(($price - $discount_amount),2);
      $loadData = $abandonedcart->load($abundantcart_id);
      $quote_collectionArray['email_id'] = $quote['email'];
      $quote_collectionArray['quote_id'] = $quote['entity_id'];
      $quote_collectionArray['is_email_send'] = 0;
      $quote_collectionArray['is_purchase'] = 0;
      $quote_collectionArray['created_time'] = $quote['created_at'];
      $quote_collectionArray['update_time'] = $quote['updated_at'];
      $quote_collectionArray['product_ids'] =implode(',', $quote_ItemArray); 
      $quote_collectionArray['subtotal'] = $subtotal;
      $loadDatahh = $loadData->addData($quote_collectionArray)->save();

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
       //  $customer_email = "manoj.chowrasiya@iksula.com";
         $abandonedcollection = Mage::getModel('abandoned/abandoned');
         $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
         $abandonedcartCollection->addFieldToFilter('email_id',$customer_email);
         $dataCollection = $abandonedcartCollection->addFieldToFilter('is_email_send',0)->getData();
         foreach ($dataCollection as $value) {
           $cartid = $value['abandoned_cart_id'];
         }
         if(count($dataCollection)>0){   

           $cart_responce = $this->checkcartinfo($customer_email);   
          // exit;
           if($cart_responce){          
             $this->sendemailAbandonedCart($customer_email);
             $abandonedcollection->load($cartid);
             $abandonedcollection->setData('is_email_send',1)->save();
           }
           
         }
         else{
          echo 'mail not send';
        } 

    }

    public function checkcartinfo($customer_email){
      // $customer_email = "nilesh@sharklasers.com";
      $abandonedcollection = Mage::getModel('abandoned/abandoned');
      $collection = Mage::getResourceModel('reports/quote_collection');
      $collection->prepareForAbandonedReport(array(1));
      $salesQuoteItem = $collection->load()->addFieldToFilter('customer_email',$customer_email)->getData();
      if(count($salesQuoteItem)==0){
         $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
         $abandonedcartCollection->addFieldToFilter('email_id',$customer_email);
         $dataCollection = $abandonedcartCollection->getData();
         foreach ($dataCollection as $value) {
           $cartid = $value['abandoned_cart_id'];
         }
         if(count($dataCollection)>0){        
           $abandonedcollection->load($cartid);
           $abandonedcollection->delete();
         }
         return 0;
      }
      else{
        return 1;
      }


    }

    public function curlRequest($email,$status,$eid,$fname,$message,$customername,$grandtotal,$linktocart,$nextdate){
		echo "<textarea>".$message."</textarea>"; //exit; 
		echo "crulReuest Function: OK<br>";
		//set URI of login1 call
		$login_uri = "https://app.cheetahmail.com/api/login1";

		//set POST parameters for login1 call
		$login_params = array(
			"name=".urlencode("API_iKsula"),
			"cleartext=".urlencode("ik_Sula_CM")
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
				$ebmtrigger_uri = "https://ebm.cheetahmail.com/ebm/ebmtrigger1";
				$embtrigger_params = array(
					"aid=2094210672",
					"email=".$email,
					"eid=233703",
					"req=1",
          "FNAME=".$customername,
          "RETURNTOCART=".$linktocart,
          "CARTAMOUNT=US$ ".$grandtotal,
          "VALIDDATE=".$nextdate,
					"CARTDETAIL=".urlencode($message)
					//"CARTAMOUNT=".$fname
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
	
	public function sendMail ($email,$status,$eid = '233703',$fname,$message,$custname,$grandtotal,$linktocart,$nextdate) 
	{
		$this->curlRequest ($email,$status,$eid,$fname,$message,$custname,$grandtotal,$linktocart,$nextdate);
	}

	public function mailsend(){
    // echo '<br/>';
      // $this->Test();
         // echo '<pre>';
       $abondent_minutes = 45;
       $currentdate = date('Y-m-d H:m:i');
       // echo '<br/>';
       // $email = 'manoj.chowrasiya@iksula.com';
       $datetime_from = date("Y-m-d H:m:i",strtotime("-$abondent_minutes minutes",time()));
       $abandonedcollection = Mage::getModel('abandoned/abandoned')->getCollection()
       ->addFieldToFilter('is_email_send',0)
       ->addFieldToFilter('update_time', array('lteq' => $datetime_from))->getData();
      // $this->abandonedcartemail($email);
      // exit;
       foreach ($abandonedcollection as $emailsend) {
        //    print_r($emailsend['email_id']);
            // echo '<br/>';
            //call function which send email through chetaha mail
         $this->abandonedcartemail($emailsend['email_id']);
       }
     //  exit;
   
	}
}
	 