<?php
class Iksula_Callforoffer_Model_Observer
{
	public function AfterAddtoCart(Varien_Event_Observer $obs)
	{
		$item = $obs->getQuoteItem();
		$productid = $item->getData('product_id');
		$item = ($item->getParentItem() ? $item->getParentItem() : $item );

		// $price = $this->_getPriceByItem($item,$productid);
		// $item->setCustomPrice($price);
		// $item->setOriginalCustomPrice($price);
              // echo '<pre>';print_r($item->getData());exit;
		$storeinfo = $productid.'------'.$price;
		Mage::log($storeinfo,null,'addtocart.log');
		$item->getProduct()->setIsSuperMode(true);

	}

	public function salesQuoteItemSetCustomAttribute($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $shipped_from = $product->getShippedFrom();
        
        if($shipped_from == 'No' || empty($shipped_from) || $shipped_from ==''){
        	$childId = $product->getId();
        	$parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($childId);
        	$_product = Mage::getModel('catalog/product')->load($parent_ids[0]);
        	if($_product->getId()){
        		$shipped_from = $_product->getShippedFrom();
        	}else{
        		$shipped_from = Mage::getStoreConfig('general/generalsetting/shippingfrom');
        	}
        }
        $quoteItem->setShippedFrom($shipped_from);
    }

	public function adminhtml_sales_order_create_process_data(Varien_Event_Observer $observer)
    {
        try {

		        $resource = $observer->getEvent()->getResource();
	        	$resource->addVirtualGridColumn(
		            'country_id',
		            'sales/order_address',
		            array(
		                'shipping_address_id' => 'entity_id'
		            ),
		            'country_id'
	        	);

	        	$resource->addVirtualGridColumn(
	        		'customer_telephone',
	        		'sales/order_address',
	        		array('billing_address_id' => 'entity_id'),
	        		'telephone'
	        	);



        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

	protected function _getPriceByItem(Mage_Sales_Model_Quote_Item $item,$productid)
	{

		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$_taxHelper  = new Mage_Tax_Helper_Data;
		$finalprice = $_taxHelper->getPrice($product, $product->getFinalPrice(), true);
		return $finalprice;
            // return 40;
	}

	public function callforfreeregistration(Varien_Event_Observer $observer)
	{

		$event = $observer->getEvent();
		$order = $event->getOrder();
		$orderId = $order->getRealOrderId();
		$collection = Mage::getModel('callforoffer/callforoffers');
		$callforfreeval=Mage::getSingleton('core/session')->getCallforfreeval();
		//$timetocall = Mage::getSingleton('core/session')->getTimetocall();
		$timeforcall = Mage::getSingleton('core/session')->getTimeforcall();
		//var_dump($timeforcall);
		//Mage::getSingleton('core/session')->unsTimeforcall();
		Mage::getSingleton('core/session')->unsCallforfreeval();
		$custid = $order->getCustomerId();

		//save callforoffers value in database
		// $order->setCallforoffers($callforfreeval);
		$order->setData('callforoffers',$callforfreeval);
		//$order->setData('timetocall',$timeforcall);
		$order->save();

		$name = ucwords($order->getShippingAddress()->getName());
		$customertelephoneno = $order->getShippingAddress()->getTelephone();
		$callforofferscollection =$collection->getCollection();
		$callforofferscollection->addFieldToFilter('customerid',$custid);
		$callforofferscollection->addFieldToFilter('customertelephoneno',$customertelephoneno);
		$cfo_id =$callforofferscollection->getData('id');
		if($custid!=''){
			$customertype = 0;
		}
		else{
			$customertype = 1;
		}

        //if user unsubscribe the offer ser as No in Database table
		if($cfo_id[0]['status']!=$callforfreeval){
			$newcollection = $collection->load($cfo_id[0]['id']);
			$newcollection->setData('status',$callforfreeval);
			$newcollection->save();
		}

        //set value for the call for offers in callforfree table
		if(count($callforofferscollection->getData())==0){
			if($callforfreeval=='1'){
				$collection->setData('cust_name',$name);
				$collection->setData('customerid',$custid);
				$collection->setData('customertelephoneno',$customertelephoneno);
				$collection->setData('status',$callforfreeval);
				$collection->setData('customertype',$customertype);
				$collection->save();
			}
		}
        //set value for the call for offers in customer section
		if($custid){
			$customerObj = Mage::getModel("customer/customer")->load($custid);
			if($callforfreeval==0){
				$callforoffer = '0';
			}
			else{
				$callforoffer = '1';
			}
			$customerObj->setData('callforoffers',$callforoffer);
			//$customerObj->setData('timetocall',$timetocall);
			$customerObj->save();
		}
	}

		public function clearAbandonedCarts(Varien_Event_Observer $observer)
		{
			// echo 'mano';exit;
			$lastQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
			if ($lastQuoteId) {
				$customerQuote = Mage::getModel('sales/quote')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());
				$customerQuote->setQuoteId($lastQuoteId);
				$this->_removeAllItems($customerQuote);

			} else {
				$quote = Mage::getModel('checkout/session')->getQuote();
				$this->_removeAllItems($quote);
			}
			//exit;
		}

		protected function _removeAllItems($quote){
			foreach ($quote->getAllItems() as $item) {
				//echo '<pre>';
				//print_r($item->getData());
				$productid = $item->getData('product_id');
				if($productid =='7071'){
					$productid = $item->setData('qty',1);
				}
				//$item->isDeleted(true);
				if ($item->getHasChildren()) {
					foreach ($item->getChildren() as $child) {
						//$child->isDeleted(true);
					}
				}
			}
			$quote->collectTotals()->save();
		}



}
