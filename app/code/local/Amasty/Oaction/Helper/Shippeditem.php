<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
* @package Amasty_Oaction
*/
class Amasty_Oaction_Helper_Shippeditem extends Mage_Core_Helper_Abstract
{

	public function getShippedItemsHtml($order){
		$html = '';
		$defaultImage = Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl(). '/placeholder/' .Mage::getStoreConfig("catalog/placeholder/small_image_placeholder");
		
		$items = $order->getAllVisibleItems();
		foreach ($items as $item) {
			if($item->getQtyShipped() >= 1){
				$parentAttributs = $this->getParentAttributs($item);
				if($parentAttributs['productType'] != 'bundle' && $parentAttributs['productType'] != ''){
					$packSize = $this->getAttributeLabel('pack_size',$item->getProduct()->getData('pack_size'));
					$pharmaceuticalForm = $this->getAttributeLabel('pharmaceutical_form',$item->getProduct()->getData('pharmaceutical_form'));
					$shippedDetails = $this->getTotalShippedQty($item->getItemId());
					$qty = round($shippedDetails['qty']);
					$total = $shippedDetails['price']*$qty;
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
					$totalQty = ($packSize+$bonus)*$qty;
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
						$shippedDetails = $this->getTotalShippedQty($item->getItemId());
						$qty = round($shippedDetails['qty']);
						$total = $shippedDetails['price']*$qty;
						$formattedPrice = Mage::helper('core')->currency($total, true, false);
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
		}				
		return $html;
	}
	public function getShippingHtml($order)
		{
			$shipping = Mage::helper('core')->currency($order->getShippingAmount(), true, false);
			$discount = Mage::helper('core')->currency($order->getDiscountAmount(), true, false);
			$grandTotal = Mage::helper('core')->currency($order->getGrandTotal(), true, false);

			$html.='<tr>
		  	<td align="right" valign="top" style="padding:14px 20px 5px; font-family:Trebuchet MS, Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; color:#3d3d3d;">Shipping Charges - <strong style="font-size:16px; text-decoration:underline;">'.$shipping.'</strong></td>
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

	public function getTotalShippedQty($id){
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$result=$read->query("SELECT sum(qty) as 'qty', price FROM `sales_flat_shipment_item` WHERE `order_item_id` = '".$id."' group BY '".$id."'");
		$row = $result->fetch();
		return $row;
	}

	public function getCompleteShipment($order){
		$qtyTotal = 0;
		$shippedQtyTotal = 0;
		foreach ($order->getAllVisibleItems() as $item){
			$qtyTotal += $item->getQtyOrdered();
			$shippedQtyTotal += $item->getQtyShipped(); 
		}	
		if($qtyTotal == $shippedQtyTotal){
			return 0;
		}else{
			return 1;
		}
	}

}