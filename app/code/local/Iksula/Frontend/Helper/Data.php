<?php
class Iksula_Frontend_Helper_Data extends Mage_Core_Helper_Abstract{

	public function getStoreCurrency($_finalPrice)	{
		$formattedPrice = Mage::helper('core')->currency($_finalPrice,true,false);
		return $formattedPrice;
	}

	public function getTrustedReviewLink($order){
		$baseUrl  = 'http://trustedcompany.com/review-invite';
		$companyId = '569653';
		$companySecretKey = '2e2d62f821565f17f63ff66559f39de93d12f2e1';
		$email_address = $order->getCustomerEmail();
		$nameofcustomer = $order->getCustomerName();
		$encodedEmail = base64_encode($email_address);
		$customerName = urlencode($nameofcustomer);
		$orderIncrementId = $order->getIncrementId();
		$hashKey = SHA1($companyId . $companySecretKey . $email_address . $orderIncrementId);
		$masterUrl = $baseUrl.'/'.$companyId.'/ul/'.$hashKey.'?a='.$encodedEmail.'&b='.$customerName.'&c='.$orderIncrementId;
		return $masterUrl;
	}

	// public function getStoreCurrency($_finalPrice)	{
	// 	$formattedPrice = Mage::helper('core')->currency($_finalPrice,true,false);
	// 	return $formattedPrice;
	// }
    public function getParentproduct(int $productId){

        $product = Mage::getModel('catalog/product');
        $productobject = $product->load($productId);
        if($productobject->getTypeId() == 'simple'){
            //product_type_configurable
            $parentIds =  Mage::getModel('catalog/product_type_configurable')
                ->getParentIdsByChild($productId);

            //product_type_grouped
            if(!$parentIds){
                $parentIds = Mage::getModel('catalog/product_type_grouped')
            ->getParentIdsByChild($productId);

            }
            //product_type_bundle
            if(!$parentIds){
                $parentIds = Mage::getModel('bundle/product_type')
                ->getParentIdsByChild($productId);

            }
            return $parentIds[0];

        }else{
            return $productId;
        }
        
    }
	public function getReviewLink($id)
	{
        // check if it has parent
        $product_id = $this->getParentproduct($id);

		return Mage::getUrl('review/product/list', array('id'=> $product_id));
	}
    
	public function getOrderStatusData($order_status){
		$history = Mage::getModel('sales/order_status_history')->getCollection();
		// $history->addFieldToFilter('parent_id',$_order->getId());
		// $history->addFieldToFilter('status',$order_status);
		print_r($history->getData());exit();
		return $history;
	}

	public function getstatus($order_statusArray)
	{

		$orderstatestatus = Mage::getModel('sales/order_status_history')->getCollection();
        $orderstatestatusData = $orderstatestatus->getData();

        foreach(array_reverse($orderstatestatusData) as $orderdata) {
            if(!in_array($orderdata['status'], $order_statusArray)) {
                $preOrderarr[] = $orderdata['status'];
                break;
            }
        }

        $data = implode('',$preOrderarr);
        return $data;
	}

    public function getHistoryByStatus($order_status,$_order) {
      $orderHistoryData = Mage::getModel('sales/order_status_history')->getCollection()
      ->addFieldToFilter('status', $order_status)
      ->addFieldToFilter('parent_id', $_order->getId());
      return $orderHistoryData;
    }
	public function getorderstatestatus($order_status_state) {
		$configArray = unserialize(Mage::getStoreConfig('custom_snippet/dynamic_status/orderstatus_state', Mage::app()->getStore()));
        foreach($configArray as $configstatus) {
	        if($configstatus['list_template'] == $order_status_state) {
	            $order_state_status = $configstatus['magento_template'];
	            return $order_state_status;
	            break;
	        }
	   	}
	}

