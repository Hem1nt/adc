<?php
require_once 'Mage/Catalog/controllers/Product/CompareController.php';

class Iksula_Frontend_Product_CompareController extends Mage_Catalog_Product_CompareController{
    
    public function addAction() {
    	// if (!$this->_validateFormKey()) {
     //        $this->_redirectReferer();
     //        return;
     //    }

    	
        $productId = (int) $this->getRequest()->getParam('product');
        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
        $categoryIds = $product->getCategoryIds();

        $productPresent = false;
        $found = array();
        $compareProducts = Mage::helper('catalog/product_compare')->getItemCollection();
        
        $itemCount = Mage::helper('catalog/product_compare')->getItemCount();

        //Allow only 4 products in comparison list
        if($itemCount == 4) {
            Mage::getSingleton('catalog/session')->addError(
                $this->__('Only 4 products can be present in the comparison list.')
            );
            $this->_redirectReferer();
            return;
        }

        if($itemCount) {
            $compareProductId = $compareProducts->getFirstItem()->getId();
            $compareProductCollection = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($compareProductId);
            $compareProductCats = $compareProductCollection->getCategoryIds();

            foreach($categoryIds as $num) {
                if (in_array($num,$compareProductCats)) {
                    $found[$num] = true;
                }
            }

            foreach($compareProducts as $products) {
                if($productId == $products->getId()) {
                    $productPresent = true;
                }
            }
            
            //Check if categories of products to be compared are matching
            // if(empty($found)){
            //     Mage::getSingleton('catalog/session')->addError(
            //         $this->__('You cannot compare %s with the items in the comparison list.', Mage::helper('core')->escapeHtml($product->getName()))
            //     );
            //     $this->_redirectReferer();
            //     return;
            // }
            
            //Check is product is already present in comparison list
            if($productPresent) {
                Mage::getSingleton('catalog/session')->addError(
                    $this->__('The product %s is already present in the comparison list.', Mage::helper('core')->escapeHtml($product->getName()))
                );
                $this->_redirectReferer();
                return;
            }
        }
        
        //Add product in comparison list
        if ($productId && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())) {
            
            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                Mage::getSingleton('catalog/session')->addSuccess(
                    $this->__('The product %s has been added to the comparison list.', Mage::helper('core')->escapeHtml($product->getName()))
                );
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
            }

            Mage::helper('catalog/product_compare')->calculate();
        }

        $this->_redirectReferer();
    }
}