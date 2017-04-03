<?php
class Iksula_Cartrule_Model_Observer
{
	public function handleMaxDiscountFormCreation($observer)
	{
		$fldSet = $observer->getForm()->getElement('action_fieldset');
		if ($fldSet) {
	        $fldSet->addField(
                'max_discount_amount', 'text', array(
                'name'  => 'max_discount_amount',
                'label' => Mage::helper('salesrule')->__('Max Amount of Discount'),
            ),
                'discount_amount'
            );   
        }
	}

	public function newcoupondiscountcal($observer)
	{
		$quote = $observer->getEvent()->getQuote();
		$quoteSubtotal = $quote->getSubtotal();
		$ruleIds = $quote->getAppliedRuleIds();
			Mage::log($ruleIds,null,'$ruleIds.log');
		
		if($ruleIds) {
			$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
			if(Mage::app()->getStore()->isAdmin()) {
                $items =  Mage::getSingleton('adminhtml/session_quote')->getQuote()->getAllItems();
            }
			foreach($items as $item){
				$discount += $item->getDiscountAmount();
			Mage::log($discount ,null,'$discAmt.log');
			}

			$discAmt = round($discount,2);
			$newRuleIds = explode(',',$ruleIds);
			$countRuleIds = count($newRuleIds);
			for($m=0;$m<$countRuleIds;$m++){
				$rules = Mage::getModel('salesrule/rule')->load($newRuleIds[$m]);
				if(round($rules->getMaxDiscountAmount()) == 0){
					if($rules->getSimpleAction() == 'by_percent'){
						$bigDiscountAmount += number_format(($quoteSubtotal*$rules->getDiscountAmount())/100,2);
					}else{
						$bigDiscountAmount +=  $rules->getDiscountAmount();
					}
				}else{
					$bigDiscountAmount +=  $rules->getMaxDiscountAmount();
				}
			}

			for($i=0;$i<$countRuleIds;$i++){
				$rules = Mage::getModel('salesrule/rule')->load($newRuleIds[$i]);
				// Mage::log($bigDiscountAmount,null,'$bigDiscountAmount.log');
				$rules->setWebsiteIds("1");
				// if($rules->getCouponCode() == 'BuyCareprostOnline' ){
						$discountAmount = $rules->getMaxDiscountAmount();
						if($discountAmount > 0) {
							if($discAmt > $bigDiscountAmount) {

								
								$quote = $observer->getEvent()->getQuote();
								$shippingAmount = $quote->getShippingAddress()->getShippingAmount();

								$quoteid =$quote->getId();
								if($quoteid) {
									$total=$quote->getBaseSubtotal();
									$quote->setSubtotal(0);
									$quote->setBaseSubtotal(0);

									$quote->setSubtotalWithDiscount(0);
									$quote->setBaseSubtotalWithDiscount(0);

									$quote->setGrandTotal(0);
									$quote->setBaseGrandTotal(0);	

									$canAddItems = $quote->isVirtual()? ('billing') : ('shipping');    
									foreach ($quote->getAllAddresses() as $address) {
										// Mage::log($address->getData(),null,'$address->getData().log');
										$address->setSubtotal(0);
										$address->setBaseSubtotal(0);
										$address->setGrandTotal(0);
										$address->setBaseGrandTotal(0);
										// $address->setSubtotalWithDiscount(0);
										// $address->setBaseSubtotalWithDiscount(0);
										$address->save();							
										$address->collectTotals();
										// Mage::log($address->getSubtotal(),null,'$address->getSubtotal().log');
										// Mage::log($quote->getData(),null,'$quote->getData().log');
										$quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
										$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());
										$quote->setSubtotalWithDiscount(
											(float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
											);
										$quote->setBaseSubtotalWithDiscount(
											(float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
											);
										$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
										$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

										$quote ->save(); 
										// Mage::log($quote->getData(),null,'$quote->getData1().log');
										$quote->setGrandTotal($quote->getBaseSubtotal()-$bigDiscountAmount+$shippingAmount)
										->setBaseGrandTotal($quote->getBaseSubtotal()-$bigDiscountAmount+$shippingAmount)
										->setSubtotalWithDiscount($quote->getBaseSubtotal()-$bigDiscountAmount)
										->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$bigDiscountAmount)
										->save();

										if($address->getAddressType()==$canAddItems) {

										 $address->setGrandTotal($quote->getSubtotal()-$bigDiscountAmount)->save();
										 $address->setBaseGrandTotal($quote->getSubtotal()-$bigDiscountAmount)->save();
											$address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount());
								             $address->setGrandTotal((float) $address->getGrandTotal()+$shippingAmount);
								             $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount());
								             $address->setBaseGrandTotal((float) $address->getBaseGrandTotal()+$shippingAmount);
								             $address->setDiscountAmount(-($bigDiscountAmount));
								             $address->setBaseDiscountAmount(-($bigDiscountAmount));
											$address->save();
										}
										// Mage::log($address->getData(),null,'$address->getData2().log');
									}
									foreach($quote->getAllItems() as $item){
			                			// We apply discount amount based on the ratio between the GrandTotal and the RowTotal
										$rat=$item->getPriceInclTax()/$total;
										$ratdisc=$bigDiscountAmount*$rat;
										$item->setDiscountAmount($ratdisc * $item->getQty());
										$item->setBaseDiscountAmount($ratdisc * $item->getQty())->save();

									}
								}   
							}
						}
				// }				 
			}
			// $rules = Mage::getModel('salesrule/rule')->load($ruleIds); 
			
			
			
		}
	}

	public function maxnewcoupondiscountcal($event)
	{
		$quote = $event->getUserQuote();
		$ruleIds = $quote->getAppliedRuleIds();
			Mage::log($ruleIds,null,'$ruleIds.log');
		
		if($ruleIds) {
			$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
			if(Mage::app()->getStore()->isAdmin()) {
                $items =  Mage::getSingleton('adminhtml/session_quote')->getQuote()->getAllItems();
            }
			foreach($items as $item){
				$discount += $item->getDiscountAmount();
			}

			$discAmt = round($discount,2);
			$newRuleIds = explode(',',$ruleIds);
			$countRuleIds = count($newRuleIds);
			for($m=0;$m<$countRuleIds;$m++){
				$rules = Mage::getModel('salesrule/rule')->load($newRuleIds[$m]);
				if(round($rules->getMaxDiscountAmount()) == 0){
					$bigDiscountAmount +=  $rules->getDiscountAmount();
				}else{
					$bigDiscountAmount +=  $rules->getMaxDiscountAmount();
				}
			}
			for($i=0;$i<$countRuleIds;$i++){
				$rules = Mage::getModel('salesrule/rule')->load($newRuleIds[$i]);
				// Mage::log($bigDiscountAmount,null,'$bigDiscountAmount.log');
				$rules->setWebsiteIds("1");
				// if($rules->getCouponCode() == 'BuyCareprostOnline' ){
						$discountAmount = $rules->getMaxDiscountAmount();
						if($discountAmount > 0) {
							if($discAmt > $bigDiscountAmount) {

								
								// $quote = $observer->getEvent()->getQuote();
								$shippingAmount = $quote->getShippingAddress()->getShippingAmount();

								$quoteid =$quote->getId();
								if($quoteid) {
									$total=$quote->getBaseSubtotal();
									$quote->setSubtotal(0);
									$quote->setBaseSubtotal(0);

									$quote->setSubtotalWithDiscount(0);
									$quote->setBaseSubtotalWithDiscount(0);

									$quote->setGrandTotal(0);
									$quote->setBaseGrandTotal(0);	

									$canAddItems = $quote->isVirtual()? ('billing') : ('shipping');    
									foreach ($quote->getAllAddresses() as $address) {
										// Mage::log($address->getData(),null,'$address->getData().log');
										$address->setSubtotal(0);
										$address->setBaseSubtotal(0);
										$address->setGrandTotal(0);
										$address->setBaseGrandTotal(0);
										// $address->setSubtotalWithDiscount(0);
										// $address->setBaseSubtotalWithDiscount(0);
										$address->save();							
										$address->collectTotals();
										// Mage::log($address->getSubtotal(),null,'$address->getSubtotal().log');
										// Mage::log($quote->getData(),null,'$quote->getData().log');
										$quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
										$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());
										$quote->setSubtotalWithDiscount(
											(float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
											);
										$quote->setBaseSubtotalWithDiscount(
											(float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
											);
										$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
										$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

										$quote ->save(); 
										// Mage::log($quote->getData(),null,'$quote->getData1().log');
										$quote->setGrandTotal($quote->getBaseSubtotal()-$bigDiscountAmount+$shippingAmount)
										->setBaseGrandTotal($quote->getBaseSubtotal()-$bigDiscountAmount+$shippingAmount)
										->setSubtotalWithDiscount($quote->getBaseSubtotal()-$bigDiscountAmount)
										->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$bigDiscountAmount)
										->save();
										
										if($address->getAddressType()==$canAddItems) {
										 
										 $address->setGrandTotal($quote->getSubtotal()-$bigDiscountAmount)->save();
										 $address->setBaseGrandTotal($quote->getSubtotal()-$bigDiscountAmount)->save();
											$address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount());
								             $address->setGrandTotal((float) $address->getGrandTotal()+$shippingAmount);
								             $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount());
								             $address->setBaseGrandTotal((float) $address->getBaseGrandTotal()+$shippingAmount);
								             $address->setDiscountAmount(-($bigDiscountAmount));
								             $address->setBaseDiscountAmount(-($bigDiscountAmount));
											$address->save();
										}
										// Mage::log($address->getData(),null,'$address->getData2().log');
									}
									foreach($quote->getAllItems() as $item){
			                			// We apply discount amount based on the ratio between the GrandTotal and the RowTotal
										$rat=$item->getPriceInclTax()/$total;
										$ratdisc=$bigDiscountAmount*$rat;
										$item->setDiscountAmount($ratdisc * $item->getQty());
										$item->setBaseDiscountAmount($ratdisc * $item->getQty())->save();

									}
								}   
							}
						}
				// }
				 
			}
			// $rules = Mage::getModel('salesrule/rule')->load($ruleIds); 
			
			
			
		}
	}
		
}
