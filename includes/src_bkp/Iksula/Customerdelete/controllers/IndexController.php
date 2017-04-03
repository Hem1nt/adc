<?php
class Iksula_Customerdelete_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Customerdelete"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("customerdelete", array(
                "label" => $this->__("Customerdelete"),
                "title" => $this->__("Customerdelete")
		   ));

      $this->renderLayout(); 
	  
    }
     public function deleteAction() {
      echo 'wait for update';exit;
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write'); 
     // insert 
        $sql = "INSERT INTO `duplicateentrytable`(`entity_id`, `email`) select max(entity_id),email from customer_entity group by email having count(email)>1 limit 0,1000";
     //  $sql = "INSERT INTO `duplicateentrytable`(`entity_id`, `email`)  SELECT `entity_id`,`email` FROM `customer_entity` GROUP BY email limit 0,100";
     //  echo $sql;
       $resultset = $connection->query($sql); 

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $sql='SELECT entity_id,email FROM duplicateentrytable';
        $results = $readConnection->fetchAll($sql);

     // print_r($results);
        foreach ($results as $value) {
      // print_r($value);
      // echo $customer_id =181737;
        $customer_id =$value['entity_id'];
      //  echo $email ='10maryli@gmail.com';
          $email =$value['email'];
          echo $deletedentry = $customer_id.'-----------------'.$email.'--------------------';
          $customer_data = Mage::getModel('customer/customer')->load($customer_id);
          $customerdatanew = $customer_data->getData();

          if(empty($customerdatanew))
          {
              echo '----------not exits------------'.'<br />';
           }else{
              $orderCollection = Mage::getModel('sales/order')->getCollection();
              $orderCollection->addFieldToFilter('customer_email',$email);
              $orderCollection->addFieldToFilter('customer_id',$customer_id);
              $orders = Mage::getResourceModel('sales/order_collection')
              ->addFieldToSelect('*')
              ->addFieldToFilter('customer_id', $customer_id);

              if ((!$orders->getSize()))
              { 
                echo '---has never placed an order---'.'<br/>'; 
                $customer = Mage::getModel('customer/customer')->load($customer_id);
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
             // $customer->loadByEmail($email) ;
                Mage::register('isSecureArea', true); 
                $customer->delete (); 
                Mage::unregister('isSecureArea');
              }
              else{
                echo '-----order exits----'.'<br/>';
              }

            }
        }
    }
}