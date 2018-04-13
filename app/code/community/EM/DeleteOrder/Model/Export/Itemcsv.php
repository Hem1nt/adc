<?php

class EM_DeleteOrder_Model_Export_Itemcsv extends EM_DeleteOrder_Model_Export_Abstract
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';

    public function exportOrders($orders)
    {
        $fileName = 'custom_item_export_'.date("Ymd_His").'.csv';
        $fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');

        $this->writeHeadRow($fp);
        foreach ($orders as $order) {
            $order = Mage::getModel('sales/order')->load($order);
            $this->writeOrder($order, $fp);
        }

        fclose($fp);
        return $fileName;
    }


    protected function writeHeadRow($fp)
    {
        fputcsv($fp, $this->getHeadRowValues(), self::DELIMITER, self::ENCLOSURE);
    }


    protected function writeOrder($order, $fp)
    {
        // $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        // $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        $common = $this->getCommonOrderValues($order);

        $orderId = $order->getIncrementId();
        $importModel = Mage::getModel('orderlog/itemexportlog');
        $itemExportLog = $importModel->getCollection()->addFieldToFilter('order_id',$orderId);
        $itemExportLogData = $itemExportLog->getData();
        if(empty($itemExportLogData)){
          $data = array(
            'order_id'=>$orderId,
            'order_id_count'=>'1',
          );
          $importModel->addData($data)->save();
          $exportCount = 1;
        }else{
          $itemData = Mage::getModel('orderlog/itemexportlog')->load($itemExportLogData[0]['id']);
          $itemExportLogCount = $itemData->getData('order_id_count')+1;
          $itemData->setOrderIdCount($itemExportLogCount)->save();
          $exportCount = $itemData->getOrderIdCount();
        }

        $orderItems = $order->getItemsCollection();
        $orderCountry = $order->getShippingAddress()->getCountry();
        // $orderCountry = 'FR';
        $itemInc = $Mauritius = $Singapore = $India = $itemCounter = 0;
        $idcollection = array();
        $i= 1;
        $shippingCountry = array();
        // $extraData = array('','','','');
        $clientComment = $westmead = $emptyStatus = $itemsShippedQty = $itemsAssignDate = $itemsTrackId = array('');
        $status = $order->getStatus();
        $_productModel = Mage::getModel('catalog/product');
        $ship_date = array();
        if(empty($order->getShipmentsCollection()->getData())){
            $ship_date[] = ' ';
        }else{
          foreach($order->getShipmentsCollection() as $shipment){
            $ship_date[] = $shipment->getCreatedAt();
              
          }
        }
        foreach ($orderItems as $item)
        {
            
            // $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($item->getProductId());
            $parentIds = $this->getParentproduct($this->getItemSku($item));
            
            // if($item->getProductType()=="configurable") {
            //     continue;
            // }

            if($item->getProductType() != 'bundle'){
              $data[$i]['bundle_qty'] = 0;
              $parent_sku = $this->getItemSku($item);
              $data[$i]['parent_id'] = $parentIds[0];
              $_product = $_productModel->load($parentIds[0]);
              $data[$i]['item_sku'] = $parent_sku;
              $data[$i]['item_id'] = $item->getId();
              $attr = $_productModel->getResource()->getAttribute("pack_size");
              if ($attr->usesSource()) {
                $pack_size = $attr->getSource()->getOptionText($item->getProduct()->getPackSize());
                $newPackSizeExplode = explode("+", $pack_size);
                $pack_size = array_sum($newPackSizeExplode);

              }
              $configValue = Mage::getStoreConfig('custom_snippet/offers_skus/offers_sku_id');
              $offers_sku = explode(",",$configValue);
              $item_sku = explode("-",$parent_sku);

              if(in_array($item_sku[0],$offers_sku)){
                  $p_bonus=intval(intval(trim($item_sku[1]))*intval($item->getQtyOrdered())/5);
                }else{
                  $p_bonus = ($item->getProduct()->getBonus())*($item->getQtyOrdered());
                }

              if(in_array('bundle', $parentIds)){
                $parentItem = Mage::getModel('sales/order_item')->load($item->getParentItemId());
                $data[$i]['item_qty'] = (int) $item->getQtyOrdered();
                $data[$i]['bundle_qty'] = 1;
                $data[$i]['bundle_total'] = $parentItem->getPrice();
                $data[$i]['item_total'] = $parentItem->getPrice();
              }else{
                $data[$i]['item_qty'] = (int) (($pack_size)*($item->getQtyOrdered()))+ $p_bonus;
                  $data[$i]['item_total'] = $item->getData('row_total');
              }
              $data[$i]['item_shipform'] = $this->getItemShipFrom($item);
              $shippingCountry[] = $this->getItemShipFrom($item);
              $data[$i]['item_data'] = $this->getOrderItemValues($parentIds[0],$item, $order, ++$itemInc);
              $data[$i]['status'] = $order->getStatus();
              $data[$i]['item_common'] = $common;
              $data[$i]['item_common'] = $this->getItemValues();

              if($i==1){
                $data[$i]['item_common'] = $common;
              }else{
                $data[$i]['item_common'] = $this->getItemValues();
              }
              $data[$i]['item_name'] = $_product->getName();
              $data[$i]['item_brand'] = $_product->getBrandCode();
              $data[$i]['export_count'] = $exportCount;
              // $result=$read->query("select GROUP_CONCAT(distinct(s.entity_id)),GROUP_CONCAT(st.track_number) as track_id,GROUP_CONCAT(distinct(s.qty)) as shipped_quantity,
              //                       GROUP_CONCAT(st.assign_date) as assign_date
              //                       from sales_flat_shipment_item as s , sales_flat_shipment_track as st
              //                       where  st.parent_id = s.parent_id 
              //                       and s.order_item_id = ".$item->getItemId());
              // $row = $result->fetch();

              // $data[$i]['track_id'] = $row['track_id'];
              // $data[$i]['shipped_quantity'] = $row['shipped_quantity'];
              // $data[$i]['assign_date'] = $row['assign_date'];
              $i++;
              $itemCounter++;
            }
        }

        $countryArray = array();
        $counter = 0;
        foreach ($shippingCountry as $key) {
          if(strpos($key,',')){
            $counter++;
          }
        }

        /* start of old logic */
        if($counter == 0 || $i == 2){
          if(count($shippingCountry) == 1){
            $country = explode(',',trim($shippingCountry[0]));
            $shipped_from = array_unique($country);
          }else{
            $shipped_from = array_unique($shippingCountry);
          }
        
          if(count($shipped_from)==1){
            $shipped_from = $shipped_from[0]; 
          }
          else{
            $shipped_from = Mage::getStoreConfig('general/generalsetting/shippingfrom');
          }
        }else{
          foreach ($shippingCountry as $key) {
            if(strpos($key,',')){
                $country = explode(',',$key);
                for($i=0;$i<count($country);$i++){
                  array_push($countryArray,trim($country[$i]));
                }
            }else{
                array_push($countryArray,trim($key));
            }
          }
         
          $count = array_count_values($countryArray);
          $keys=array_keys($count);
          if(in_array('India',$keys)){
            $shipped_from = 'India';
          }else{
            $maxs = array_keys($count, max($count));
            if(count($maxs)==1){
              $shipped_from = trim($maxs[0]);
            }else{
              arsort($maxs);
              $maxs = array_values($maxs);
              $shipped_from = trim($maxs[0]);
            }
          }
          
        }

        /* end of old logic */

        /* New logic start to set logic in country is 'US' and $shipped_from = 'Mauritius'*/

        foreach ($shippingCountry as $shippingCountryKey) {

            $shippingCountryKey = explode(',', $shippingCountryKey);
            $shippingCountryKey=array_map('trim',$shippingCountryKey);

           if(in_array('Mauritius',$shippingCountryKey)){
              $Mauritius++;
           }
           if(in_array('Singapore',$shippingCountryKey)){
              $Singapore++;
           }
           if(in_array('India',$shippingCountryKey)){
              $India++;
           }
        }

        foreach ($shippingCountry as $key) {
          $value = explode(',',$key);
          foreach ($value as $newValue) {
            $newCountry[] = trim($newValue);
          }
        }

        if($orderCountry == 'US'){
          $newCountry = array_unique($newCountry);
          if (in_array('India', $newCountry)) {
           $shipped_from = 'India';
          }else{
            if($Singapore == $itemCounter){
              $shipped_from = 'Singapore';
            }elseif($Mauritius == $itemCounter){
              $shipped_from = 'Mauritius';
            }elseif($India == $itemCounter){
              $shipped_from = 'India';
            }else{
              $shipped_from = 'India';
            }
          }          
        }
        if($orderCountry != 'US'){
          $newShippingCountryArray = array_diff(array_unique($newCountry), array('Mauritius'));
           if (in_array('India', $newShippingCountryArray)) {
              $shipped_from = 'India';
            }else{
              if($Singapore == $itemCounter){
                $shipped_from = 'Singapore';
              }elseif($India == $itemCounter){
                $shipped_from = 'India';
              }else{
                $shipped_from = 'India';
              }
          }
        }

        /* end set logic in country is 'US' and $shipped_from = 'Mauritius'*/
        $csvdata = $this->mergeArray($data, 'parent_id');
        $finalarray = array();
        foreach ($csvdata as $csvkey => $csvvalue) {
          $itemsStatus = $itemsPrice = $itemsTotalQtyBonus = $itemname = $itemsSku = $itemsTotalQty = $itemsBrand = $item_exportcount = array();

          if(is_array($csvvalue['status'])){
              $itemsStatus[] = implode(',',$csvvalue['status']);
            }else{
              $itemsStatus[] = $csvvalue['status'];
            }


           if(is_array($csvvalue['item_qty'])){
              if($csvvalue['bundle_qty']){
                $itemsTotalQty[] = array_unique($csvvalue['item_qty']); 
                $itemsTotalQty = current($itemsTotalQty);
              }else{
                $itemsTotalQty[] = array_sum($csvvalue['item_qty']);
              }
            }else{
              // if($csvvalue['bundle_qty']){
              //   $itemsTotalQty[] = array_unique($csvvalue['item_qty']);  
              //   $itemsTotalQty = current($itemsTotalQty);
              // }else{
                $itemsTotalQty[] = $csvvalue['item_qty'];
              // }
            }

            if(is_array($csvvalue['item_sku'])){
              $itemsSku[] = implode(',',$csvvalue['item_sku']);
            }else{
              $itemsSku[] = $csvvalue['item_sku'];
            }
            if(is_array($csvvalue['item_total'])){
              $itemsPrice[] = array_sum($csvvalue['item_total']);
            }else{
              $itemsPrice[] = $csvvalue['item_total'];
            }
          if(is_array($csvvalue['item_name'])){
              $itemname[] = implode(',',$csvvalue['item_name']);
            }else{
              $itemname[] = $csvvalue['item_name'];
            }
            
            // if(is_array($csvvalue['shipped_quantity'])){
            //   $arr = $this->valueReplace($csvvalue['shipped_quantity']);
            //   if($csvvalue['bundle_qty']){
            //     $arr = array_unique($arr);
            //   }
            //   $itemsShippedQty[] = implode(',',$arr);
            // }else{
            //   $emptyValue = $this->replaceEmpVal($csvvalue['shipped_quantity']);
            //   $itemsShippedQty[] = $emptyValue;
            // }

            // if(is_array($csvvalue['assign_date'])){
            //   $arr = $this->valueReplace($csvvalue['assign_date']);
            //   $itemsAssignDate[] = implode(',',$arr);
            // }else{
            //   $emptyValue = $this->replaceEmpVal($csvvalue['assign_date']);
            //   $itemsAssignDate[] = $emptyValue;
            // }

            // if(is_array($csvvalue['track_id'])){
            //   $arr = $this->valueReplace($csvvalue['track_id']);
            //   $itemsTrackId[] = implode(',',$arr);
            // }else{
            //   $emptyValue = $this->replaceEmpVal($csvvalue['track_id']);
            //   $itemsTrackId[] = $emptyValue;
            // }
          $item_shipform = array();
          $item_shipform[] = $shipped_from;
          if(is_array($csvvalue['item_brand'])){
              $itemsBrand[] = implode(',',$csvvalue['item_brand']);
            }else{
              $itemsBrand[] = $csvvalue['item_brand'];
            }
          $item_exportcount[] = $csvvalue['export_count'];
          // $item_brand = $csvvalue['item_brand'];
          //$record = array_merge($csvvalue['item_common'],$itemsSku,$itemname,$itemname,$itemsTotalQty,$itemsPrice,$itemsStatus,$clientComment,$itemsTrackId,$itemsShippedQty,$itemsAssignDate,$emptyStatus,$westmead,$item_shipform,$itemsBrand,$item_exportcount);
          $shippingAmount = array();
          if($csvvalue['item_common'][13] != ''){
          	$shippingAmount[] = $csvvalue['item_common'][13];
          }else{
          	$shippingAmount[] = '';
          }
          //echo $csvvalue['item_common'][13].$itemsStatus;exit;
          $record = array_merge($csvvalue['item_common'],$itemsSku,$itemname,$itemsTotalQty,$itemsPrice,$shippingAmount,$itemsStatus,$clientComment,$itemsTrackId,$ship_date,$item_shipform,$itemsBrand,$item_exportcount);
          unset($record[13]);
          foreach ($record as $key => $value) {
              if ($value == '') {
                   $record[$key] = "  ";
              }
              if(is_array($value)){
                if(empty($rows)){
                  $record[$key] = "  ";   
                }
              }
          }
          foreach ($record as $key => $value) {
            $newarray[$key][] =   $value;
          }
          
        }
        foreach ($newarray as $key => $value) {
          $finalarray[] = implode("  ",array_unique($value));
        }

        fputcsv($fp,$finalarray, self::DELIMITER, self::ENCLOSURE);
    }

    public function getItemShipFrom($item)
    {
      $shipped_from = $item->getProduct()->getData('shipped_from');
      if($shipped_from == 'No' || empty($shipped_from) || $shipped_from ==''){
          $childId = $item->getProduct()->getId();
          $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($childId);
          $_product = Mage::getModel('catalog/product')->load($parent_ids[0]);
          if($_product->getId()){
            $shipped_from = $_product->getShippedFrom();
          }
          if(empty($shipped_from)){
             $shipped_from = Mage::getStoreConfig('general/generalsetting/shippingfrom');
          }          
        }
      return $shipped_from;
    }

    protected function getItemValues()
    {
      return array(
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        );
    }
    protected function getHeadRowValues()
    {
        return array(
          'Order Number',
          'Custom Order #',
          'Order Date',
          'First Name*',
          'Middle Initial',
          'Last Name*',
          'Company',
          'Address*',
          'Address 2',
          'City*',
          'State/Province*',
          'Postal Code*',
          'Country*',
          'SKU Code',
          //'Supplier Product Name|(Product Variation Details)*',
          'Site Product Name*',
          'Qty (From Supplier)',
          'Patient Price for All Packs of this Item*',
          'Shipping Amount',
          'Status',
          'Clients Comments',
          'Tracking Id',
          'ship_date',/*
          'Quantity',
          'Ship Date',
          'Status',
          'Westmead Comments',*/
          'Shipped From',
          'Brand Code',
          'Export Count',
      );
    }
    function mergeArray($result_arr, $key){

      foreach($result_arr as $val){
        $item = $val[$key];
        foreach($val as $k=>$v){
          $arr[$item][$k][] = $v;
        }
      }
    // Combine unique entries into a single array
    // and non-unique entries into a single element
      foreach($arr as $key=>$val){
        foreach($val as $k=>$v){
          if($k=='item_qty' || $k=='shipped_quantity'){
            $field = $v;
          }
          else{

            $field = array_unique($v);
          }

          if(count($field) == 1){
            $field = array_values($field);
            $field = $field[0];
            $arr[$key][$k] = $field;
          }
        }
      }

      return $arr;
    }
    protected function getCommonOrderValues($order)
    {

        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
        $shipingAddress = $order->getShippingAddress();
        
        $firstName = $shippingAddress->getFirstname();
        $lastName = $shippingAddress->getLastname();
        $middlename = $shippingAddress->getMiddlename();
        $streetAddress = explode("\n",$shipingAddress->getData("street"));
        $company = $order->getBillingAddress()->getCompany();
        if(empty($company)){
          $company = $order->getShippingAddress()->getCompany();
        }
        return array(
            $order->getRealOrderId(),
            $order->getCustomerOrderIncrementId(), //customer order increment id
            $order->getCreatedAt(),
            $firstName,
            $middlename,
            $lastName ,
            $company,
            $streetAddress[0],
            $streetAddress[1],
            $shipingAddress ? $shipingAddress->getData("city"): '',
            $shipingAddress ? $shipingAddress->getRegion(): '',
            $shipingAddress ? "'".$shipingAddress->getData("postcode"): '',
            $shipingAddress ? $shipingAddress->getCountry() : '',
            $order->getShippingAmount(),
        );
    }


    protected function getOrderItemValues($parentPrduct,$item, $order, $itemInc=1)
    {

        return array(
            '',
            ''
            );
    }

