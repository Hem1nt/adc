<?php
class Manoj_Abandoned_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Abandoned"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("abandoned", array(
                "label" => $this->__("Abandoned"),
                "title" => $this->__("Abandoned")
		   ));

      $this->renderLayout(); 
	  
    }

    public function TestAction(){

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
        $abandonedcartCollection->addFieldToFilter('email_id',$quote['email']);
        $quotedetails = $abandonedcartCollection->getData();
        if(count($quotedetails)>0){
          foreach ($quotedetails as $quotevalue) {
              if($quotevalue['update_time']!=$quote['updated_at']){
                 $abundantcart_id = $quotevalue['abundantcart_id']; 
                 $loadData = $abandonedcart->load($abundantcart_id);
                $this->updateAbandonedCartAction($quote,$abundantcart_id);
              }//end of inner if
          }//end of inner foreach            
        }//end of if
        else{
          $this->saveAbandonedCartAction($quote);
        }
      }
     
    }


    public function saveAbandonedCartAction($quote){
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
      $subtotal = $price - $discount_amount;
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

    public function sendemailAbandonedCartAction($cust_email_id){
      $cust_email_id = 'manoj.chowrasiya@iksula.com'; 
      $productmodelobj = Mage::getModel('catalog/product');
      $to = "manoj.chowrasiya@iksula.com";
      $subject = "Test email";
      $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
      $dataCollection = $abandonedcartCollection->addFieldToFilter('email_id',$cust_email_id)->getData();
    
      // print($dataCollection);
      foreach ($dataCollection as $prodcuthtml) {
        // print_r($ids);
        $ids = explode(',', $prodcuthtml['product_ids']);
        // echo '<br/>';
        $htmlnew = "<table style='border-collapse:collapse' >
        <tr style='border:1px solid black'>
         <th style='border:1px solid%%PARAM1%%'>Product Name</th>
         <th style='border:1px solid%%PARAM2%%'>Unit Price</th>
         <th style='border:1px solid%%PARAM3%%'>Qunatity</th> 
         <th style='border:1px solid%%PARAM4%%'>Subtotal</th>
        </tr>";
        foreach ($ids as $value) {
          $ids = explode('_', $value);

          $orderedaty = $ids[1];  
          $_product = $productmodelobj->load($ids[0]);
          $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
          $parentobj = $productmodelobj->load($parentIds[0]);
          $pro_strength = $parentobj->getData('configurable_attribute');
          $pro_product_name = $parentobj->getName();
          $pro_special_price = $parentobj->getData('special_price');
          $pro_url_path = $parentobj->getData('url_path');
          $pro_small_image = $parentobj->getData('small_image');
          $pro_price = $_product->getData('price');

          $imageurlpath = Mage::helper('catalog/image')->init($parentobj, 'small_image')->resize(150);
          $productimagehtml = "<img src='".$imageurlpath."' />";
          $productnamehtml = '<h3>'.$pro_product_name.' '.$pro_strength.'</h3>';
          $productpricehtml = $pro_price;
          $totalprice = $pro_price*$orderedaty;

         // $html = "<tr style='border:1px solid black'><td style='border:1px solid'>".$productimagehtml." ".$productnamehtml."</td> <td style='border:1px solid'>".$productpricehtml."</td> <td style='border:1px solid'>".$orderedaty."</td> <td style='border:1px solid'>".$totalprice."</td></tr>";
        $html = "<tr style='border:1px solid black;color:red;'><td style='border:1px solid;color:red;'><span style='float: left'>".$productimagehtml."</span><span> ".$productnamehtml."</span></td> <td style='border:1px solid'>".$productpricehtml."</td> <td style='border:1px solid'>".$orderedaty."</td> <td style='border:1px solid'>".$totalprice."</td></tr>";
          $htmlnew.=$html;
        }
     }
 
     $customer_email = $cust_email_id;
     $customer = Mage::getModel("customer/customer");
     $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
     $customer->loadByEmail($customer_email); //load customer by email id
     $customername = ucfirst($customer->getData('firstname'));
      $welcomemsg = "Dear ".$customername.','.'<br/><br/>';
      $baseUrl = Mage::getBaseUrl().'abandonedcart/index/cartreturn?email='.$to;
      $message = $welcomemsg." ".$htmlnew."<a href='".$baseUrl."'>Return to cart</a>";
   
      $abandonedcart = Mage::helper('abandonedcart');
      $abandonedcart->sendMail('manoj.chowrasiya@iksula.com',$status,'233703','Manoj',$message);
      // exit;
    }

    public function updateAbandonedCartAction($quote,$abundantcart_id){
      //UPDATE 
      $abandonedcart = Mage::getModel('abandoned/abandoned');
      $quote_collectionArray = array();
      $quote_ItemArray = array();
      $salesQuoteItemObj = Mage::getSingleton('sales/quote_item');
      $price = 0;
      $discount_amount = 0;
      $salesQuoteItem = $salesQuoteItemObj->getCollection()->addFieldToFilter('quote_id', $quote['entity_id'])->getData();
      foreach ($salesQuoteItem as $items) {
        // $quote_ItemArray[]=$items['sku
        $price += $items['price'];
        $discount_amount += $items['discount_amount'];
        $qty = (int)$items['qty'];
        $quote_ItemArray[]=$items['product_id'].'_'.$qty;
      }
       // echo $price.' '.$discount_amount;
       // exit;
      $subtotal = $price - $discount_amount;
      $loadData = $abandonedcart->load($abundantcart_id);
      $quote_collectionArray['email_id'] = $quote['email'];
      $quote_collectionArray['quote_id'] = $quote['entity_id'];
      $quote_collectionArray['is_email_send'] = 0;
      $quote_collectionArray['is_purchase'] = 0;
      $quote_collectionArray['created_time'] = $quote['created_at'];
      $quote_collectionArray['update_time'] = $quote['updated_at'];
      $quote_collectionArray['product_ids'] =implode(',', $quote_ItemArray); 
      $quote_collectionArray['subtotal'] = $subtotal;

      $loadData = $abandonedcart->addData($quote_collectionArray)->save();
    }

    public function cartreturnAction()
    {
      $email = Mage::app()->getRequest()->getParam('email'); 
      $customer = Mage::getModel('customer/customer');
      $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
      $customer->loadByEmail(trim($email));
      Mage::getSingleton('customer/session')->loginById($customer->getId());
      $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
    }

    public function abandonedcartemailAction($customer_email){
         $customer_email = "manoj.chowrasiya@iksula.com";
         $abandonedcollection = Mage::getModel('abandoned/abandoned');
         $abandonedcartCollection = Mage::getSingleton('abandoned/abandoned')->getCollection();
         $abandonedcartCollection->addFieldToFilter('email_id',$customer_email);
         $dataCollection = $abandonedcartCollection->addFieldToFilter('is_email_send',0)->getData();
         foreach ($dataCollection as $value) {
           $cartid = $value['abundantcart_id'];
         }
         if(count($dataCollection)>0){   

           $cart_responce = $this->checkcartinfoAction($customer_email);   
          // exit;
           if($cart_responce){          
             $this->sendemailAbandonedCartAction($customer_email);
             $abandonedcollection->load($cartid);
             // $abandonedcollection->setData('is_email_send',1)->save();
           }
           
         }
         else{
          echo 'mail not send';
        } 

    }

    public function checkcartinfoAction($customer_email){
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
           $cartid = $value['abundantcart_id'];
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

}