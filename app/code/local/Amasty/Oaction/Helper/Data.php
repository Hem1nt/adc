<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
* @package Amasty_Oaction
*/
class Amasty_Oaction_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getItemsHtml($order){
		$html = '';
		$defaultImage = Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl(). '/placeholder/' .Mage::getStoreConfig("catalog/placeholder/small_image_placeholder");
		$items = $order->getAllItems();
		foreach ($items as $item) {
			$parentAttributs = $this->getParentAttributs($item);
			if($parentAttributs['productType'] != 'bundle' && $parentAttributs['productType'] != ''){
				$packSize = $this->getAttributeLabel('pack_size',$item->getProduct()->getData('pack_size'));
				$newPackSizeExplode = explode("+", $packSize);
	    		$pack_size_NEW = array_sum($newPackSizeExplode);
				$pharmaceuticalForm = $this->getAttributeLabel('pharmaceutical_form',$item->getProduct()->getData('pharmaceutical_form'));
				$qty = round($item->getQtyOrdered());
				$total = $item->getRowTotal();
				$formattedPrice = Mage::helper('core')->currency($total, true, false);
				if($item->getProduct()->getData('bonus')){
					$bonus = $item->getProduct()->getData('bonus');
				}else{
					$bonus = 0;
				}
				$imgPath =  Mage::helper('catalog/image')->init($parentAttributs['product'], 'small_image');
				if(empty($imgPath)){
					$imgPath = $defaultImage;
				}
				if(strpos($packSize, '+') !== false){
				$totalQty = ($pack_size_NEW+$bonus)*$qty;
				}else{
				$totalQty = ($packSize+$bonus)*$qty;
				}
				$genericName = $item->getProduct()->getData('generic_name'); 
				$html.='<tr>
          	<td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 0;">
            	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="left" valign="middle" style="padding:0 0 0 10px;"><img src="'.$imgPath.'" width="42" alt="img1" style="border:none; font-size:14px;" /></td>
                    <td align="left" valign="middle">'.$parentAttributs["name"].'<br />
                      ('.$genericName.')</td>
                  </tr>
                </table>
            </td>
            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">'.$packSize." ".$pharmaceuticalForm.'</td>
            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">'.$bonus.'</td>
            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">'.$qty.'</td>
            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">'.$totalQty." ".$pharmaceuticalForm.'</td>
            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px; border-right:1px solid #aeaeae;">'.$formattedPrice.'</td>
          </tr>';
			}elseif($parentAttributs['productType'] == 'bundle' && $parentAttributs['productType'] !=''){
					// $packSize = $this->getAttributeLabel('pack_size',$item->getProduct()->getData('pack_size'));
					$bundleInfo = $this->getBundleInfo($item);
					$bundleProductName = $bundleInfo[0];
					$qty = round($item->getQtyOrdered());
					$total = $item->getRowTotal();
					$formattedPrice = Mage::helper('core')->currency($total, true, false);
					if($item->getProduct()->getData('bonus')){
						$bonus = $item->getProduct()->getData('bonus');
					}else{
						$bonus = 0;
					}
					array_shift($bundleInfo);
					$bundleItemNames = implode('<br>', $bundleInfo);
					$imgPath =  Mage::helper('catalog/image')->init($parentAttributs['product'], 'small_image');
					if(empty($imgPath)){
						$imgPath = $defaultImage;
					}
					$totalQty = ($packSize+$bonus)*$qty;
				    $genericName = $item->getProduct()->getData('generic_name');
				    $pharmaceuticalForm = $this->getAttributeLabel('pharmaceutical_form',$item->getProduct()->getData('pharmaceutical_form')); 
					$html.='<tr>
					          	<td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 0;">
					            	<table width="100%" border="0" cellspacing="0" cellpadding="0">
					                  <tr>
					                    <td align="left" valign="middle" style="padding:0 0 0 10px;"><img src="'.$imgPath.'" width="42" alt="img1" style="border:none; font-size:14px;" /></td>
					                    <td align="left" valign="middle">'.$bundleProductName.'<br />
					                      '.$bundleItemNames.'</td>
					                  </tr>
					                </table>
					            </td>
					            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">NA</td>
					             <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">'.$bonus.'</td>
					            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">'.$qty.'</td>
					            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px;">'.$totalQty." ".$pharmaceuticalForm.'</td>
					            <td align="center" valign="middle" style="border-left:1px solid #aeaeae; border-bottom:1px solid #aeaeae; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:20px 10px; border-right:1px solid #aeaeae;">'.$formattedPrice.'</td>
					          </tr>';
			}
		}				
				