	/**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getUrl('*/*/history');
        }
        return Mage::getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */
    public function getBackTitle()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::helper('sales')->__('Back to My Orders');
        }
        return Mage::helper('sales')->__('View Another Order');
    }

    public function encrypt_decrypt($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'dzfvszx65f5dfgdfxgbdffdgb';
        $secret_iv = 'sdgfvsdg45f56df4gdsx';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function getCustomReorderUrl($order)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $orderurl = Mage::getUrl('sales/guest/reorder', array('order_id' => $order->getId()));
            return $orderurl;
        }
        $orderurl = Mage::getUrl('sales/order/reorder', array('order_id' => $order->getId()));
        return $orderurl;
    }
     /* Attribute info for products */

    public function getFirstChildInfo($_product)
    {
        $firstChildhtml = '';
        if($_product->getTypeId()=='configurable'):
        $ids = $_product->getTypeInstance()->getUsedProductIds();
        $countofsimple = count($ids);
        if($countofsimple > 0){
            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_product);
            $pack_size = array();
            foreach ($childProducts as $key => $child) {
              if($child->getStatus() == 1){
                $pack_size[$child->getPrice()] = array('pack_size'=>$child->getResource()->getAttribute('pack_size')->getFrontend()->getValue($child),'child'=>$child);
              }
            }
            sort($pack_size);
            $packSizedDisplay = current($pack_size);
            $child = $packSizedDisplay['child'];
            $pack_size = $this->getPackSize($child);
            if($pack_size != "NA" && !empty($pack_size))
            {
                $formattedPrice = Mage::helper('core')->currency($child->getPrice(),true,false);
                $formattedSpecialPrice = Mage::helper('core')->currency($child->getSpecialPrice(),true,false);
                $firstChildhtml .= '<p class="custom_price">';
                $firstChildhtml .= '<span class="custom_child_1">'.$this->getPackSize($child).'</span>';
                $firstChildhtml .= '<span class="custom_child_2"> '.$this->getPharmaceuticalForm($_product).'</span>';
                if($child->getSpecialPrice()) {
                    $firstChildhtml .= '<span class="custom_child_3"> - <span class="old-price">'.$formattedPrice.'</span>
                    </span>&nbsp;&nbsp;<span>'.$formattedSpecialPrice.'</span>';
                } else {
                    $firstChildhtml .= '<span class="custom_child_3"> - '.$formattedPrice.'</span>';
                }
                $firstChildhtml .= "</p>";
            }
        }
        return $firstChildhtml;
        endif;
    }

    public function getFirstChildPrice($_product)
    {
        $firstChildhtml = '';
        if($_product->getTypeId()=='configurable'):
        $ids = $_product->getTypeInstance()->getUsedProductIds();
        $countofsimple = count($ids);
        // $pharmaceutical_form = explode('/',$this->getPharmaceuticalForm($_product));
        $pharmaceutical_form = $this->getPerUnitPharmaceuticalForm($_product);
        $perUnit = Mage::getStoreConfig('custom_snippet/best_value/per_unit');
        $orderListing = Mage::getStoreConfig('custom_snippet/best_value/listing_order');
        //orderListing coming from backend configuration//////////////////////////
        if($countofsimple > 0){
            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_product);
            $pack_size = array();
            $price_division = array();
            foreach ($childProducts as $key => $child) {
              $childData = Mage::getModel('catalog/product')->load($child->getEntityId());  
              if($childData->getStatus() == 1){    
                  $pack_size[$childData->getPrice()] = array(
                    'pack_bonus'=>$childData->getBonus(),
                    'pack_special_price'=>$childData->getSpecialPrice(),
                    'pack_price'=>$childData->getPrice(),
                    'pack_size'=>$childData->getResource()->getAttribute('pack_size')->getFrontend()->getValue($childData) + $childData->getBonus(),
                    );
                  $price_division[$childData->getPrice()] = array(
                    'per_unit'=>$childData->getPrice()/$pack_size[$childData->getPrice()]['pack_size'],
                    'pack_bonus'=>$childData->getBonus(),
                    'pack_special_price'=>$childData->getSpecialPrice(),
                    'pack_price'=>$childData->getPrice(),
                    'pack_size'=>$childData->getResource()->getAttribute('pack_size')->getFrontend()->getValue($childData) + $childData->getBonus()
                    );
              }  
            }
            sort($pack_size);
            sort($price_division);

            // echo '<pre>';print_r($orderListing);exit();
            if($orderListing == 1) {
                $bestValue = Mage::getStoreConfig('custom_snippet/best_value/best_value_listing_order');
                $specialPrice = $pack_size[$bestValue-1]['pack_special_price'];
                $packPrice = $pack_size[$bestValue-1]['pack_price'];
                $packSize = $pack_size[$bestValue-1]['pack_size'];
                $packBonusSize = $pack_size[$bestValue-1]['pack_bonus'];
                $perUnitprice = $packPrice/$packSize;  
                $perUnitSpecialprice = $specialPrice/$packSize;  
            }
            elseif($orderListing == 2) {
                // echo '<pre>';print_r($price_division);exit();
                // krsort($price_division);
                $specialPrice = $price_division[0]['pack_special_price'];
                $packPrice = $price_division[0]['pack_price'].'  ';
                $packSize = $price_division[0]['pack_size'];
                $perUnitprice = $packPrice/$packSize;
                $perUnitSpecialprice = $specialPrice/$packSize;  
            }
            else {
                $specialPrice = $pack_size[0]['pack_special_price'];
                $packPrice = $pack_size[0]['pack_price'];
                $packSize = $pack_size[0]['pack_size'];                
                $perUnitprice = $packPrice/$packSize;
                $perUnitSpecialprice = $specialPrice/$packSize;
            }
             
            // Mage::log($pack_size,null,'price_zero.log');

            $formattedPrice = Mage::helper('core')->currency($perUnitprice,true,false);
            $formattedSpecialPrice = Mage::helper('core')->currency($perUnitSpecialprice,true,false);

            $firstChildhtml .= '<p class="custom_price">';
            $firstChildhtml .= '<span class="custom_child_1">'.$perUnit.'</span>';
            $firstChildhtml .= '<span class="custom_child_2"> '.$pharmaceutical_form.'</span>';
            if($perUnitSpecialprice) {
                    $firstChildhtml .= '<span class="custom_child_3"> - <span class="old-price">'.$formattedPrice.'</span>
                    </span>&nbsp;&nbsp;<span>'.$formattedSpecialPrice.'</span>';
                } else {
                    $firstChildhtml .= '<span class="custom_child_3"> - '.$formattedPrice.'</span>';
                }
                $firstChildhtml .= "</p>";
        }
        return $firstChildhtml;
        endif;
    }

    public function getAllChildPrice($_product)
    {
        if($_product->getTypeId()=='configurable'):
        $ids = $_product->getTypeInstance()->getUsedProductIds();
        $countofsimple = count($ids);
        if($countofsimple > 0){
            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_product);
            $pack_size = array();
            foreach ($childProducts as $key => $child) {
              $pack_size[$child->getPrice()] = array('pack_size'=>$child->getResource()->getAttribute('pack_size')->getFrontend()->getValue($child),'child'=>$child);
            }
            sort($pack_size);
            // $packSizedDisplay = current($pack_size);
            // $child = $packSizedDisplay['child'];
            // $pack_size = $this->getPackSize($child);
            // $price = $child->getPrice();
        }
        // print_r($pack_size);exit();
        return $pack_size;
        endif;
    }

    


    public function getManufacturerInfo($_product){
        $manufacturerHtml = '';
        $manufacturer = $_product->getResource()->getAttribute('manufacturer')
                     ->getFrontend()->getValue($_product);
        if($manufacturer!='No'){
            $manufacturerHtml = $manufacturer;
        }
        return $manufacturerHtml;
    }

    public function getGenericInfo($_product){
        $genericInfo = $_product->getGenericName();
        return $genericInfo;
    }

    public function getUsBrandInfo($_product){
        $usbrandInfo = $_product->getUsBrandName();
        if(empty($usbrandInfo)){
            $usbrandInfo = $this->getActiveIngredients($_product);
        }
        if($usbrandInfo=='N/A'){
            $usbrandInfo = '';
        }
        return $usbrandInfo;
    }

    public function getPackSize($_product){
        $packSize = $_product->getResource()->getAttribute('pack_size')->getFrontend()->getValue($_product);
        return $packSize;
    }

    public function getPharmaceuticalForm($_product){
        $pharmaceutical_form = $_product->getResource()->getAttribute('pharmaceutical_form')
                            ->getFrontend()->getValue($_product);
        return $pharmaceutical_form;
    }

    public function getPerUnitPharmaceuticalForm($_product){
        $pharmaceutical_form = $_product->getResource()->getAttribute('per_unit_pharmaceutical_form')
                            ->getFrontend()->getValue($_product);
        return $pharmaceutical_form;
    }

    public function getCartPackSize($_product){
        $packSize = $_product->getResource()->getAttribute('pack_size')->getFrontend()->getValue($_product);
        $pharmaceutical_form = $_product->getResource()->getAttribute('pharmaceutical_form')
                            ->getFrontend()->getValue($_product);
        return $packSize.' '.$pharmaceutical_form;
    }

    public function getActiveIngredients($_product){
        $active_ingridients = $_product->getResource()->getAttribute('active_ingridients')
                            ->getFrontend()->getValue($_product);
        return $active_ingridients;
    }

    public function getStrengthInfo($_product){
        $configurable_attribute = $_product->getConfigurableAttribute();
        return $configurable_attribute;
    }

    public function getCustomStockStatus($_product){
        $custom_stock_status = $_product->getResource()->getAttribute('custom_stock_status')
                             ->getFrontend()->getValue($_product);
        if($custom_stock_status!='No'){
            $custom_stock_status = $custom_stock_status;
        }
        return $custom_stock_status;
    }

     public function leastPrice($pack_size){
        $i=0;
        // echo '<pre>';
        // print_r($pack_size);exit();
        foreach ($pack_size as $value) {
            $minPrice[] = $value['pack_price']/$value['pack_size'];
            $special_Price = $value['special_price'];
            $Price = $value['price'];
            $i++;
        }
        return $minPrice;
     } 

     public function getUnitPrice($_product){ 
        if($_product->getTypeId()=='configurable'):            
            $storeId = Mage::app()->getStore()->getId();
            $cache = Mage::getSingleton('core/cache');
            // echo '<pre>';print_r($cache->getData());exit;
            $key = 'product-page-bestprice-'.$storeId.'-'.$_product->getId();  //Unique Key for every Product Page
            $data = $cache->load($key);

            // var_dump($data);
            // print_r($data);exit;
            if($data == NULL){     
                // echo "fresh";           
                $ids = $_product->getTypeInstance()->getUsedProductIds();
                $countofsimple = count($ids);
                if($countofsimple > 0){
                    $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_product);
                    $pack_size = array();
                    foreach ($childProducts as $key => $child) {
                        $packsizeCount = $child->getResource()->getAttribute('pack_size')->getFrontend()->getValue($child);
                        $packsizeBonus = $child->getResource()->getAttribute('bonus')->getFrontend()->getValue($child);
                        $totalQty = $packsizeBonus + $packsizeCount;
                        $unitPrice = $child->getFinalPrice()/$totalQty;
                        $pack_size[$child->getPrice()] = array('unit_price'=>$unitPrice,'pack_price'=>$child->getPrice(),'pack_size'=>$child->getResource()->getAttribute('pack_size')->getFrontend()->getValue($child));
                    }
                    sort($pack_size);
                }
                try {
                    /* Serialize Multidimensional Array */
                    $data = serialize($pack_size);
                    /*Save the array with cache with the Unique Key */
                    $cache->save($data, $key, array("product-detail-page"), 60*60*24);
                    
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                return $pack_size;
            }else{
                 // echo "cache";    
                // If cache already exists the return the cached data in unserialized format
                return unserialize(urldecode($data)); 
            }
        endif;
     }

     public function getproductReviews($id){
        $summaryData = Mage::getModel('review/review_summary')->load($id);
        $reviews['reviews_count'] = $summaryData->getReviewsCount();
        $reviews['rating_summary'] = $summaryData->getRatingSummary();
        return $reviews;
     }
}

