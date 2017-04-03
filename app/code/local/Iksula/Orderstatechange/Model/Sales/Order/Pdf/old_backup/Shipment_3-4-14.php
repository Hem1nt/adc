<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
 
 require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');
include_once 'app/code/core/Mage/Sales/Model/Order/Pdf/Shipment.php';

class Iksula_Orderstatechange_Model_Sales_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{
  
    public function getPdf($shipmentsArray = array())
    {
	//echo "sdf";exit;
		$this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        // create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//        $pdf->getCreatedAt(); 
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('Invoice');
		//$pdf->SetSubject('TCPDF Tutorial');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(30,2,25,TRUE);
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------
		// set font
		$pdf->SetFont('helvetica', 'B', 20);
 
		$this->_setPdf($pdf);
		
        foreach ($shipmentsArray as $shipment) {
		
			$pdf->AddPage();
			  $pdfsession = Mage::getSingleton('core/session')->getPdfsession();
			  if($pdfsession != "1")
			  {
				$shipment = $shipmentsArray;
			  }
			  
			foreach ($shipment as $shipments) 
			{
					if ($shipments->getStoreId()) {
						Mage::app()->getLocale()->emulate($shipments->getStoreId());
						Mage::app()->setCurrentStore($shipments->getStoreId());
					}
					$order = $shipments->getOrder();
						$invoice_no = '&nbsp';
						$invoice_Date = '&nbsp;';
						
					if($order->hasInvoices()) {
					// "$_eachInvoice" is each of the Invoice object of the order "$order"
						foreach ($order->getInvoiceCollection() as $_eachInvoice) {
							$invoice_no = $_eachInvoice->getData('increment_id');
							$invoice_Date =date('d/m/Y', time($_eachInvoice->getData('created_at')));
						}
					}
					$order_date = date('d/m/Y', time($order->getData('created_at')));
					$trackArr = $shipments->getTracksCollection()->getData();
						//$carrier_code = $trackArr[0]['carrier_code'];
						$track_title = $trackArr[0]['title']; 
						$track_number = $trackArr[0]['track_number']; 
						$shipping_add = $shipments->getShippingAddress();
						
						$shippingAdd 		= $shipments->getShippingAddress()->getData();
						$c_name 				= $shippingAdd['firstname'].' '.$shippingAdd['lastname'];
						$filename = $shippingAdd['firstname'].$shippingAdd['lastname'];
						$street1 					= $shipping_add->getStreet(1);
						$street2 					= $shipping_add->getStreet(2);
						$city 						= $shippingAdd['city'];
						$postcode 			= $shippingAdd['postcode'];
						$region 					= $shippingAdd['region'];
						$telephone			= $shippingAdd['telephone'];
						$fax				= $shippingAdd['fax'];
						$country = $shippingAdd['country_id'];
						$country=Mage::app()->getLocale()->getCountryTranslation($country);
						$address = Mage::getModel('customer/address')->load($shippingAdd['customer_address_id']);
						
						//$landmark_shipping		= $address->getData('landmark');
						//$landline_shipping		= $address->getData('telephonetwo');
						$shippingNo 			= $shipments->getIncrementId();	
						
						$orderId 				= $order->getData('increment_id');
						$shippingDate 		= date('d/m/Y', $shipments->getCreatedAtDate()->getTimestamp());
						//echo "<pre>";
						//print_r($address->getData());exit;
						$pdf->SetFont('helvetica', '', 12);
						$HTML = '<br/><br/><br/><table border="0" style="background-color: #FFF;font-size:14px;font-family:Arial;width:100%">
								<tr>
									<td colspan="1" style="text-align:left;width:40%;"><strong>Checked By-</strong></td>
									<td colspan="1" style="text-align:left;width:30%;"><strong>REF. NO.</strong>'.$shippingNo.'</td>
									<td colspan="1" style="text-align:left;width:30%;"><strong>Order NO.</strong>'.$orderId.'</td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"><strong>Date :</strong>'.$order_date.'</td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"><strong>Order NO.</strong>'.$orderId.'</td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:center"><strong>CUSTOMER REFERENCE GUIDE</strong></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"><strong>US BRAND NAME / S (for reference purpose):</strong></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
								<td colspan="4">
								 <table border="1" style="background-color: #Fff;width:100%">
									<tr>
										<td colspan="1" style="text-align:left;width:30%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>US Brand Name/s </strong></td>
										<td colspan="1" style="text-align:left;width:20%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>Strength</strong></td>
										<td colspan="1" style="text-align:left;width:20%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>Quantity</strong></td>
										<td colspan="1" style="text-align:left;width:30%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>Name of Active Ingredient (generic name)</strong></td>
									</tr>';
								
								
								
					/* Add body */
					$count = 1;
					foreach ($shipments->getAllItems() as $item) {
						if ($item->getOrderItem()->getParentItem()) {
							continue;
						}
						$itemArr = $item->getData();
						$product = Mage::getModel('catalog/product')->load($itemArr['product_id']);
						 $attr = $product->getResource()->getAttribute("pharmaceutical_form");
						//product detail
						$p_us_brand = $product->getUsBrandName();
						$p_genericname = $product->getGenericName();
						$p_strength = $product->getConfigurableAttribute();
						$p_bonus = $product->getBonus();
						$item_sku = explode("-",$itemArr['sku']);
						# nilesh
						//$product = Mage::getModel('catalog/product');
						$parent_sku = $item_sku[0];
						$prod = Mage::getModel('catalog/product')->loadByAttribute('sku', $parent_sku)->getData();
						$strength = $prod['configurable_attribute'];
						if(empty($strength)) {
						$strength = "";
						}
						# end
						$p_packagesize = trim($item_sku[1]) + $p_bonus;
						//$qty = number_format($itemArr['qty'])."X".$p_packagesize;
						$pharmaceuticalformId = $product->getPharmaceuticalForm();
						$pharm = $attr->getSource()->getOptionText($pharmaceuticalformId);
						$qty = number_format($itemArr['qty'])."X".$p_packagesize . ' ' . $pharm;
						//Us brand Name Reference----
							// $HTML.='<tr>
							// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_us_brand.' - '.$strength.'</td>
							// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$strength.'</td>
							// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$qty.'</td>
							// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_genericname.'</td>
							// 		</tr>';
						
						$product_type_id  = $product->getData('type_id');
						$p_us_brand = $product->getName();
											// exit;
						if($product_type_id=='bundle'){
											// print_r($itemArr['qty']);
							$total_pills = $qty;
							$qty = number_format($itemArr['qty'])."X".number_format($itemArr['qty']). ' ' . $pharm;
							$p_genericname = '';
							$product_id = $product->getData('entity_id');

							$bundled_product = new Mage_Catalog_Model_Product();
							$bundled_product->load($product_id);
							$selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
								$bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
								);
							$bundled_items = array();
							foreach($selectionCollection as $option) {
								$attr = $bundled_product->getResource()->getAttribute("active_ingridients");
								if ($attr->usesSource()) {
									$bundle_active_ingridients[] = $attr->getSource()->getOptionText($option['active_ingridients']);
								}

							}
							$p_genericname = implode(' , ', $bundle_active_ingridients);
							$p_us_brand = $product->getName();
							$HTML.='<tr>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_us_brand.'</td>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$strength.'</td>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$qty.'</td>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_genericname.'</td>
							</tr>';

						}else{

							$HTML.='<tr>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_us_brand.' - '.$strength.'</td>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$strength.'</td>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$qty.'</td>
							<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_genericname.'</td>
							</tr>';

						}
						/* Draw item */
						//$this->_drawItem($item, $page, $order);
						//$page = end($pdf->pages);
						$count++;
					}
					$HTML.='</table>
							</td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left"><strong>EQUIVALENT INDIAN PRODUCTS / S</strong></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left">
								<table border="1" style="background-color: #Fff;width:100%">
									<tr>
										<td colspan="1" style="text-align:left;width:30%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>Equivalent Indian Product/s </strong></td>
										<td colspan="1" style="text-align:left;width:20%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>Strength</strong></td>
										<td colspan="1" style="text-align:left;width:20%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>Quantity</strong></td>
										<td colspan="1" style="text-align:left;width:30%;padding-top:7px;padding-bottom:7px;padding-left:5px;"><strong>Name of Active Ingredient (generic name)</strong></td>
									</tr>';
									$count1 = 1;
									foreach ($shipments->getAllItems() as $item) {
										if ($item->getOrderItem()->getParentItem()) {
											continue;
										}
										$itemArr = $item->getData();
										$product = Mage::getModel('catalog/product')->load($itemArr['product_id']);
										
										$p_brand = $product->getName();
										//product detail
										//configureble product name
										$simpleProductId = $item->getProductId(); 
										$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId); 
										$config_product = Mage::getModel('catalog/product')->load($parentIds[0]);
										if(strpos($p_brand,'-') !== false){
											$itemNameArray=explode('-', $p_brand);
											$p_brand = $itemNameArray[0];
										}
										if($p_brand==''){
											
										     $p_brand=$config_product->getName();
										}
										   //end
										$p_genericname = $product->getGenericName();
										$p_strength = $product->getConfigurableAttribute();
										$p_bonus = $product->getBonus();
										$item_sku = explode("-",$itemArr['sku']);
										# nilesh
										//$product = Mage::getModel('catalog/product');
										$parent_sku = $item_sku[0];
										$prod = Mage::getModel('catalog/product')->loadByAttribute('sku', $parent_sku)->getData();
										$strength = $prod['configurable_attribute'];
										if(empty($strength)) {
										$strength = "";
										}
										# end
										$p_packagesize = trim($item_sku[1]) + $p_bonus;
										$pharmaceuticalformId = $product->getPharmaceuticalForm();
										$pharm = $attr->getSource()->getOptionText($pharmaceuticalformId);
										//$qty = number_format($itemArr['qty'])."X".$p_packagesize;
										$qty = number_format($itemArr['qty'])."X".$p_packagesize . ' ' . $pharm;
										$bundle_active_ingridients =array(); 
										$product_type_id  = $product->getData('type_id');
										$p_us_brand = $product->getName();
											// exit;
										if($product_type_id=='bundle'){
											// print_r($itemArr['qty']);
											$total_pills = $qty;
											$qty = number_format($itemArr['qty'])."X".number_format($itemArr['qty']). ' ' . $pharm;
											$p_genericname = '';
											$product_id = $product->getData('entity_id');

											$bundled_product = new Mage_Catalog_Model_Product();
											$bundled_product->load($product_id);
											$selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
												$bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
												);
											$bundled_items = array();
											foreach($selectionCollection as $option) {
												$attr = $bundled_product->getResource()->getAttribute("active_ingridients");
												if ($attr->usesSource()) {
													$bundle_active_ingridients[] = $attr->getSource()->getOptionText($option['active_ingridients']);
												}

											}
											$p_genericname = implode(' , ', $bundle_active_ingridients);
											$p_us_brand = $product->getName();
											$HTML.='<tr>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_us_brand.'</td>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$strength.'</td>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$qty.'</td>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_genericname.'</td>
											</tr>';

										}else{

											$HTML.='<tr>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_us_brand.' - '.$strength.'</td>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$strength.'</td>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$qty.'</td>
											<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_genericname.'</td>
											</tr>';

										}
										//Us brand Name Reference----
											// $HTML.='<tr>
											// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_brand.' - '.$strength.'</td>
											// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$strength.'</td>
											// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$qty.'</td>
											// 			<td colspan="1" style="text-align:left;padding-top:7px;padding-bottom:7px;padding-left:5px;">'.$p_genericname.'</td>
														
											// 		</tr>';
										/* Draw item */
										//$this->_drawItem($item, $page, $order);
										//$page = end($pdf->pages);
										$count1++;
									}
					$HTML.='</table>
							</td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>Name of Patient and Address:-</strong></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>'.strtoupper($c_name).'</strong></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>'.strtoupper($street1).'</strong></td>
							</tr>
							';
							if(ucwords($country) == "United States")
							{
								$country = "USA";
							}
							if($street2 != "")
							{
							
							$HTML.='<tr>
										<td colspan="3" style="text-align:left"><strong>'.strtoupper($street2).'</strong></td>
									</tr>';
							}
					 $HTML.='<tr>
								<td colspan="3" style="text-align:left"><strong>'.strtoupper($city).'</strong><strong>, '.strtoupper($region).' </strong><strong>&nbsp;'.$postcode.'</strong></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>'.strtoupper($country).'</strong><br/><br/><br/></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left;font-size: 12px;">'.$shippingNo.'</td>
							</tr>
						</table>';
						//echo $HTML; exit;
					$pdf->writeHTML($HTML, true, false, false, false, '');
			}

				//$this->_afterGetPdf();
				/*if ($shipments->getStoreId()) {
					Mage::app()->getLocale()->revert();*/
        }
		//echo $HTML;exit;
		Mage::getSingleton('core/session')->unsPdfsession();
		$fileName = 'Ref_guide.pdf';
		$pdf->Output($fileName, 'D');		
        return $pdf;
        
    }
