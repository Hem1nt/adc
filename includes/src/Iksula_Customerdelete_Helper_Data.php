<?php
class Iksula_Customerdelete_Helper_Data extends Mage_Core_Helper_Abstract
{
  
    /**
     * Generates CSV report for sales data accordng to product category wise
     * @return array
     */
    
    public function generateMlnList($fromdate,$todate)
    {
       $resource = Mage::getSingleton('core/resource');
       $readConnection = $resource->getConnection('core_read');
       $writeConnection = $resource->getConnection('core_write');
     //  $query = 'SELECT  `sku` ,  `name` , SUM(`qty_ordered` ) as soldqty, SUM(`qty_canceled` ) as qtycanceled, SUM(`qty_refunded`) as qtyrefunded,product_id FROM  `sales_flat_order_item` GROUP BY  `sku` ORDER BY  `sku`'; 
       $query = " SELECT i.`sku` as sku , i.`name` as name, SUM(i.`qty_ordered` ) AS soldqty, SUM(i.`qty_canceled` ) AS qtycanceled, SUM( i.`qty_refunded` ) AS qtyrefunded, i.`product_id`
 FROM sales_flat_order o LEFT JOIN sales_flat_order_item i ON o.`entity_id` = i.`order_id`
 WHERE o.`status` IN ('complete','dispensing','Order Shipped','Shipped With tracking Number','payment_accepted') and i.`created_at` <'".$todate."' and i.`created_at`>'".$fromdate."'
 GROUP BY i.`sku` order BY i.`qty_ordered` limit 0,100";
 // echo $query;
 // exit;
       $results = $readConnection->fetchAll($query);
       $io = new Varien_Io_File();
       $path = Mage::getBaseDir('var') . DS . 'export' . DS;
       $name = md5(microtime());
       $file = $path . DS . $name . '.csv';
       $io->setAllowCreateFolders(true);
       $io->open(array('path' => $path));
       $io->streamOpen($file, 'w+');
       $io->streamLock(true);
       $io->streamWriteCsv($this->_getCsvHeaders($results));
       $collection = Mage::getModel('catalog/product');
       $categoryCollection = Mage::getModel('catalog/category');
       $productCollectionObject  = Mage::getModel('catalog/product')->getCollection();
       $productIds = $productCollectionObject->getAllIds();

       foreach ($results as $product) {
        $productsku = explode('-', $product['sku']);
        $productcollection = $collection->loadByAttribute('sku',$productsku[0]);
        if (in_array($product['product_id'], $productIds)) {
          $categoryIds = $productcollection->getCategoryIds();
          foreach($categoryIds as $categoryId) {
            $category = $categoryCollection->load($categoryId);
            $catname = $category->getData('name');
            $catnamenew[] = $catname;
          }
        } 
        $product['product_id']=$catnamenew[0];
        $product[7]=$catnamenew[1];
        $product[8]=$catnamenew[2];
        $io->streamWriteCsv($product);
        $catnamenew = array();
      }
      return array(
        'type'  => 'filename',
        'value' => $file,
        'rm'    => true // can delete file after use
      );
    }

    /**
    * Function used for the headers in the report for sales data accordng to product category wise
    */
    protected function _getCsvHeaders($products)
    {
        $product =array('Sku', 'Name', 'Qty Sold', 'Qty Cancelled','Qty Refunded','Category 1 ','Category 2 ');
        return $product;
    }

    /**
     * Generates CSV report for sales data for customer who have not order since last 3 months
     * 
     */

     public function generateCustomerreport()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');
        $customeridsquery = 'SELECT entity_id FROM customer_entity GROUP BY email ORDER BY entity_id'; 
        $newcustomerdata = $readConnection->fetchAll($customeridsquery);

        foreach ($newcustomerdata as $key =>$custid) {
          $query = 'SELECT DATEDIFF( NOW( ) ,`created_at` ) AS DiffDate,created_at
          FROM sales_flat_order
          WHERE  `customer_id` ='.$custid['entity_id']; 
          $customerdata = $readConnection->fetchOne($query);
          if($customerdata >=90){
              $customerids[]=$custid['entity_id'];
          }
              
          // echo $query;
          // exit();
        //   if($fromdate!='' || $todate!=''){

        //     $query = "SELECT `created_at` AS DiffDate,created_at
        //     FROM sales_flat_order
        //     WHERE  `customer_id` =".$custid['entity_id']." and `created_at` >='".$fromdate."' and `created_at` <='".$todate."' order by created_at desc";
        //    // echo $query;
        //    // exit();
        //     $value = $key;
        //     $customerdata = $readConnection->fetchOne($query);
        //     $customerids[]=$custid['entity_id'];
            

        // }
       
        /* get records who have not ordered in last 3 months*/ 
        //     $query = 'SELECT DATEDIFF( NOW( ) ,`created_at` ) AS DiffDate,created_at
        //     FROM sales_flat_order
        //     WHERE  `customer_id` ='.$custid['entity_id']; 
        //     $value = $key;
        //     $customerdata = $readConnection->fetchOne($query);
        //     if($customerdata >=90){
        //       $customerids[]=$custid['entity_id'];
        //   }
        // }
          // $value = $key;
          // $customerdata = $readConnection->fetchOne($query);
          // if($customerdata >70){
          //  $customerids[]=$custid['entity_id'];
        
        }
       //exit;
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($this->_getCustomerCsvHeaders($newcustomerdata));
     
