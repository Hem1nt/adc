<?php
class Iksula_Overrides_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSecureUrl($url){

        return $this->_getUrl($url, array(
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
        ));
    }
	public function getFirstChildPrice($_product)
    {
        $firstChildInfo = array();
        if($_product->getTypeId()=='configurable'):
        $ids = $_product->getTypeInstance()->getUsedProductIds();
        $countofsimple = count($ids);
        if($countofsimple > 0){
            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_product);
            $pack_size = array();
            foreach ($childProducts as $key => $child) {
                $childData = Mage::getModel('catalog/product')->load($child->getEntityId());
                if($childData->getData('status')==1) {
                  $pack_size[$childData->getPrice()] = array('pack_size'=>$childData->getResource()->getAttribute('pack_size')->getFrontend()->getValue($childData),'child'=>$childData);
                }
            }            
            sort($pack_size);
            $packSizedDisplay = current($pack_size);
            $child = $packSizedDisplay['child'];
            $pack_size = $this->getPackSize($child);
            if($pack_size != "NA" && !empty($pack_size))
            {
                $firstChildPrice= Mage::helper('core')->currency($child->getPrice(),true,false);
            }
        }
        return $firstChildPrice;
        endif;
    }

    public function getFirstChildApi($_product)
    {
        $firstChildInfo = array();
        if($_product->getTypeId()=='configurable'):
        $ids = $_product->getTypeInstance()->getUsedProductIds();
        $countofsimple = count($ids);
        if($countofsimple > 0){
            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_product);
            $pack_size = array();
            foreach ($childProducts as $key => $child) {
                if($child->getData('status')==1) {
                  $pack_size[$child->getPrice()] = array('pack_size'=>$child->getResource()->getAttribute('pack_size')->getFrontend()->getValue($child),'child'=>$child);
                }
            }
            sort($pack_size);
            $packSizedDisplay = current($pack_size);
            $child = $packSizedDisplay['child'];
            $pack_size = $this->getPackSize($child);
            if($pack_size != "NA" && !empty($pack_size))
            {
                $firstChildInfo = $this->getPackSize($child).' '.$this->getPharmaceuticalForm($child);
            }
        }
        return $firstChildInfo;
        endif;
    }

    public function getPharmaceuticalForm($_product){
        $pharmaceutical_form = $_product->getResource()->getAttribute('pharmaceutical_form')
                            ->getFrontend()->getValue($_product);
        return $pharmaceutical_form;
    }


    public function getPackSize($_product){
        $packSize = $_product->getResource()->getAttribute('pack_size')->getFrontend()->getValue($_product);
        return $packSize;
    }

    public function SaveAddress($address,$customerId,$status) {
        try {
            $addressdata = array(
                'firstname'=>$address->getData('firstname'),
                'middlename'=>$address->getData('middlename'),
                'lastname'=>$address->getData('lastname'),
                'company'=>$address->getData('company'),
                'telephone'=>$address->getData('telephone'),
                'fax'=>$address->getData('fax'),
                'street'=>$address->getData('street'),
                'city'=>$address->getData('city'),
                'region'=>$address->getData('region'),
                'region_id'=>$address->getData('region_id'),
                'postcode'=>$address->getData('postcode'),
                'country_id'=>$address->getData('country_id'),
                );

            $default = 1;
            $customAddress = Mage::getModel('customer/address');

            $customAddress->setData($addressdata)
                          ->setCustomerId($customerId);
            if($status == 'billing') {
                $customAddress->setIsDefaultBilling($default);
            } else {
                $customAddress->setIsDefaultShipping($default);
            }

            $customAddress->setSaveInAddressBook('1');
            $customAddress->save();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin())
        {
            return true;
        }

        if(Mage::getDesign()->getArea() == 'adminhtml')
        {
            return true;
        }

        return false;
    }

    public function payOrderCount($email){
      $status = Mage::getStoreConfig('order_status/general/selected_status');
      $orderData = Mage::getStoreConfig('order_status/general/order_date');
      $statusArray = explode(',',$status);
      $order = Mage::getModel('sales/order')->getCollection();
      $order->addFieldToFilter('customer_email',$email);
      $order->addFieldToFilter('status',array('in' => $statusArray));
      $order->addFieldToFilter('created_at',array('lteq' => $orderData));
      $count = $order->getSize();
      return $count;        
    }

    public function getBestSellingProducts(){
         return $collection = Mage::getResourceModel('sales/report_bestsellers_collection')->setPageSize(10);
    }

    public function getProductDetails($id){
        return Mage::getModel('catalog/product')->load($id);
    }
    
}

