<?php 
class Excellence_Remove_IndexController extends Mage_Core_Controller_Front_Action
{	

	public function importAddresses3Action(){
		
		$this->_quote = Mage::getModel('sales/quote')->save();
        $this->_order = Mage::getModel('sales/order');
		  $databasetable = "sample";
		  $fieldseparator = ",";
		  $lineseparator = "\n";
		  $csvfile = "Order-import-8-1-2012.csv";
		  echo '<pre>';
		  $row = 0;
			if (($handle = fopen($csvfile, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					//echo "<p> $num fields in line $row: <br /></p>\n";
					$row++; 
					if($row > 1){
						//$this->createOrder($data);exit;
						$orderNo = trim($data[1]);
						$orderSku = trim($data[2]);
						
						if($orderNo != null && $orderNo && 'N/A' && $orderNo != ''){
							if($orderSku != null && $orderSku && 'N/A' && $orderSku != ''){
								$this->_ordersToCreate[$orderNo]['order'] = trim($data[1]);
								$this->_ordersToCreate[$orderNo]['customer'] = trim($data[27]);
								$this->_ordersToCreate[$orderNo]['address'] = $data;
								$this->_ordersToCreate[$orderNo]['gateway'] = trim($data[44]);
								$this->_ordersToCreate[$orderNo]['shippingCost'] = trim($data[7]);
								$this->_ordersToCreate[$orderNo]['status'] = trim($data[11]);
								$this->_ordersToCreate[$orderNo]['date'] = trim($data[0]);
								$this->_ordersToCreate[$orderNo]['history'] = trim($data[39]);
								$this->_ordersToCreate[$orderNo]['items'][] = $data;
							}
						}
					}
				}
				//print_r($this->_ordersToCreate);exit; 
				fclose($handle);
			}
		//	var_dump(get_class($this));
		foreach($this->_ordersToCreate as $orderNo => $orderData){
			
			$this->CreateOrder2();	
			
		}
		echo 'Complete';	
	}
	
	
	
	
	
	
	
	
	


  public function CreateOrder2() {

        try {
            $storeObject = Mage::getModel('core/store')->load(1); // Store ID
        }
        catch(Exeception $ex)
        {
            echo $ex->getMessage();
        }


        $cart_api = Mage::getModel('checkout/cart_api');
        $quoteId = $cart_api->create($storeObject->getStoreId());
        $quote = Mage::getModel('sales/quote')->loadByIdWithoutStore($quoteId);

        $product = Mage::getModel('catalog/product');
        $productId = $product->getIdBySku('SBTEMP075'); // Bundle Product SKU

        $params = array(
            'product' =>  $productId,
            'related_product' => null,
            'bundle_option' => array(
                    164 => '3235', // Bundle Options
                    165 => '3240',
                    166 => '3241'

                ),
             'bundle_option_qty' => array(
                    164 => 1, // Bundle Quantities
                    165 => 1,
                    166 => 1
                ),
        );

        $request = new Varien_Object();
        $request->setData($params);

        $_value['Email'] = 'ZsdfsXcrtdfert@gmail.com';
        $_value['First Name'] = 'Jeffrey';
        $_value['Last Name'] = 'Roberts';
        $_value['Address'] = 'Biscayne Blvd';
        $_value['City'] = 'Miami';
        $_value['Post Code'] = '33137';
        $_value['Telephone'] = '305-555-1155';

        // create customer
        $customer = Mage::getModel('customer/customer');
        $password = 'stackexchange';

        $customer->setWebsiteId(4); // Set Website ID
        $customer->loadByEmail($_value['Email']);
        $customer->setWebsiteId(4); // Set Website ID AGAIN!

        if(!$customer->getId()) {
            $customer->setEmail($_value['Email']);
            $customer->setFirstname($_value['First Name']);
            $customer->setLastname($_value['Last Name']);
            $customer->setPassword($password);
            $customer->setMode(Mage_Checkout_Model_Cart_Customer_Api::MODE_REGISTER);
        }
        else
        {
            $customer->setMode(Mage_Checkout_Model_Cart_Customer_Api::MODE_CUSTOMER);
        }

        try {
            $customer->save();
            $customer->setConfirmation(null);
            $customer->save();
        }
        catch (Exception $ex) {
            error_log(var_dump($ex));
        }

        if (! $customer->getId() ){
            return $this;                
        }

        $dataShipping = array(
            'firstname'  => $_value['First Name'],
            'lastname'   => $_value['Last Name'],
            'street'     => $_value['Address'],
            'city'       => $_value['City'],
            'region'     => '',
            'region_id'  => '',
            'postcode'   => $_value['Post Code'],
            'country_id' => 'CZ', //todo: un-hardcode this.
            'telephone'  => $_value['Telephone'],
        );

        $customerAddress = Mage::getModel('customer/address');

        if ($defaultShippingId = $customer->getDefaultShipping()){
             $customerAddress->load($defaultShippingId); 
        } else {   
             $customerAddress
                ->setCustomerId($customer->getId())
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1')
             ;
             $customer->addAddress($customerAddress);
        }            

        try {
            $customerAddress
                ->addData($dataShipping)
                ->save()
            ;           
        } catch(Exception $e){
            Mage::log('Address Save Error::' . $e->getMessage());
        }

        $customer->save();

        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);

        $quote->addProduct($product, $request);
        $quote->setCustomer($customer);
        $quote->assignCustomer($customer);
        $quote->getShippingAddress()->addData($dataShipping)->setShippingMethod('tablerate_bestway');
        $quote->getShippingAddress()->setShippingMethod('tablerate_bestway');
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->getShippingAddress()->collectShippingRates(); 
        $quote->setPayment($quote->getPayment()->setMethod('free'));
        $quote->collectTotals();
        $quote->save();

        $convertQuoteObj = Mage::getModel('sales/convert_quote');
        $orderObj = $convertQuoteObj->toOrder($quote);

        $orderPaymentObj=$convertQuoteObj->paymentToOrderPayment($quote->getPayment());

        $orderObj->setBillingAddress($convertQuoteObj->addressToOrderAddress($quote->getBillingAddress()));
        $orderObj->setPayment($convertQuoteObj->paymentToOrderPayment($quote->getPayment()));
        $orderObj->setShippingAddress($convertQuoteObj->addressToOrderAddress($quote->getShippingAddress()));


        $qty=1;
        foreach ($quote->getShippingAddress()->getAllItems() as $item) {
            //@var $item Mage_Sales_Model_Quote_Item
            $item->setQty($qty);
            $orderItem = $convertQuoteObj->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($orderObj->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $orderObj->addItem($orderItem);
        }

        $orderObj->setCanShipPartiallyItem(false);

        $totalDue=$orderObj->getTotalDue();
        echo "<p>total due: $totalDue</p>";
        $orderObj->place(); //calls _placePayment
        $orderObj->save();
        $orderId=$orderObj->getId();
        echo "<p>orderId: $orderId</p>";

    }





	
	
	
	
	
	
	
	
}


?>