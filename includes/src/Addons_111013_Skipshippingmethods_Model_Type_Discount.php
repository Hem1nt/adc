<?php



class Addons_Skipshippingmethods_Model_Type_Discount extends Mage_SalesRule_Model_Quote_Discount
{
   
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
		$amount = $address->getDiscountAmount();	
        if ($amount!=0) {
            $description = $address->getDiscountDescription();
            if (strlen($description)) {
				
				if($description=="Charge"){
								$title = Mage::helper('sales')->__('Shipping Charge', $description);
				}else{
                $title = Mage::helper('sales')->__('Discount (%s)', $description);
				}
            } else {
                $title = Mage::helper('sales')->__('Discount');
            }
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $amount
            ));
        }
        return $this;
    }
}
