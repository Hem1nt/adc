<?php
/**
 * J2T RewardsPoint2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@j2t-design.com so we can send you a copy immediately.
 *
 * @category   Magento extension
 * @package    RewardsPoint2
 * @copyright  Copyright (c) 2011 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rewardpoints_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getReferalUrl()
    {
        return $this->_getUrl('rewardpoints/');
    }
    
    
    public function getResizedUrl($imgName,$x,$y=NULL){
        $imgPathFull=Mage::getBaseDir("media").DS.$imgName;
 
        $widht=$x;
        $y?$height=$y:$height=$x;
        $resizeFolder="j2t_resized";
        $imageResizedPath=Mage::getBaseDir("media").DS.$resizeFolder.DS.$imgName;
        
        if (!file_exists($imageResizedPath) && file_exists($imgPathFull)){
            $imageObj = new Varien_Image($imgPathFull);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepTransparency(true);
            $imageObj->resize($widht,$height);
            $imageObj->save($imageResizedPath);
        }
        
        //return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$resizeFolder.DS.$imgName;
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$resizeFolder.'/'.$imgName;
    }
    
    
    
    public function getProductPointsText($product, $noCeil = false, $from_list = false){
        $math_method = Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId());
        if ($math_method == 1 || $math_method == 2){
            $noCeil = true;
        }
        
        $points = $this->getProductPoints($product, $noCeil, $from_list);        
        $img = '';
        if ($points){
            if (Mage::getStoreConfig('rewardpoints/design/small_inline_image_show', Mage::app()->getStore()->getId())){
                $img = '<img src="'.$this->getResizedUrl('j2t_image_small.png', 16, 16) .'" alt="" width="16" height="16" /> ';
            }
            
            $return = '<p class="j2t-loyalty-points inline-points">'.$img. Mage::helper('rewardpoints')->__("With this product, you earn <span id='j2t-pts'>%d</span> loyalty point(s).", $points) . $this->getEquivalence($points) . '</p>';
            return $return;
        }
        return '<p class="j2t-loyalty-points inline-points" style="display:none;">'.$img. Mage::helper('rewardpoints')->__("With this product, you earn <span id='j2t-pts'>%d</span> loyalty point(s).", $points) . $this->getEquivalence($points) . '</p>';
    }
    
    public function getEquivalence($points){
        $equivalence = '';
        $points = (int)$points;
        //if ($points > 0){
            if (Mage::getStoreConfig('rewardpoints/default/point_equivalence', Mage::app()->getStore()->getId())){
                $formattedPrice = Mage::helper('core')->currency($this->convertPointsToMoneyEquivalence(floor($points)), true, false);
                $equivalence = ' <span class="j2t-point-equivalence">'.Mage::helper('rewardpoints')->__("%d points = %s.", $points, $formattedPrice).'</span>';
            }
        //}
        
        return $equivalence;
    }
    
    
    public function processMathBaseValue($amount, $specific_rate = null){
        $math_method = Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId());
        if ($math_method == 1){
            $amount = round($amount);
        } elseif ($math_method == 0) {
            $amount = floor($amount);
        }
        return $amount;
    }
    

    public function processMathValue($amount, $specific_rate = null){
        $math_method = Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId());
        if ($math_method == 1){
            $amount = round($amount);
        } elseif ($math_method == 0) {
            $amount = floor($amount);
        }
        return $this->ratePointCorrection($amount, $specific_rate);
    }

    public function processMathValueCart($amount, $specific_rate = null){
        $math_method = Mage::getStoreConfig('rewardpoints/default/math_method', Mage::app()->getStore()->getId());
        if ($math_method == 1){
            $amount = round($amount);
        } elseif ($math_method == 0) {
            $amount = floor($amount);
        }
        return $amount;
        //return $this->ratePointCorrection($amount, $specific_rate);
    }

    public function ratePointCorrection($points, $rate = null){
        if ($rate == null){
            $baseCurrency = Mage::app()->getBaseCurrencyCode();
            $currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();
            $rate = Mage::getModel('directory/currency')->load($baseCurrency)->getRate($currentCurrency);
        }
        if (Mage::getStoreConfig('rewardpoints/default/process_rate', Mage::app()->getStore()->getId())){
            /*if ($rate > 1){
                return $points * $rate;
            } else {*/
                return $points / $rate;
            //}
        } else {
            return $points;
        }
    }

    public function rateMoneyCorrection($money, $rate = null){
        if ($rate == null){
            $baseCurrency = Mage::app()->getBaseCurrencyCode();
            $currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();
            $rate = Mage::getModel('directory/currency')->load($baseCurrency)->getRate($currentCurrency);
        }
        
        if (Mage::getStoreConfig('rewardpoints/default/process_rate', Mage::app()->getStore()->getId())){
            /*if ($rate < 1){
                return $money / $rate;
            } else {
                return $money * $rate;
            }*/
                
            return $money * $rate;
        } else {
            return $money;
        }
        
    }

    public function isCustomProductPoints($product){
        $product_default_points = $this->getDefaultProductPoints($product, Mage::app()->getStore()->getId());
        $catalog_points = Mage::getModel('rewardpoints/catalogpointrules')->getAllCatalogRulePointsGathered($product, $product_default_points);
        if ($catalog_points === false){
            return true;
        }
        $attribute_restriction = Mage::getStoreConfig('rewardpoints/default/process_restriction', Mage::app()->getStore()->getId());
        $product_points = $product->getData('reward_points');
        if ($product_points > 0){
            return true;
        }
        return false;
    }
    

    public function getProductPoints($product, $noCeil = false, $from_list = false, $money_points = false){
        if ($from_list){
            $product = Mage::getModel('catalog/product')->load($product->getId());            
        }
        
        $product_default_points = $this->getDefaultProductPoints($product, Mage::app()->getStore()->getId(), $money_points);
        $catalog_points = Mage::getModel('rewardpoints/catalogpointrules')->getAllCatalogRulePointsGathered($product, $product_default_points);
        
        if ($catalog_points === false){
            return 0;
        }
        
        $attribute_restriction = Mage::getStoreConfig('rewardpoints/default/process_restriction', Mage::app()->getStore()->getId());
        $product_points = $product->getRewardPoints();
        $points_tobeused = 0;
        
        if ($product_points > 0){
            $points_tobeused = $product_points + (int)$catalog_points;
            if (Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', Mage::app()->getStore()->getId())){
                if ((int)Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', Mage::app()->getStore()->getId()) < $points_tobeused){
                    return Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', Mage::app()->getStore()->getId());
                }
            }
            return ($points_tobeused);
        } else if (!$attribute_restriction) {
            //get product price include vat
            //echo $product->getFinalPrice();
            //die;
            $_finalPriceInclTax = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
            $_weeeTaxAmount = Mage::helper('weee')->getAmount($product);
            
            // fix for amount issue
            if (Mage::getStoreConfig('rewardpoints/default/exclude_tax', Mage::app()->getStore()->getId())){
                $price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), false);
            } else {
                $price = $_finalPriceInclTax+$_weeeTaxAmount;
            }
            // fix rounding points
            //$price = ceil($price);
            
            if ($money_points !== false){
                $money_to_points = $money_points;
            } else {
                $money_to_points = Mage::getStoreConfig('rewardpoints/default/money_points', Mage::app()->getStore()->getId());
            }
            
            
            if ($money_to_points > 0){
                $price = $price * $money_to_points;
                $points_tobeused = $this->processMathValue($price + (int)$catalog_points);
            } else {
                $points_tobeused += (int)$catalog_points;
            }
            if (Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', Mage::app()->getStore()->getId())){
                if ((int)Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', Mage::app()->getStore()->getId()) < $points_tobeused){
                    return Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', Mage::app()->getStore()->getId());
                }
            }
            if ($noCeil)
                return $points_tobeused;
            else {
                return ceil($points_tobeused);
            }

        }
        return 0;
    }

    public function convertMoneyToPoints($money){
        $points_to_get_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        $money_amount = $this->processMathValue($money*$points_to_get_money);
        
        return $this->rateMoneyCorrection($money_amount);
        //return $money_amount;
    }
    
    
    public function convertBaseMoneyToPoints($money){
        $points_to_get_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        $money_amount = $this->processMathBaseValue($money*$points_to_get_money);
        
        return $money_amount;
    }


    public function convertProductMoneyToPoints($money, $money_points = false){
        if ($money_points !== false){
            $points_to_get_money = $money_points;
        } else {
            $points_to_get_money = Mage::getStoreConfig('rewardpoints/default/money_points', Mage::app()->getStore()->getId());
        }
        
        $money_amount = $this->processMathValue($money*$points_to_get_money);
        return $this->rateMoneyCorrection($money_amount);
        //return $money_amount;
    }
    
    public function convertPointsToMoneyEquivalence($points_to_be_used){
        $points_to_get_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        //$return_value = $this->processMathValueCart($points_to_be_used/$points_to_get_money);
        $return_value = $points_to_be_used/$points_to_get_money;
        return $return_value;
    }
    

    public function convertPointsToMoney($points_to_be_used, $customer_id = null){
        if ($customer_id != null){
            $customerId = $customer_id;
        } else {
            $customerId = Mage::getModel('customer/session')
                                        ->getCustomerId();
        }
        
        $reward_model = Mage::getModel('rewardpoints/stats');
        $current = $reward_model->getPointsCurrent($customerId, Mage::app()->getStore()->getId());
        
        
        //echo "$current < $points_to_be_used";
        //die;
        if ($current < $points_to_be_used) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('rewardpoints')->__('Not enough points available.'));
            Mage::helper('rewardpoints/event')->setCreditPoints(0);
            Mage::helper('checkout/cart')->getCart()->getQuote()
                ->setRewardpointsQuantity(NULL)
                ->setRewardpointsDescription(NULL)
                ->setBaseRewardpoints(NULL)
                ->setRewardpoints(NULL)
                ->save();
            return 0;
        }
        $step = Mage::getStoreConfig('rewardpoints/default/step_value', Mage::app()->getStore()->getId());
        $step_apply = Mage::getStoreConfig('rewardpoints/default/step_apply', Mage::app()->getStore()->getId());
        if ($step > $points_to_be_used && $step_apply){
            Mage::getSingleton('checkout/session')->addError(Mage::helper('rewardpoints')->__('The minimum required points is not reached.'));
            Mage::helper('rewardpoints/event')->setCreditPoints(0);
            Mage::helper('checkout/cart')->getCart()->getQuote()
                ->setRewardpointsQuantity(NULL)
                ->setRewardpointsDescription(NULL)
                ->setBaseRewardpoints(NULL)
                ->setRewardpoints(NULL)
                ->save();
            return 0;
        }

        
        if ($step_apply){
            if (($points_to_be_used % $step) != 0){
                Mage::getSingleton('checkout/session')->addError(Mage::helper('rewardpoints')->__('Amount of points wrongly used.'));
                Mage::helper('rewardpoints/event')->setCreditPoints(0);
                Mage::helper('checkout/cart')->getCart()->getQuote()
                    ->setRewardpointsQuantity(NULL)
                    ->setRewardpointsDescription(NULL)
                    ->setBaseRewardpoints(NULL)
                    ->setRewardpoints(NULL)
                    ->save();
                return 0;
            }
        }

        $points_to_get_money = Mage::getStoreConfig('rewardpoints/default/points_money', Mage::app()->getStore()->getId());
        $discount_amount = $this->processMathValueCart($points_to_be_used/$points_to_get_money);

        //return $this->ratePointCorrection($discount_amount);
        return $discount_amount;
    }

    public function getPointsOnOrder($cartLoaded = null, $cartQuote = null, $specific_rate = null, $exclude_rules = false, $storeId = false, $money_points = false){
        $rewardPoints = 0;
        $rewardPointsAtt = 0;

        if (!$storeId){
            $storeId = Mage::app()->getStore()->getId();
        }
        
        //get points cart rule
        if (!$exclude_rules){
            if ($cartLoaded != null){
                $points_rules = Mage::getModel('rewardpoints/pointrules')->getAllRulePointsGathered($cartLoaded);
            } else {
                $points_rules = Mage::getModel('rewardpoints/pointrules')->getAllRulePointsGathered();
            }
            if ($points_rules === false){
                return 0;
            }
            $rewardPoints += $this->processMathBaseValue($points_rules);//(int)$points_rules;
        }
        
        
        if ($cartLoaded == null){
            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();
        } elseif ($cartQuote != null){
            $items = $cartQuote->getAllItems();
        }else {
            $items = $cartLoaded->getAllItems();
        }
        
        $attribute_restriction = Mage::getStoreConfig('rewardpoints/default/process_restriction', $storeId);

        foreach ($items as $_item){
            $item_default_points = $this->getItemPoints($_item, $storeId, $money_points);
            if ($_item->getHasChildren()){                
                $child_points = Mage::getModel('rewardpoints/catalogpointrules')->getAllCatalogRulePointsGathered($_item->getProduct(), $item_default_points, $storeId);
                if ($child_points === false){
                    continue;
                } else if(!$attribute_restriction) {
                    //FIX COMMENT
                    /*if ($cartLoaded == null || $cartQuote != null){
                        $item_qty = $_item->getQty();
                    } else {
                        $item_qty = $_item->getQtyOrdered();
                    }
                    $bundle_price = $this->getSubtotalInclTax($_item, $storeId);
                    $rewardPoints += $this->processMathBaseValue(Mage::getStoreConfig('rewardpoints/default/money_points', $storeId) * $bundle_price);*/
                    $rewardPoints += $item_default_points;
                }
                
                continue;
            }
            
            /*if ($_item->getHasChildren() && ($_item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE || $_item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE)) {
                continue;
            }*/
            
            if ($_item->getParentItemId()) {
                if ($cartLoaded == null || $cartQuote != null){
                    $item_qty = $_item->getParentItem()->getQty();
                } else {
                    $item_qty = $_item->getParentItem()->getQtyOrdered();
                }
            } else {
                if ($cartLoaded == null || $cartQuote != null){
                    $item_qty = $_item->getQty();
                } else {
                    $item_qty = $_item->getQtyOrdered();
                }
            }
            
            $_product = Mage::getModel('catalog/product')->load($_item->getProductId());
            //getAllCatalogRulePointsGathered($product = null, $item_default_points = null, $storeId = null)
            $catalog_points = Mage::getModel('rewardpoints/catalogpointrules')->getAllCatalogRulePointsGathered($_product, $item_default_points, $storeId);
            
            if ($catalog_points === false){
                continue;
            } else if(!$attribute_restriction) {
                $rewardPoints += $this->processMathBaseValue($catalog_points * $item_qty);
            }
            $product_points = $_product->getData('reward_points');
            
            if ($product_points > 0){
                if ($_item->getQty() > 0 || $_item->getQtyOrdered() > 0){
                    $rewardPointsAtt += $this->processMathBaseValue($product_points * $item_qty);
                }
            } else if(!$attribute_restriction) {
                //check if product is option product (bundle product)
                if (!$_item->getParentItemId()) {
                    //v.2.0.0 exclude_tax
                    //JON
                    /*if (Mage::getStoreConfig('rewardpoints/default/exclude_tax', $storeId)){
                        $tax_amount = 0;
                    } else {
                        $tax_amount = $_item->getBaseTaxAmount();
                    }
                    $price = $_item->getBaseRowTotal() + $tax_amount - $_item->getBaseDiscountAmount();
                    $rewardPoints += $this->processMathBaseValue(Mage::getStoreConfig('rewardpoints/default/money_points', $storeId) * $price);*/
                    $rewardPoints += $item_default_points;
                } else {
                }
                
            }
        }
        $rewardPoints = $this->processMathBaseValue($this->processMathValue($rewardPoints, $specific_rate) + $rewardPointsAtt);

        if (Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', $storeId)){
            if ((int)Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', $storeId) < $rewardPoints){
                return ceil(Mage::getStoreConfig('rewardpoints/default/max_point_collect_order', $storeId));
            }
        }
        return ceil($rewardPoints);
    }
    
    protected function getDefaultProductPoints($product, $storeId, $money_points = false){
        $points = 0;
        $_finalPriceInclTax = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
        $_weeeTaxAmount = Mage::helper('weee')->getAmount($product);

        if (Mage::getStoreConfig('rewardpoints/default/exclude_tax', Mage::app()->getStore()->getId())){
            $price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), false);
        } else {
            $price = $_finalPriceInclTax+$_weeeTaxAmount;
        }
        if ($money_points !== false){
            $points = $this->processMathBaseValue($money_points * $price);
        } else {
            $points = $this->processMathBaseValue(Mage::getStoreConfig('rewardpoints/default/money_points', $storeId) * $price);
        }
        return $points;
    }
    
    protected function getItemPoints($_item, $storeId, $money_points = false){
        //$_product = Mage::getModel('catalog/product')->load($_item->getProductId());
        //$points = $_product->getData('reward_points');
        //if ($points > 0){
            $price = $this->getSubtotalInclTax($_item, $storeId);
            
            if ($money_points !== false){
                $points = $this->processMathBaseValue($money_points * $price);
            } else {
                $points = $this->processMathBaseValue(Mage::getStoreConfig('rewardpoints/default/money_points', $storeId) * $price);
            }
        //}
        return $points;
    }
    
    
    protected function getSubtotalInclTax($item, $storeId)
    {
        $baseTax = ($item->getTaxBeforeDiscount() ? $item->getTaxBeforeDiscount() : ($item->getTaxAmount() ? $item->getTaxAmount() : 0));
        $tax = ($item->getBaseTaxBeforeDiscount() ? $item->getBaseTaxBeforeDiscount() : ($item->getBaseTaxAmount() ? $item->getBaseTaxAmount() : 0));
        $discount_amount = $item->getBaseDiscountAmount();
        
        if (Mage::getStoreConfig('rewardpoints/default/exclude_tax', $storeId)){
            return $item->getBaseRowTotal()-$discount_amount;
        }
        
        //Zend_Debug::dump($item->debug());
        //die;
        return $item->getBaseRowTotal()+$tax-$discount_amount;
    }

    public function cheetahCurlRequest($params=0){
   
         $login_aid = $params['aid'];
         $login_eid = $params['eid'];
         $fname = $params['fname'];
         $Rname = $params['Rname'];
         $email = $params['email'];

        foreach($params as $k=>$v) {
            $sendParams[]="$k=$v";
        }
        
        // print_r($sendParams);
        $login_uri = "https://app.cheetahmail.com/api/login1";
        $login_params = array(
            "name=".urlencode("API_iKsula"),
            "cleartext=".urlencode("ik_Sula_CM")
            );
        $param_string = implode('&', $login_params);
        $curl = curl_init($login_uri);
        if ($curl){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_COOKIEJAR, "C:\\my_cookie_file");
            $response = trim(curl_exec($curl));

               // echo "error: ".curl_error($curl);
                // echo "<br>";
            }
            // return $response;
            curl_close($curl);
            //echo "login server response: $response\n<br>";
          //  echo "<br>";
          //  echo "<pre>";
          //  print_r($sendParams);

            if ($response == "OK"){

                $ebmtrigger_uri = "https://ebm.cheetahmail.com/ebm/ebmtrigger1";
                // $embtrigger_params = $sendParams;
                $embtrigger_params = $sendParams;
                // print_r($embtrigger_params);
                // exit;
                $param_string = implode('&', $login_params);
      
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
                      //  echo "error: ".curl_error($curl); 
                            // echo "<br>";
                    }
                    curl_close($curl);
                        //echo "ebmtrigger server response: $response\n"; 
                        // echo "<br>";
                    
                }
            }
            
        // }
            //exit;
        
    }

    public function currentRewardPoints() {

        $store_id = Mage::app()->getStore()->getId();
        $customer = Mage::getSingleton('customer/customer');
        $reward_model = Mage::getSingleton('rewardpoints/stats');
        $allRefers = $reward_model->getCollection()->addFieldToFilter('points_current',array('gt' => 0))->getData();
        foreach($allRefers as $k=>$v) {
            if($v['customer_id']) {
                if(!in_array($v['customer_id'], $customerIds)) {
                    $customerIds[] = $v['customer_id'];

                    $currentPoints = Mage::getModel('rewardpoints/stats')->getPointsCurrent($v['customer_id'], $store_id)+0;

                    if($currentPoints) {
                        $customerData = $customer->load($v['customer_id']);
                        $customerEmail = $customerData->getEmail();
                        $customerName = $customerData->getName();
                        // echo $customerEmail." ## ".$customerName." ## ".$currentPoints."<br/>";
                        // $customerEmail = 'manoj.chowrasiya@iksula.com';
                        $params = array('email'=>$customerEmail, 'AMT'=>$currentPoints, 'FNAME'=>ucwords($customerName), 'aid'=>'2094210672', 'eid'=>'235682');
                        $this->cheetahCurlRequest($params);
                        // exit;
                       // exit;
                    }

                }
            }
        }

    }
    
}