		return $html;
	}
	public function getShippingHtml($order)
		{
			$shipping = Mage::helper('core')->currency($order->getShippingAmount(), true, false);
			$discount = Mage::helper('core')->currency($order->getDiscountAmount(), true, false);
			$grandTotal = Mage::helper('core')->currency($order->getGrandTotal(), true, false);
			$subTotal = Mage::helper('core')->currency($order->getSubtotal(), true, false);

			$html.='<tr>
		  	<td align="right" valign="top" style="padding:14px 20px 5px; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#3d3d3d;">Subtotal - <strong style="font-size:16px; text-decoration:underline;">'.$subTotal.'</strong></td>
		  </tr>
			<tr>
		  	<td align="right" valign="top" style="padding:0px 20px 5px; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#3d3d3d;">Shipping Charges - <strong style="font-size:16px; text-decoration:underline;">'.$shipping.'</strong></td>
		  </tr>
		  <tr>
		  	<td align="right" valign="top" style="padding:0 20px; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#3d3d3d;">Discount- <strong style="font-size:16px; text-decoration:underline;">'.$discount.'</strong></td>
		  </tr>
		  <tr>
		  	<td align="right" valign="top" style="padding:0 20px; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#3d3d3d;">Total Price - <strong style="font-size:16px; text-decoration:underline;">'.$grandTotal.'</strong></td>
		  </tr>';
		  return $html;
		}
	public function getParentAttributs($item){
			if($item->getProductType() != 'bundle'){
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($item->getProductId());
				if(!empty($parentId)){
					$product = Mage::getModel('catalog/product')->load($parentId[0]);
					if($product->getTypeId() != 'bundle'){
						$parentAttr = array(
							'name'=>$product->getName(),
							'bonus'=>$product->getBonus(),
							'productType'=>$product->getTypeId(),
							'product'=>$product,
						);
						return $parentAttr;
					}else{
						$parentAttr = array();	
						return $parentAttr;
					}
				}
			}else{
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				$parentAttr = array(
						'name'=>$product->getName(),
						'bonus'=>$product->getBonus(),
						'productType'=>$product->getTypeId(),
						'product'=>$product,
					);
				return $parentAttr;
			}
	}

	public function getBundleInfo($item){
        $itemNames = array();
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$itemNames[] = $product->getName(); 
		$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($item->getProductId());
         $collection = $product->getTypeInstance(true)
            ->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);


         foreach ($collection as $item) {
            $itemNames[] = $item->getName();
         }
         return $itemNames; 
	}

	public function getAttributeLabel($attribure_code,$option_value){
		$productModel = Mage::getModel('catalog/product');
		$attributeText = $productModel->getResource()->getAttribute($attribure_code);
		return $attributeText->getSource()->getOptionText($option_value);
	}

	public function getOrderItemHtml($order){
		return Mage::app()->getLayout()->createBlock('sales/order_email_items')->setData('order',$order)->setTemplate('email/order/items.phtml')->toHtml();
	}

	public function getRecommendedProducts($order){
		$items = $order->getAllItems();
		$productModel = Mage::getModel('catalog/product');
		$defaultImage = Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl(). '/placeholder/' .Mage::getStoreConfig("catalog/placeholder/small_image_placeholder");
		$i = 0;
		foreach ($items as $item) {
			$parent = $this->getParentAttributs($item);
			$broughtProductObj = new Jain_Bought_Block_Bought();
			$relatedProductCollection = $broughtProductObj->getBoughtProducts($item->getProductId());
			if($relatedProductCollection){
				foreach ($relatedProductCollection as $relatedProduct) {
					if($i<=2){
						$entityId = $relatedProduct->getEntityId();
						$productData = Mage::getModel('catalog/product')->load($entityId);
						$pUrl = 'https://www.alldaychemist.com/'.$productData->getUrlPath();
						$finalPrice = $productData->getFinalPrice(); 
						$formattedPrice = Mage::helper('core')->currency($finalPrice, true, false);
						$imgPath =  Mage::helper('catalog/image')->init($productData, 'small_image')->resize(150,150);
						if(empty($imgPath)){
								$imgPath = $defaultImage;
						}

						$html .= '<td align="left" valign="top">
			                      	<table width="100%" border="0" cellspacing="0" cellpadding="0">
			                          <tr>
			                            <td align="center" valign="top"><a href="'.$pUrl.'"><img src="'.$imgPath.'" width="150" style="border:none; font-size:14px;"/></a></td>
			                          </tr>
			                          <tr>
			                            <td align="center" valign="middle" style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:12px; color:#333333; padding:10px 0;">'.$productData->getName().'<br />
										<strong>'.$formattedPrice.'</strong></td>
			                          </tr>
			                          <tr>
			                            <td align="center" valign="top" style="padding:10px 0;"><a style="padding:8px 25px; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:13px; color:#ffffff; font-weight:normal; background-color:#4f6b05; text-decoration:none;" href="'.$pUrl.'">Buy Now</a></td>
			                          </tr>
			                        </table>
		                    	   </td>';
	                }
	                $i++;
				}
			}	
		}
			return $html;
	}

}