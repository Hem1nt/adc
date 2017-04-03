<?php 
include_once 'app/code/core/Mage/Checkout/Model/Cart.php';
class Iksula_Overrides_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{
	/**
     * Convert order item to quote item
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @param mixed $qtyFlag if is null set product qty like in order
     * @return Mage_Checkout_Model_Cart
     */
    public function addOrderItem($orderItem, $qtyFlag=null)
    {

        /* @var $orderItem Mage_Sales_Model_Order_Item */
        if (is_null($orderItem->getParentItem())) {
        	// echo "<pre>"; print_r($orderItem->getData());exit;
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($orderItem->getProductId());
            if (!$product->getId()) {
                return $this;
            }

            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new Varien_Object($info);
            if (is_null($qtyFlag)) {
            	// echo "if - here";exit;
                $info->setQty($orderItem->getQtyOrdered());
                $product_sku = $orderItem->getSku();
            	// if($product_sku == '126 - 6')
                // {
                    //$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_sku);
                    $product = Mage::getModel('catalog/product'); 
                    $product->load($product->getIdBySku($product_sku)); 
            	// }
                // $info->setPrice($orderItem->getSku());
                
            } else {
                $info->setQty(1);
            }
            $this->addProduct($product, $info);
        }
        return $this;
    }
}