//for all print pdf 

public function getallprintPdf($invoiceArray = array(),$shipmentsArray = array())
{
	
     $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        // create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//        $pdf->getCreatedAt(); 
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('Invoice');
		//$pdf->SetSubject('TCPDF Tutorial');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(30,0,25,TRUE);
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('helvetica', 'B', 20);
 
		$this->_setPdf($pdf);
		
		foreach ($invoiceArray as $invoice)
		{
		   
		    foreach ($invoice as $invoices) {
					if ($invoices->getStoreId()) {
						Mage::app()->getLocale()->emulate($invoices->getStoreId());
						Mage::app()->setCurrentStore($invoices->getStoreId());
					}
					$pdf->AddPage();
					$order = $invoices->getOrder();
					$billing = $invoices->getBillingAddress();
					//echo "<pre>";
					//print_r($invoices->getData());exit;
                    /*  Billing Address*/
					$billingAdd 	= $invoices->getBillingAddress()->getData();
					$b_name 		= $billing->getName();
					$b_street1 		= $billing->getStreet(1);
					$b_street2 		= $billing->getStreet(2);
					$b_city 		= $billing->getCity();
					$b_postcode 	= $billing->getPostcode();
					$b_region 		= $billing->getRegion();
					$telephone		= $billingAdd['telephone'];
					$fax_billing	= $billingAdd['fax'];
					$b_country = $billing->getCountry();
				    $b_country=Mage::app()->getLocale()->getCountryTranslation($b_country);
					$b_address = Mage::getModel('customer/address')->load($billingAdd['customer_address_id']);
					
					$shipping = $invoices->getShippingAddress();
					
                    /*  Shipping Address*/
					$shippingAdd 	= $invoices->getShippingAddress()->getData();
					$s_name 		= $shipping->getName();
					$s_street1 		= $shipping->getStreet(1);
					$s_street2		= $shipping->getStreet(2);
					$s_city 		= $shipping->getCity();
					$s_postcode 	= $shipping->getPostcode();
					$s_region 		= $shipping->getRegion();
					$s_telephone		= $shippingAdd['telephone'];
					$s_fax_billing	= $shippingAdd['fax'];
					$s_country = $shipping->getCountry();
					$s_country=Mage::app()->getLocale()->getCountryTranslation($s_country);
					$s_address = Mage::getModel('customer/address')->load($shippingAdd['customer_address_id']);
					
					// order id and date
					$orderId 		= $order->getData('increment_id');//order no
					$orderDate 		= date('F-d-Y', time($order->getData('created_at')));
					
					$pdf->SetFont('helvetica', '', 8);
					$HTML = '<table border="0" style="background-color: #fff;width:100%">
						<tr><td colspan="4" style="text-align:center"><strong>Order Details</strong></td></tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Order id :</strong>'.$orderId.'</td>
							<td colspan="2" style="text-align:right"><strong>Date </strong>'.$orderDate.'</td>
						</tr>
						<tr><td colspan="4" style="text-align:center"></td></tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Billing Address</strong></td>
							<td colspan="2" style="text-align:left"><strong>Shipping Address</strong></td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>Name </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($b_name).'</td>
							<td colspan="1" style="text-align:left"><strong>Name </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($s_name).'</td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>Address1 </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($b_street1).'</td>
							<td colspan="1" style="text-align:left"><strong>Address1 </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($s_street1).'</td>
					    </tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>Address2 </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($b_street2).'</td>
							<td colspan="1" style="text-align:left"><strong>Address2 </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($s_street2).'</td>
					    </tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>City  </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($b_city).'</td>
							<td colspan="1" style="text-align:left"><strong>City  </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($s_city).'</td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>State  </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($b_region).'</td>
							<td colspan="1" style="text-align:left"><strong>State  </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($s_region).'</td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>Post Code  </strong></td>
							<td colspan="1" style="text-align:left">'.$b_postcode.'</td>
							<td colspan="1" style="text-align:left"><strong>Post Code  </strong></td>
							<td colspan="1" style="text-align:left">'.$s_postcode.'</td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>Country  </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($b_country).'</td>
							<td colspan="1" style="text-align:left"><strong>Country  </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords($s_country).'</td>
						</tr>
						<tr><td colspan="4" style="text-align:center"></td></tr>
						<tr>
							<td colspan="4" style="text-align:center"><strong>Product Details</strong></td>
						</tr>
						<tr>
						<td colspan="4">
						<table border="1px" style="background-color: #fff;width:100%;">
						<tr>
							<td colspan="1" style="text-align:left;width: 11%;"><strong>Brand</strong></td>
							<td colspan="1" style="text-align:left;width: 11%;"><strong>US Brand</strong></td>
							<td colspan="1" style="text-align:left;width: 12%;"><strong>Active Ingredients</strong></td>
							<td colspan="1" style="text-align:left;width: 9%;"><strong>Strength</strong></td>
							<td colspan="1" style="text-align:left;width: 10%;"><strong>Package Size</strong></td>
							<td colspan="1" style="text-align:left;width: 8%;"><strong>Bonus</strong></td>
							<td colspan="1" style="text-align:left;width: 9%;"><strong>No Of Packs</strong></td>
							<td colspan="1" style="text-align:left;width: 12%;"><strong>Total Quantity</strong></td>
							<td colspan="1" style="text-align:left;width: 8%;"><strong>Price</strong></td>
							<td colspan="1" style="text-align:left;width: 10%;"><strong>Total</strong></td>
						</tr>';
						//add code FCF5DD
						  /* Add body */
										
										$count = 1;
										foreach ($invoices->getAllItems() as $item){
										    
											if ($item->getOrderItem()->getParentItem()) {
												continue;
											}

											/* Draw item */
										   // $page = $this->_drawItem($item, $page, $order);
										   $itemArr = $item->getData();
										   //echo "<pre>";
										   //print_r($itemArr);exit;
										  $product = Mage::getModel('catalog/product')->load($itemArr['product_id']);
										  $resource = Mage::getSingleton('core/resource');
										  //echo "<pre>";
										  //print_r($product->getData());exit;
     									 //product info
										 
										   $attr = $product->getResource()->getAttribute("pharmaceutical_form");
										   $pharmaceuticalformId = $product->getPharmaceuticalForm();
										   $pharm = $attr->getSource()->getOptionText($pharmaceuticalformId);
										  
										   $itemName = $itemArr['name'];//name
										   //configureble product name
											 $simpleProductId = $item->getProductId(); 
											 $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId); 
											 $config_product = Mage::getModel('catalog/product')->load($parentIds[0]);
										     $itemName=$config_product->getName();
										   //end
										   $p_us_brand = $product->getUsBrandName();
										   $p_genericname = $product->getGenericName();
										   $p_strength = $product->getConfigurableAttribute();
										   $p_bonus = $product->getBonus();
										   $item_sku = explode("-",$itemArr['sku']);  
										   $p_packagesize = trim($item_sku[1])." ".$pharm;
										  
										   $qty = number_format($itemArr['qty']);
										   $total_pills = (trim($item_sku[1]) * $itemArr['qty'])+$p_bonus;
										   $price = "$".sprintf ("%.2f", $itemArr['price']);
										   $total_price = "$".sprintf ("%.2f", $itemArr['row_total_incl_tax']);
										    
										   //$base_price = number_format($itemArr['price']);//indi price
										   //$discountAmount = number_format($itemArr['discount_amount']);
										   //$price = number_format($itemArr['price'] - $itemArr['discount_amount']);
										   //$subtotal = number_format($itemArr['qty']*($itemArr['price'] - $itemArr['discount_amount']));
										   $productcode=$itemArr['sku'];//code
										  
										   $attr =  Mage::getModel('catalog/product')->loadByAttribute('sku',$productcode);
										   
										   //$attrValueColor = $attr->getAttributeText('color');
										   //$attrValueSize = $attr->getAttributeText('size');
										   //$netValue = number_format($itemArr['base_row_total']);
										   //$grossValue =   number_format($itemArr['row_total_incl_tax']);
										   $mrpValue = number_format($itemArr['price']);
										   //$vat_tax = number_format($itemArr['row_total_incl_tax']*$vat /100);
										   //$dis_val = number_format($itemArr['base_discount_amount']);
										   $HTML .='
										   <tr style="text-align:left;">
										   		<td> '.$itemName.' </td>
												<td>'.$p_us_brand.'</td>
												<td>'.$p_genericname.'</td>
												<td>'.$p_strength.'</td>
												<td>'.$p_packagesize.'</td>
												<td>'.$p_bonus.'</td>
												<td>'.$qty.'</td>
												<td>'.$total_pills." ".$pharm.'</td>
												<td>'.$price.'</td>
												<td>'.$total_price.'</td>
												
											</tr>';
											$count++;
								        }
						//end code
						$HTML .='</table> </td></tr>';
						//Pice Detail----------------------
						$order_sub_total = sprintf ("%.2f", $invoices->getSubtotal());
						$shipping_cost = sprintf ("%.2f", $invoices->getBaseShippingAmount());
						//$rebate = round($invoices->getBaseDiscountAmount(),2);
						$product_Total = $order_sub_total + $shipping_cost;
						$product_Total = sprintf ("%.2f", $product_Total);
						$p_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
						$payment_method_code = $p_order->getPayment()->getMethodInstance()->getCode();
						//echo "method--".$payment_method_code;exit;
						if($payment_method_code == "checkmo" || $payment_method_code == "wiretransfer")
						{
							$rebate = "$5.00";
						}
						else
						{
							$rebate = "N/A";
						}
					
						$HTML .='<tr><td colspan="4" style="text-align:center"></td></tr>';
						//$HTML .='<tr><td colspan="4" style="text-align:left"><strong>Price</strong></td></tr>';
						$HTML .='<tr><td colspan="4"><table border="1px" style="background-color: #fff;width:80%">
								<tr>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Order Sub Total</strong></td>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Shipping Cost</strong></td>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Rebate</strong></td>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Order Total</strong></td>
								</tr>
								<tr>
									<td colspan="1" style="text-align:left;width: 20%;">$'.$order_sub_total.'</td>
									<td colspan="1" style="text-align:left;width: 20%;">$25</td>
									<td colspan="1" style="text-align:left;width: 20%;">'.$rebate.'</td>
									<td colspan="1" style="text-align:left;width: 20%;">$'.$product_Total.'</td>
								</tr>
								</table></td></tr>';
								
							//Medical History Detail---------------------------	
							$id = $order->getData('medical_history');
							//echo "id--".$id;exit;
					//if($id != "")
					//{
							$medicalhistoryObj = Mage::getModel('medicalhistory/medicalhistory')->load($id);
							$medical_questObj = Mage::getModel('medicalhistory/medicalquestion');
							//echo "<pre>";
							//print_r($medicalhistoryObj->getData());exit;
							$question = $medicalhistoryObj->getData('questionid');
							$qus = explode(",",$question);
							
						$HTML .='<tr><td colspan="4" style="text-align:center"></td></tr>';	
						$HTML .='<tr><td colspan="4" style="text-align:center"><strong>Medical History</strong></td></tr>';
						$HTML .='<tr><td colspan="4"><table border="1px" style="background-color: #fff;width:100%;">
								<tr>
									<td colspan="1" style="text-align:left;width: 40%;"><strong> </strong></td>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Medicine</strong></td>
									<td colspan="1" style="text-align:left;width: 11%;"><strong>Frequency </strong></td>
									<td colspan="1" style="text-align:left;width: 10%;"><strong>Duration</strong></td>
									<td colspan="1" style="text-align:left;width: 10%;"><strong>Drug Allergies</strong></td>
								</tr>
								<tr>
									<td colspan="1" style="text-align:left;width: 40%;">
									    <table border="1px" style="text-align:left;width: 100%;">
										<tr>';
								$HTML .="<td colspan='1' style='text-align:left;width: 50%;'><strong>Primary Physician's Name :</strong></td>";
								$HTML .='<td colspan="1" style="text-align:left;width: 50%;">'.$medicalhistoryObj->getName().'</td>
										</tr>
										</table>
									</td>
									<td colspan="1" style="text-align:left;width: 20%;">'.$medicalhistoryObj->getProductName().'</td>
									<td colspan="1" style="text-align:left;width: 11%;">'.$medicalhistoryObj->getFrequency().'</td>
									<td colspan="1" style="text-align:left;width: 10%;">'.$medicalhistoryObj->getDuration().'</td>
									<td colspan="1" style="text-align:left;width: 10%;">'.$medicalhistoryObj->getDrugAllergies().'</td>
								</tr>
								<tr>
									<td colspan="5" style="text-align:left;width: 91%;"></td>
								</tr>
								<tr>
									<td colspan="1" style="text-align:left;width: 40%;">
									    <table border="1px" style="text-align:left;width: 100%;">
										<tr>';
								$HTML .="<td colspan='1' style='text-align:left;width: 50%;'><strong>Physician's Telephone No :</strong></td>";
								$HTML .='<td colspan="1" style="text-align:left;width: 50%;">'.$medicalhistoryObj->getPhonenumber().'</td>
										</tr>
										</table>
									</td>
									<td colspan="1" style="text-align:left;width: 20%;">'.$medicalhistoryObj->getProductName().'</td>
									<td colspan="1" style="text-align:left;width: 10%;">'.$medicalhistoryObj->getFrequency().'</td>
									<td colspan="1" style="text-align:left;width: 10%;">'.$medicalhistoryObj->getDuration().'</td>
									<td colspan="1" style="text-align:left;width: 10%;">'.$medicalhistoryObj->getDrugAllergies().'</td>
								</tr>
								
								</table>
								</td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left;"><strong></strong></td>
								</tr>';
						$HTML .='<table border="1px" style="background-color: #fff;width:100%">
						       
								<tr>
								    <td colspan="1" style="text-align:left;width:50%">
										<table border="1px" style="background-color: #fff;width:100%">';
										for($i=1;$i<=7;$i++)
										{
											$medical_questObj = Mage::getModel('medicalhistory/medicalquestion')->load($i);
											$quest[$i] = $medical_questObj->getData('question');
											$HTML .='<tr>
												 <td colspan="1" style="text-align:left;width:50%">'.$quest[$i].'</td>';
												 for($j=0;$j<sizeof($qus);$j++){
													if($i == $qus[$j]){
													$ans="Yes";break;
													}else 
													{$ans= "No";}
												 }
											$HTML .='<td colspan="1" style="text-align:left;width:50%">'.$ans.'</td>
												</tr>';
										}
											
										$HTML .='</table>
									</td >
									<td colspan="1" style="text-align:left;width:50%">
										<table border="1px" style="background-color: #fff;width:100%">';
										for($i=8;$i<=14;$i++)
										{
											$medical_questObj = Mage::getModel('medicalhistory/medicalquestion')->load($i);
											$quest[$i] = $medical_questObj->getData('question');
											$HTML .='<tr>
														<td colspan="1" style="text-align:left;width:50%">'.$quest[$i].'</td>';
												 for($j=0;$j<sizeof($qus);$j++){
													if($i == $qus[$j]){
													$ans="Yes";break;
													}else 
													{$ans= "No";}
												 }
											$HTML .='<td colspan="1" style="text-align:left;width:50%">'.$ans.'</td>
													</tr>';
										}
										
									$HTML .='</table>
									</td>
								</tr>
									
								<tr>
									<td colspan="2" style="text-align:left;"><strong>If you have selected "Yes" to any of the above fields,please enter more detail below.</strong></td>
								</tr>
								 <tr>
									<td colspan="2" style="text-align:left;">'.$medicalhistoryObj->getOptionText().'</td>
								 </tr>
								</table>
								<tr>
								<tr><td colspan="4" style="text-align:center"></td></tr>
								<td colspan="4" style="text-align:left;">
								<strong>I certify that I am over 18 years and that I am under the supervision of a doctor. The ordered medication is for my own personal use and is
									strictly not meant for a re-sale. I also accept that I am taking the medicine /s at	my own risk and that I am duly aware of all the effects / side effects of
									the medicine / s. I acknowledge that the drug is as per the norms of the country of destination.</strong>
								</td>
								</tr>';
					//}
								
								
						$HTML .='</table>';
						//echo $HTML;exit;
					if ($invoices->getStoreId()) {
						Mage::app()->getLocale()->revert();
					}
					/*echo "<pre>";
					print_r($shipping->getData());exit;*/
					$pdf->writeHTML($HTML, true, false, false, false, '');
					
//print pdf code for shipment detail---------------------------------

					$rel_orderId = $order->getId();
					$shipment = Mage::getResourceModel('sales/order_shipment_collection')
								->setOrderFilter($rel_orderId)
								->load();
					
				$pdf->AddPage();
				foreach ($shipment as $shipments) 
				{
					if ($shipments->getStoreId()) {
						Mage::app()->getLocale()->emulate($shipments->getStoreId());
						Mage::app()->setCurrentStore($shipments->getStoreId());
					}
					$order = $shipments->getOrder();
						$invoice_no = '&nbsp';
						$invoice_Date = '&nbsp;';
						
					if($order->hasInvoices()) {
					// "$_eachInvoice" is each of the Invoice object of the order "$order"
						foreach ($order->getInvoiceCollection() as $_eachInvoice) {
							$invoice_no = $_eachInvoice->getData('increment_id');
							$invoice_Date =date('d/m/Y', time($_eachInvoice->getData('created_at')));
						}
					}
					$order_date = date('d/m/Y', time($order->getData('created_at')));
					$trackArr = $shipments->getTracksCollection()->getData();
						//$carrier_code = $trackArr[0]['carrier_code'];
						$track_title = $trackArr[0]['title']; 
						$track_number = $trackArr[0]['track_number']; 
						
						$shipping_add = $shipments->getShippingAddress();
						
						$shippingAdd 		= $shipments->getShippingAddress()->getData();
						$c_name 				= $shippingAdd['firstname'].' '.$shippingAdd['lastname'];
						$filename = $shippingAdd['firstname'].$shippingAdd['lastname'];
						$street1 					= $shipping_add->getStreet(1);
						$street2 					= $shipping_add->getStreet(2);
						$city 						= $shippingAdd['city'];
						$postcode 			= $shippingAdd['postcode'];
						$region 					= $shippingAdd['region'];
						$telephone			= $shippingAdd['telephone'];
						$fax				= $shippingAdd['fax'];
						$country = $shippingAdd['country_id'];
						$country=Mage::app()->getLocale()->getCountryTranslation($country);
						$address = Mage::getModel('customer/address')->load($shippingAdd['customer_address_id']);
						
						//$landmark_shipping		= $address->getData('landmark');
						//$landline_shipping		= $address->getData('telephonetwo');
						$shippingNo 			= $shipments->getIncrementId();	
						
						$orderId 				= $order->getData('increment_id');
						$shippingDate 		= date('d/m/Y', $shipments->getCreatedAtDate()->getTimestamp());
						//echo "<pre>";
						//print_r($address->getData());exit;
						$pdf->SetFont('helvetica', '', 8);
						$HTML = '<table border="0" style="background-color: #FFF;width:100%">
								<tr>
									<td colspan="1" style="text-align:left;width:40%;"><strong>Checked By-</strong></td>
									<td colspan="1" style="text-align:left;width:30%;"><strong>REF. NO.</strong>'.$shippingNo.'</td>
									<td colspan="1" style="text-align:left;width:30%;"><strong>Order NO.</strong>'.$orderId.'</td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"><strong>Date :</strong>'.$order_date.'</td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"><strong>Order NO.</strong>'.$orderId.'</td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:center"><strong>CUSTOMER REFERENCE GUIDE</strong></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"><strong>US BRAND NAME / S (for reference purpose):</strong></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left"></td>
								</tr>
								<tr>
								<td colspan="4">
								 <table border="1" style="background-color: #Fff;width:100%">
									<tr>
										<td colspan="1" style="text-align:left;width:30%;"><strong>US Brand Name/s </strong></td>
										<td colspan="1" style="text-align:left;width:20%;"><strong>Strength</strong></td>
										<td colspan="1" style="text-align:left;width:20%;"><strong>Quantity</strong></td>
										<td colspan="1" style="text-align:left;width:30%;"><strong>Name of Active Ingredient (generic name)</strong></td>
									</tr>';
								
								
								
					/* Add body */
					$count = 1;
					foreach ($shipments->getAllItems() as $item) {
						if ($item->getOrderItem()->getParentItem()) {
							continue;
						}
						$itemArr = $item->getData();
						$product = Mage::getModel('catalog/product')->load($itemArr['product_id']);
						
						//product detail
						$p_us_brand = $product->getUsBrandName();
						$p_genericname = $product->getGenericName();
						$p_strength = $product->getConfigurableAttribute();
						$p_bonus = $product->getBonus();
						$item_sku = explode("-",$itemArr['sku']);  
						$p_packagesize = trim($item_sku[1]) + $p_bonus;
						//$qty = number_format($itemArr['qty'])."X".$p_packagesize;
						$qty = number_format($itemArr['qty'])."X".$p_packagesize . ' ' . $pharm;
						//Us brand Name Reference----
							$HTML.='<tr>
										<td colspan="1" style="text-align:left">'.$p_us_brand.'</td>
										<td colspan="1" style="text-align:left">'.$p_strength.'</td>
										<td colspan="1" style="text-align:left">'.$qty.'</td>
										<td colspan="1" style="text-align:left">'.$p_genericname.'</td>
									</tr>';
						
						
						/* Draw item */
						//$this->_drawItem($item, $page, $order);
						//$page = end($pdf->pages);
						$count++;
					}
					//mail("", "", "");
					$HTML.='</table>
							</td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left"><strong>EQUIVALENT INDIAN PRODUCTS / S</strong></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align:left">
								<table border="1" style="background-color: #Fff;width:100%">
									<tr>
										<td colspan="1" style="text-align:left;width:30%;"><strong>Equivalent Indian Product/s </strong></td>
										<td colspan="1" style="text-align:left;width:20%;"><strong>Strength</strong></td>
										<td colspan="1" style="text-align:left;width:20%;"><strong>Quantity</strong></td>
										<td colspan="1" style="text-align:left;width:30%;"><strong>Name of Active Ingredient (generic name)</strong></td>
										
									</tr>';
									$count1 = 1;
									foreach ($shipments->getAllItems() as $item) {
										if ($item->getOrderItem()->getParentItem()) {
											continue;
										}
										$itemArr = $item->getData();
										$product = Mage::getModel('catalog/product')->load($itemArr['product_id']);
										
										//product detail
										$p_brand = $product->getName();
										//configureble product name
											 $simpleProductId = $item->getProductId(); 
											 $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId); 
											 $config_product = Mage::getModel('catalog/product')->load($parentIds[0]);
										     $p_brand=$config_product->getName();
										   //end
										$p_genericname = $product->getGenericName();
										$p_strength = $product->getConfigurableAttribute();
										$p_bonus = $product->getBonus();
										$item_sku = explode("-",$itemArr['sku']);  
										$p_packagesize = trim($item_sku[1]) + $p_bonus;
										//$qty = number_format($itemArr['qty'])."X".$p_packagesize;
										$qty = number_format($itemArr['qty'])."X".$p_packagesize . ' ' . $pharm;
										//Indian brand Name Reference----
											$HTML.='<tr>
														<td colspan="1" style="text-align:left">'.$p_brand.'</td>
														<td colspan="1" style="text-align:left">'.$p_strength.'</td>
														<td colspan="1" style="text-align:left">'.$qty.'</td>
														<td colspan="1" style="text-align:left">'.$p_genericname.'</td>
													</tr>';
										
										
										/* Draw item */
										//$this->_drawItem($item, $page, $order);
										//$page = end($pdf->pages);
										$count1++;
									}
					$HTML.='</table>
							</td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>Name of Patient and Address:-</strong></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>'.ucwords($c_name).'</strong></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>'.ucwords($street1).'</strong></td>
							</tr>
							';
							if($street2 != "")
							{
							
							$HTML.='<tr>
										<td colspan="3" style="text-align:left"><strong>'.ucwords($street2).'</strong></td>
									</tr>';
							}
					 $HTML.='<tr>
								<td colspan="3" style="text-align:left"><strong>'.ucwords($city).'</strong><strong>,'.ucwords($region).' </strong><strong> '.$postcode.'</strong></td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:left"><strong>'.ucwords($country).'</strong></td>
							</tr>
						</table>';
						
					$pdf->writeHTML($HTML, true, false, false, false, '');
				}
								
			}
					
				
		}
		
		
		// $pdf->writeHTML($HTML, true, false, false, false, '');

		//Close and output PDF document
		//$fileName = 'invoice-'.date('d_m_Y').'.pdf';
		
		$fileName = 'invoice_reference.pdf';
		$pdf->Output($fileName, 'D');		
        return $pdf;   
}
    
}