        foreach ($customerids as $customer_id) {
            $customerData = Mage::getModel('customer/customer')->load($customer_id);
            $email = $customerData->getData('email');
            $firstname = $customerData->getData('firstname');
            $lastname = $customerData->getData('lastname');
            $query = 'SELECT created_at FROM sales_flat_order WHERE  `customer_id` ='.$customer_id;
            $customerdata = $readConnection->fetchOne($query);
            if($customerdata!=''){
                  try{
                        $data = $customerData->getDefaultBillingAddress();
                        if($data!=''){
                            $telephoneno = $data->getData('telephone');
                        }  
                }
                catch(exception $e){
                      echo $e;
                }
                $newarary = array($email,$firstname,$lastname,$telephoneno,$customerdata);
                $io->streamWriteCsv($newarary);
            }
 
       }
       return array(
          'type'  => 'filename',
          'value' => $file,
          'rm'    => true // can delete file after use
       );
    }

    /**
    * Function used for the headers in the report for sales data for customer who have not order since last 3 months
    */

    protected function _getCustomerCsvHeaders($products)
    {
       $headers =array('Email', 'First Name','Last Name', 'Telephoneno', 'Last Order Date');
       return $headers ;
    }


    public function generateCustomerreportwithfilters12($fromdate,$todate) {

      echo "Hello"; exit;

    }
    public function generateCustomerreportwithfilters($fromdate,$todate)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');
        $customeridsquery = 'SELECT entity_id FROM customer_entity GROUP BY email ORDER BY entity_id'; 
        $newcustomerdata = $readConnection->fetchAll($customeridsquery);
        $sqlQuery = "SELECT customer_id FROM sales_flat_order where (created_at between '".$fromdate."' and '".$todate."') group by customer_id";
        $customerdata = $readConnection->fetchAll($sqlQuery);
        $count = count($customerdata);
        $str = "(";
        $i=1;
        foreach($customerdata as $key => $val) {
          //echo $val['customer_email']; echo "<br/>";
          //echo $i."##".$count; echo "<br/>";
          if(!empty($val['customer_id'])) {
             if($i==$count) {
              $str.="'".$val['customer_id']."'";
            }
            else {
              $str.="'".$val['customer_id']."', ";
            }
          }
          $i++;
        }
        $str.=")";
        //echo $str; exit;
        $sqlQuery = "SELECT entity_id FROM `customer_entity` where entity_id not in $str";
        $customerids = $readConnection->fetchAll($sqlQuery);
        //echo "<pre>"; print_r($customerids); exit;
        //echo count($customerdata); exit;

        
        /* logic for writing file to csv */

        /*$io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($this->_getCustomerfilterCsvHeaders($newcustomerdata));*/

        /* logic for writing file to csv */
        $customerObj = Mage::getModel('customer/customer');

        $my_file = time().'file.csv';
        $content = "some text here";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/extra_reports/".$my_file,"wb");
        
        $newdata = 'Email, First Name, Last Name, Telephoneno, Last Order Date'."\n";
        //echo "<pre>"; print_r($customerids); echo "</pre>";
        foreach ($customerids as $customerDt) {
            $customer_id = $customerDt['entity_id'];
            $customerData = $customerObj->load($customer_id);
            $email = $customerData->getData('email');
            $firstname = $customerData->getData('firstname');
            $lastname = $customerData->getData('lastname');
            $query = 'SELECT created_at FROM sales_flat_order WHERE  `customer_id` ='.$customer_id;
            $customerdata = $readConnection->fetchOne($query);
            if($customerdata!=''){
                  try{
                        $data = $customerData->getDefaultBillingAddress();
                        if($data!=''){
                            $telephoneno = $data->getData('telephone');
                        }  
                }
                catch(exception $e){
                      echo $e;
                }
                $newdata.=$email.", ".$firstname.", ".$lastname.", ".$telephoneno.", ".$customerdata."\n";
                
            } else {
              $newdata.=$email.", ".$firstname.", ".$lastname."\n";
            }
       }
       //echo $newdata; exit;
      fwrite($fp,$newdata);
      fclose($fp);
      header("Location:http://adc.iksulabeta.com/extra_reports/".$my_file); exit;
      //unlink($my_file); exit;
       /*return array(
          'type'  => 'filename',
          'value' => $file,
          'rm'    => true // can delete file after use
       );*/
    }

    /**
    * Function used for the headers in the report for sales data for customer who have not order since last 3 months
    */

    protected function _getCustomerfilterCsvHeaders($products)
    {
       $headers =array('Email', 'First Name','Last Name', 'Telephoneno', 'Last Order Date');
       return $headers ;
    }
}

