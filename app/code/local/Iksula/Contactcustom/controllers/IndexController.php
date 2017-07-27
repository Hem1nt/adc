<?php
class Iksula_Contactcustom_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction()
    {
		$block = $this->getLayout()->createBlock("core/template")->setTemplate("catalog/product/compare/ajax_sidebar.phtml");
		echo $block->toHtml();
	}

	public function combopackAction()
    {
    	$cart = Mage::getModel('checkout/cart');
    	$flag = 0;
    	foreach($cart->getQuote()->getAllItems() as $item)
    	{
    		$productid = $item->getProductId();
    		if($productid=='9227'){
    			$flag = 1;
    			break;
    		}else{
    			$flag = 0;
    		}

    	}
		// echo $flag;exit;
    	if($flag!=1):
    		$params = array(
    			'product' => 9227,
    			'related_product' => null,
    			'bundle_option' => array(
    				1 => 1,
    				2 => 2,
    				4 => 4,
    				),
    			'qty' => 1,
    			);
    	$cart = Mage::getSingleton('checkout/cart');
    	$product = new Mage_Catalog_Model_Product();
    	$product->load(9227);
    	$cart->addProduct($product, $params);
    	$cart->save();
    	$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
    	endif;
    	$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));

	}
	public function combopack2Action()
    {
    	$cart = Mage::getModel('checkout/cart');
    	$flag = 0;
    	$storeConfigProductId = Mage::getStoreConfig('custom_offer/custom_code/offer');
    	foreach($cart->getQuote()->getAllItems() as $item)
    	{
    		$productid = $item->getProductId();
    		if($productid==$storeConfigProductId){
    			$flag = 1;
    			break;
    		}else{
    			$flag = 0;
    		}
    	}
// echo $flag;exit;
    	if($flag!=1):
    		$params = array(
    			'product' => $storeConfigProductId,
    			'related_product' => null,
    			'bundle_option' => array(
    				10 => 14,
    				12 => 15,
    				// 4 => 4,
    				),
    			'qty' => 1,
    			);
    	$cart = Mage::getSingleton('checkout/cart');
    	$product = new Mage_Catalog_Model_Product();
    	$product->load($storeConfigProductId);
    	$cart->addProduct($product, $params);
    	$cart->save();
    	$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
    	endif;
    	$this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));

	}
	public function customernewdeleteAction()
    {
    	$orderid = $this->getRequest()->getParam('orderid');
    	$order	= Mage::getModel('sales/order')->load($orderid);
    	//echo '<pre>';
		$billing_address = $order->getBillingAddress();
		$customername = $order->getCustomerName();
		$incrementid = $order->getIncrementId();



		if($order->getData('customer_order_increment_id')) {
			$custom_order_id = $order->getData('customer_order_increment_id');
			$print_id = $order->getData('customer_order_increment_id');
		}else {
			$custom_order_id = '';
			$print_id = $order->getData('increment_id');
		}

		$orders = Mage::getModel('sales/order')->load($orderid)->getShippingAddressId();

		$_order = Mage::getModel('sales/order')->load($orderid);

		$_shippingAddress = $_order->getShippingAddress();

		$companyaddress = $_shippingAddress->getCompany();
		$streetaddress = $_shippingAddress->getStreetFull();
		$regionaddress = $_shippingAddress->getRegion() . '';
		$city = $_shippingAddress->getCity();
		$postcode = $_shippingAddress->getPostcode();
		$telephone = $_shippingAddress->getTelephone();
		$country_code = $_shippingAddress->getCountry_id();
		$countryocde=Mage::app()->getLocale()->getCountryTranslation($country_code);
		if($billregion!='')
		{
			$regionaddress =$regionaddress.', ';
		}
		else{

			$regionaddress='';
		}
		$shippingAddressfull = $customername.'<br/>'.$streetaddress.'<br/>'.$city.', '.$regionaddress.' '.$postcode.'<br/>'.$countryocde;
		$_order->getCustomerId();

		$_BillingAddress = $_order->getBillingAddress();

		$_BillingAddress->getFirstname();
		$_BillingAddress->getLastname();
		$billingname = $_BillingAddress->getFirstname().' '.$_BillingAddress->getLastname();
		$billcomapany = $_BillingAddress->getCompany();
		$billstreetaddress = $_BillingAddress->getStreetFull();
		$billregion = $_BillingAddress->getRegion();
		$billcity = $_BillingAddress->getCity();
		$billpostcode = $_BillingAddress->getPostcode();
		$billtelphone = $_BillingAddress->getTelephone();
		$billcountry_code = $_BillingAddress->getCountry_id();
		$billcountrycode=Mage::app()->getLocale()->getCountryTranslation($billcountry_code);

		if($billregion!='')
		{
			$billregion =$billregion.', ';
		}
		else{

			$billregion='';
		}
		$billingaddressfull =  $billingname.'<br/> '.$billstreetaddress.'<br/> '.$billcity.', '.$billregion.''.$billpostcode.'<br/>'.$billcountrycode;
		$_order->getCustomerId();

		$shippinfmethod = $_order->getShippingDescription().'<br/>';
		$paymentmethod = $_order->getPayment()->getMethodInstance()->getTitle();
		$_order->getCreatedAt();
		$orderdate = date("F j, Y", strtotime($_order->getCreatedAt()));

    	require_once('tcpdf/config/lang/eng.php');
		require_once('tcpdf/tcpdf.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetTitle('order');
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		//set margins
		$pdf->SetMargins(5,5,5,TRUE);
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		// set font
		$pdf->SetFont('helvetica', 'B', 20);
 		$pdf->AddPage();
 		//$img_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."frontend/allday/medicines/images/logo_print.gif";
		$HTML ='<table width="100%" border="0">
				  	<tr>

					    <td width="250"></td>
					    <td width="150">&nbsp;</td>
					    <td width="250">&nbsp;</td>
				  	</tr>
				 	<tr>
				  		<td style="font-size:14px;">Custom Order # '.$custom_order_id.'<br/></td>
				  	</tr>
				  	<tr>
					    <td style="font-size:14px;">Order Date : '.$orderdate.'</td>
					    <td>&nbsp;</td>
					    <td>&nbsp;</td>
				  	</tr>
				  <tr>
					    <td style="font-size:12px;">
					    	<br/><br/><div style="border-bottom:1px solid #ccc;color:#819d01;line-height:2.5px;">SHIPPING ADDRESS</div><br/>'.$shippingAddressfull.'
					       	<br/>
					    </td>
					    <td>&nbsp;</td>
					    <td style="font-size:12px;"><br/><br/><div style="border-bottom:1px solid #ccc;color:#819d01;line-height:2.5px;">SHIPPING METHOD</div><br/>'.$shippinfmethod.'
					    </td>
				  </tr>
				  <tr>
					    <td style="font-size:12px;"><br/><div style="border-bottom:1px solid #ccc;color:#819d01;line-height:2.5px;">BILLING ADDRESS</div><br/>'.$billingaddressfull.'</td>
					    <td>&nbsp;</td>
					    <td style="font-size:12px;"><br/><div style="border-bottom:1px solid #ccc;color:#819d01;line-height:2.5px;">PAYMENT METHOD</div><br/>'.$paymentmethod.'</td>
				  </tr>
				  <tr cellpadding="10">
				  		<td colspan="3"><span style="border-bottom:1px solid #ccc;color:#819d01;line-height:1.5px;font-size:12px;">ITEMS ORDERED</span></td>
				  </tr>
				</table>

		        <table width="100%" border="1" cellpadding="10" align="center">
		          <tr style="font-size:9px;font-weight:bold;">
		            <td width="240" >Product Name</td>
		            <td width="85" >Pack Size</td>
		            <td width="85">Bonus</td>
		            <td width="83">No. of Packs</td>
		            <td width="85">Total Qty</td>
		            <td width="57">Price</td>
		            <td width="57">Total</td>
		          </tr>';

		$ordered_items = $_order->getAllItems();
// 		$quoteId = $order->getQuoteId();
// $quote   = Mage::getModel('sales/quote')->load($quoteId);
		// echo "<pre>";

// $data = $quote->getData();
// print_r($_order->getdata());
// foreach ($totals as $_total) {
//    echo $_total->getCode() . ' => ' . $_total->getValue();
// }
		// print_r($_order->getData());
		// exit;
		$ruleCollection = Mage::getModel('salesrule/rule');
		foreach(explode(",",$_order->getAppliedRuleIds()) as $ruleID) {
                    //Load the rule object
                    $rule = $ruleCollection->load($ruleID);
                    // echo "<pre>"; print_r($rule->getData());
                    $ruleAmount[] = $rule->getDiscountAmount();

        }
		$subtotal = $_order->getData('base_subtotal');
		$shipping_amount = $_order->getData('base_shipping_amount');
		$grandtotal = $_order->getData('base_grand_total');
		$discount_amount  = abs($_order->getData('discount_amount'));
		foreach($ordered_items as $item){

				$productObject = Mage::getModel('catalog/product')->loadByAttribute('sku',$item->getSku());
				$active_ingridientsattr = $productObject->getResource()->getAttribute("active_ingridients");
				$activeIngridientsId = $productObject->getActiveIngridients();
				$activeIngridient = $active_ingridientsattr->getSource()->getOptionText($activeIngridientsId);
				$item->getItemId(); //product id
				$orderedQty = $item->getQtyOrdered(); //ordered qty of item
				$productname = $item->getName()."</br>".$activeIngridient;

				$productModel = Mage::getModel('catalog/product');

				$childId = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
				$parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($childId);
				/* start fix for simple product */
				$_childProduct = Mage::getModel('catalog/product')->load($childId);
				if(!empty($parent_ids)){
					$productObjectNew = Mage::getModel('catalog/product')->load($parent_ids[0]);
					$genericname = $productObject->getData('generic_name');
					$pack_size = $productObject->getData('pack_size');
					$pharmaceutical_form = $productObject->getData('pharmaceutical_form');
					$attr = $productModel->getResource()->getAttribute("pack_size");
					if ($attr->usesSource()) {
						$pack_size_text = $attr->getSource()->getOptionText($pack_size);
					}
					$attr = $productModel->getResource()->getAttribute("pharmaceutical_form");
					if ($attr->usesSource()) {
						$pharmaceutical_form_label = $attr->getSource()->getOptionText($pharmaceutical_form);
					}
				}else{
					$productObjectNew = $_childProduct;
					$genericname = $productObjectNew->getData('generic_name');
					$pack_size = $productObjectNew->getData('pack_size');
					$attr = $productModel->getResource()->getAttribute("pack_size");
					if ($attr->usesSource()) {
						$pack_size_text = $attr->getSource()->getOptionText($pack_size);
					}
					$pharmaceutical_form = $productObjectNew->getData('pharmaceutical_form');
					$attr = $productModel->getResource()->getAttribute("pharmaceutical_form");
					if ($attr->usesSource()) {
						$pharmaceutical_form_label = $attr->getSource()->getOptionText($pharmaceutical_form);
					}
				}
				/* end fix for simple product */

				$productname = $productObjectNew->getData('name');

				$bonus = $productObject->getData('bonus') * $orderedQty;
				//exit;
				$pack_size_label = $pack_size_text.' '.$pharmaceutical_form_label;
				$newPackSizeExplode = explode("+", $pack_size_text);
	    		$pack_size_NEW = array_sum($newPackSizeExplode);
				if(strpos($pack_size_text, '+') !== false)
	    		{
				$totalquantity = ($pack_size_NEW * $orderedQty) + $bonus.' '.$pharmaceutical_form_label;
	    		}else{
				$totalquantity = ($pack_size_text * $orderedQty) + $bonus.' '.$pharmaceutical_form_label;
	    		}
				$orderprice = $item->getData('base_price');
				$totalprice = $orderprice * $orderedQty;
				$us_brand_name = $productObject->getData('us_brand_name');
				$configurable_attribute = $productObject->getData('configurable_attribute');

		// print_r($item->getData('discount_amount'));
		// 		exit;

				$HTML.='<tr style="font-size:9px;text-align:center;">
							<td><p style="margin:0;">'.$productname .'</p><p style="margin:0;">'. $activeIngridient.'</p></td>
							<td>'.$pack_size_label.'</td>
							<td>'.$bonus.'</td>
							<td>'.number_format($orderedQty).'</td>
							<td>'.$totalquantity.'</td>
							<td>'.'&#x24;'.sprintf ("%.2f", $orderprice).'</td>
							<td>'.'&#x24;'.sprintf ("%.2f", $totalprice).'</td>

						</tr>';
				}

			$HTML.='<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="6">Subtotal</td>
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $subtotal).'</td>
					</tr>
					<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="6">Discount</td>
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $discount_amount).'</td>
					</tr>
					<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="6">Shipping & Handling</td>
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $shipping_amount).'</td>

					</tr>
					<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="6">Grand Total</td>
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $grandtotal).'</td>
					</tr>
				</table>';
		$HTML.='<p align="justify" style="font-size:9px;font-weight:bold;text-align:left;" >I certify that I am '."'over 18 years'".' and that I am under the supervision of a doctor.
				The ordered medication is for my own personal use and is strictly not meant for a re-sale.
				I also accept that I am taking the medicine /s at my own risk and that I am duly aware of
				all the effects / side effects of the medicine / s. If my order contain Tadalafil,
				I confirm that the same is not meant for consumption in the USA. I acknowledge that the drugs
				are as per the norms of the country of destination.</p>';

		$pdf->writeHTML($HTML, true, false, false, false, '');

		Mage::getSingleton('core/session')->unsPdfsession();
		$fileName = 'Order_'.$print_id.'.pdf';
		$pdf->Output($fileName, 'D');
        return $pdf;
	}
}
?>