public function getParentproduct(int $sku){

    $product = Mage::getModel('catalog/product');
    $productobject = $product->load($product->getIdBySku($sku));
    $ProductId = $product->getIdBySku($sku);
    // $helperdata = Mage::helper("modulename/data");

    if($productobject->getTypeId() == 'simple'){
        //product_type_grouped
        $parentIds = Mage::getModel('catalog/product_type_grouped')
        ->getParentIdsByChild($productobject->getId());


        //product_type_configurable
        if(!$parentIds){
            $parentIds = Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild($productobject->getId());

        }
        //product_type_bundle
        if(!$parentIds){
            $parentIds = Mage::getModel('bundle/product_type')
            ->getParentIdsByChild($productobject->getId());
            array_push($parentIds,'bundle');
        }

    }
    return $parentIds;
}

  public function valueReplace($arr){
    $emptyValue = "";
    $na = "NA";
    $count = count($arr);
    for($i=0;$i<$count;$i++) {
       if($arr[$i] == $emptyValue) {
          $arr[$i] = $na;
       }
    }
    return $arr;       
  }

  public function replaceEmpVal($arr){
    if(empty($arr)){
      $replaceVal = 'NA';
    }else{
      $replaceVal = $arr;
    }
    return $replaceVal;
  }
}
?>
