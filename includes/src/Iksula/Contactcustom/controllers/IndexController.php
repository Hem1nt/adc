<?php
class Iksula_Contactcustom_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction()
    {
		$block = $this->getLayout()->createBlock("core/template")->setTemplate("catalog/product/compare/ajax_sidebar.phtml");
		echo $block->toHtml();
	}
	public function customernewdeleteAction()
    {
    	$orderid = $this->getRequest()->getParam('orderid');
    	$order	= Mage::getModel('sales/order')->load($orderid);
    	//echo '<pre>';
		$billing_address = $order->getBillingAddress();
		$customername = $order->getCustomerName();
		$incrementid = $order->getIncrementId();

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
		$shippingAddressfull = $customername.'<br/>'.$streetaddress.'<br/>'.$city.', '.$regionaddress.' '.$postcode.'<br/>'.$countryocde.'<br/>Telephone : '.$telephone;
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
		$billingaddressfull =  $billingname.'<br/> '.$billstreetaddress.'<br/> '.$billcity.', '.$billregion.''.$billpostcode.'<br/>'.$billcountrycode.'<br/>Telephone : '.$billtelphone;
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
 		$img_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."frontend/default/alldaychemist/images/logo_print.gif";
		$HTML ='<table width="700" border="0">
				  	<tr>
					
					    <td width="250"><img width="180px" src="'.$img_url.'" /></td>
					    <td width="150">&nbsp;</td>
					    <td width="250">&nbsp;</td>
				  </tr>
				  <tr>
					    <td style="font-size:14px;">Order # '.$incrementid.'<br/><br/>Order Date : '.$orderdate.'</td>
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
	
		        <table width="700" border="1" cellpadding="10" align="center">
		          <tr style="font-size:9px;font-weight:bold;">
		            <td width="92" >Product Name</td>
		            <td width="92">Equivalent US Brand</td>
		            <td width="78" cellpadding="10">Generic</td>
		            <td width="97">Strength</td>
		            <td width="65" >Pack Size</td>
		            <td width="55">Bonus</td>
		            <td width="53">No. of Packs</td>
		            <td width="65">Total Qty</td>
		            <td width="57">Price</td>
		            <td width="57">Total</td>
		          </tr>';

		$ordered_items = $_order->getAllItems();
		$subtotal = $_order->getData('base_subtotal');
		$shipping_amount = $_order->getData('base_shipping_amount');
		$grandtotal = $_order->getData('base_grand_total');
		$discount_amount  = abs($_order->getData('discount_amount'));
		foreach($ordered_items as $item){

				$item->getItemId(); //product id
				$orderedQty = $item->getQtyOrdered(); //ordered qty of item
				$productname = $item->getName();
				$productObject = Mage::getModel('catalog/product')->loadByAttribute('sku',$item->getSku());
				$genericname = $productObject->getData('generic_name');
				$pack_size = $productObject->getData('pack_size');
				$pharmaceutical_form = $productObject->getData('pharmaceutical_form');
				$productModel = Mage::getModel('catalog/product');
				$attr = $productModel->getResource()->getAttribute("pack_size");
				if ($attr->usesSource()) {
					$pack_size_text = $attr->getSource()->getOptionText($pack_size);
				}
				$attr = $productModel->getResource()->getAttribute("pharmaceutical_form");
				if ($attr->usesSource()) {
					$pharmaceutical_form_label = $attr->getSource()->getOptionText($pharmaceutical_form);
				}
				$childId = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
				$parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($childId);
				$productObjectNew = Mage::getModel('catalog/product')->load($parent_ids[0]);
				
				$productname = $productObjectNew->getData('name');
				$bonus= $productObjectNew->getData('bonus');
				//exit;
				$pack_size_label = $pack_size_text.' '.$pharmaceutical_form_label;
				$totalquantity = $pack_size_text.' '.$pharmaceutical_form_label;
				$orderprice = $item->getData('base_price');
				$totalprice = $orderprice * $orderedQty;
				$us_brand_name = $productObject->getData('us_brand_name');
				$configurable_attribute = $productObject->getData('configurable_attribute');
				
		// print_r($item->getData('discount_amount'));
		// 		exit;

				$HTML.='<tr style="font-size:9px;text-align:center;">
							<td>'.$productname.'</td>
							<td>'.$us_brand_name.'</td>
							<td>'.$genericname.'</td>
							<td>'.$configurable_attribute.'</td>
							<td>'.$pack_size_label.'</td>
							<td>'.$bonus.'</td>
							<td>'.number_format($orderedQty).'</td>
							<td>'.$totalquantity.'</td>
							<td>'.'&#x24;'.sprintf ("%.2f", $orderprice).'</td>
							<td>'.'&#x24;'.sprintf ("%.2f", $totalprice).'</td>

						</tr>';
				}

			$HTML.='<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="7">Subtotal</td> 
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $subtotal).'</td>
					</tr>
					<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="7">Discount</td> 
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $discount_amount).'</td>
					</tr>
					<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="7">Shipping & Handling</td> 
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $shipping_amount).'</td>

					</tr> 
					<tr cellpadding="10" style="font-size:9px;font-weight:bold;text-align:right;">
						<td colspan="7">Grand Total</td> 
						<td colspan="3">'.'&#x24;'.sprintf ("%.2f", $grandtotal).'</td>
					</tr>
				</table>';

		$pdf->writeHTML($HTML, true, false, false, false, '');
			
		Mage::getSingleton('core/session')->unsPdfsession();
		$fileName = 'Order_'.$incrementid.'.pdf';
		$pdf->Output($fileName, 'D');		
        return $pdf;
	}
}
?>