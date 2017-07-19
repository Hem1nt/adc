<?php

/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');
include_once 'app/code/core/Mage/Sales/Model/Order/Pdf/Invoice.php';
class Iksula_Orderstatechange_Model_Sales_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{

    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoiceArray = array())
    {

		$this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        // create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// $pdf->getCreatedAt();
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
		$pdf->SetMargins(15,10,15,TRUE);
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
			  $pdfsession = Mage::getSingleton('core/session')->getPdfsession();
			  //echo "asd--".$pdfsession;exit;
			  if($pdfsession != "1")
			  {
				$invoice = $invoiceArray;
			  }
		    foreach ($invoice as $invoices) {
					if ($invoice->getStoreId()) {
						Mage::app()->getLocale()->emulate($invoice->getStoreId());
						Mage::app()->setCurrentStore($invoice->getStoreId());
					}
					$pdf->AddPage();
					$order = $invoices->getOrder();

					// $ipaddress = $order->getData('remote_ip');

					$x_forwarded_for_ip = $order->getData('x_forwarded_for');
					$ipaddress = explode(',',$x_forwarded_for_ip);

					$billing = $invoices->getBillingAddress();
					$bill_company_name = $billing->getCompany();
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
					$ship_company_name = $shipping->getCompany();
                    /*  Shipping Address*/
					$shippingAdd 	= $invoices->getShippingAddress()->getData();
					$s_name 		= $shipping->getName();
					$s_street1 		= $shipping->getStreet(1);
					$s_street2 		= $shipping->getStreet(2);
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

					if($order->getData('customer_order_increment_id')) {
						$customer_order_increment_id = $order->getData('customer_order_increment_id');
					}else {
						$customer_order_increment_id = '';
					}

					$pdf->SetFont('helvetica', '', 8);
					$HTML = 'Invoice<br/><br/><br/><br/><table border="0" style="background-color: #fff;width:100%">
						<tr><td colspan="4" style="text-align:center"><strong>Order Details</strong></td></tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Order id :</strong>'.$orderId.'</td>
							<td colspan="2" style="text-align:right"><strong>Date  </strong>'.$orderDate.'</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Custom Order id :</strong>'.$customer_order_increment_id.'</td>
						</tr>
						<tr><td colspan="4" style="text-align:center"></td></tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Billing Address</strong></td>
							<td colspan="2" style="text-align:left"><strong>Shipping Address</strong></td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>Name </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords(strtolower($b_name)).'</td>
							<td colspan="1" style="text-align:left"><strong>Name </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords(strtolower($s_name)).'</td>
						</tr>';
						$bill_option =$bill_company_name ? $bill_company_name : "N/A";
						$ship_option =$ship_company_name ? $ship_company_name : "N/A";
						if($bill_company_name!='' || $ship_company_name!=''):
						$HTML .= '
							<tr>
							<td colspan="1" style="text-align:left">
									<strong>Company Name </strong>
							</td>
							<td colspan="1" style="text-align:left">'.$bill_option.'</td>
							<td colspan="1" style="text-align:left">
									<strong>Company Name </strong>
							</td>
							<td colspan="1" style="text-align:left">'.$ship_option.'</td>
							</tr>
							';
						endif;

						$HTML .= '<tr>
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
							<td colspan="4" style="text-align:center"></td>
						</tr>
						<tr>
						<td colspan="4">
						<table border="1px" style="background-color: #fff;width:100%;">
						<tr>
							<td colspan="1" style="width:100px;text-align:left;"><strong>Brand</strong></td>
							<td colspan="1" style="text-align:left;width: 11%;"><strong>US Brand</strong></td>
							<td colspan="1" style="text-align:left;width: 12%;"><strong>Active Ingredients</strong></td>
							<td colspan="1" style="text-align:left;width: 9%;"><strong>Strength</strong></td>
							<td colspan="1" style="text-align:left;width: 12%;"><strong>Package Size</strong></td>
							<td colspan="1" style="text-align:left;width: 8%;"><strong>Bonus</strong></td>
							<td colspan="1" style="text-align:left;width: 7%;"><strong>No Of Packs</strong></td>
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

										  $product = Mage::getModel('catalog/product')->load($itemArr['product_id']);
										  $resource = Mage::getSingleton('core/resource');
										  // echo "<pre>";
										  // print_r($product->getData());exit;
     									 // product info

										   $attr = $product->getResource()->getAttribute("pharmaceutical_form");
										   $pharmaceuticalformId = $product->getPharmaceuticalForm();
										   $pharm = $attr->getSource()->getOptionText($pharmaceuticalformId);

										   $itemName = $itemArr['name'];//name

										   //configureble product name
											 $simpleProductId = $item->getProductId();
											 $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId);
											 $config_product = Mage::getModel('catalog/product')->load($parentIds[0]);

											 if(strpos($itemName,'-') !== false){
										   	  	$itemNameArray=explode('-', $itemName);
										     	$itemName = $itemNameArray[0];
										   	 }
										    if($itemName==''){
										    	  $itemName=$config_product->getName();
										    }


										   $p_us_brand = $product->getUsBrandName();
										   $p_genericname = $product->getGenericName();
										   $p_strength = $product->getConfigurableAttribute();
										   $newPackSize = $product->getAttributeText('pack_size');
										   $item_sku = explode("-",$itemArr['sku']);
										   $newPackSizeExplode = explode("+", $newPackSize);
                						   $pack_size = array_sum($newPackSizeExplode);
											
										   $parent_sku = $item_sku[0];
										   $strength = $p_strength;

											$prod = Mage::getModel('catalog/product')->loadByAttribute('sku', $parent_sku);
											if($prod){
												$parentProd = $prod->getData();
												$strength = $parentProd['configurable_attribute'];
												if(empty($strength)) {
													$strength = "";
												}
											}
											
										   # end

										   if($product->getBonus() != "")
										   {
										     $p_bonus = $item_sku[1];
										   }
										   else
										   {
										     $p_bonus = 0;
										   }
										   if(strpos($newPackSize, '+') !== false)
										   {
										   $p_packagesize = $pack_size;
										   }else{
										   $p_packagesize = trim($item_sku[1])." ".$pharm;
										   }

										   $qty = number_format($itemArr['qty']);
										   //$total_pills = (trim($item_sku[1]) + $p_bonus) * $itemArr['qty'];
										   if(strpos($newPackSize, '+') !== false)
										   {
										   $total_pills = ($p_packagesize + $p_bonus) * $itemArr['qty'];
										   }else{
										   $total_pills = (trim($item_sku[1]) + $p_bonus) * $itemArr['qty'];
										   }
										   $price = "$".sprintf ("%.2f", $itemArr['price']);
										   $total_price = "$".sprintf ("%.2f", $itemArr['row_total_incl_tax']);							
										   $productcode=$itemArr['sku'];//code

										   $attr =  Mage::getModel('catalog/product')->loadByAttribute('sku',$productcode);
										   $mrpValue = number_format($itemArr['price']);
										
										   if($product->getData('type_id')=='bundle'){
										   		$total_pills = $qty;
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
										   }

										   $HTML .='
										   <tr style="text-align:left;">
										   		<td style="width:100px;vertical-align:bottom;"> '.$itemName.' - '.$strength.'</td>
												<td style="vertical-align:bottom;">'.$p_us_brand.'</td>
												<td style="vertical-align:bottom;">'.$p_genericname.'</td>
												<td style="vertical-align:bottom;">'.$strength.'</td>
												<td style="vertical-align:bottom;">'.$p_packagesize.'</td>
												<td style="vertical-align:bottom;">'.$p_bonus.'</td>
												<td style="vertical-align:bottom;">'.$qty.'</td>
												<td style="vertical-align:bottom;">'.$total_pills." ".$pharm.'</td>
												<td style="vertical-align:bottom;">'.$price.'</td>
												<td style="vertical-align:bottom;">'.$total_price.'</td>

											</tr>';
											$count++;
								        }
								       //  exit;
						//end code
						$HTML .='</table> </td></tr>';
						//Pice Detail----------------------
						// echo '<pre>';
						// print_r($invoices->getData());
						// exit;
						$order_sub_total = sprintf ("%.2f", $invoices->getSubtotal());
						$shipping_cost = sprintf ("%.2f", $invoices->getBaseShippingAmount());
						$product_Total = $invoices->getSubtotal();
						//$rebate = round($invoices->getBaseDiscountAmount(),2);
						// $product_Total = $order_sub_total + $shipping_cost;
						$product_Total = sprintf ("%.2f", $product_Total);
						$p_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
						$payment_method_code = $p_order->getPayment()->getMethodInstance()->getCode();
						//echo "method--".$payment_method_code;exit;
						$subtotal = $invoices->getSubtotal();
						$discount = $invoices->getDiscountAmount();
						$grand_total =  sprintf ("%.2f",$invoices->getGrandTotal());
						$currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
						$totalExcludingShipping = $subtotal-$discount;

						// if($payment_method_code == "checkmo" || $payment_method_code == "wiretransfer")
						// {
						// 	$rebate = "$5.00";
						// }
						// else
						// {
							if($discount== 0){
							    $rebate = "N/A";
							}
							else{
								// $rebate = Mage::helper('core')->currency($discount,true,false);
							 	$rebate = '$'.sprintf ("%.2f",$discount);
						    }
						// }
						$order_shipping = $this->getPriceFormat($invoices->getShippingAmount());

						$HTML .='<tr><td colspan="4" style="text-align:center"></td></tr>';
						//$HTML .='<tr><td colspan="4" style="text-align:left"><strong>Price</strong></td></tr>';
						$HTML .='<tr><td colspan="4"><table border="1px" style="background-color: #fff;width:80%">
								<tr>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Order Sub Total</strong></td>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Rebate</strong></td>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Shipping & Handling</strong></td>
									<td colspan="1" style="text-align:left;width: 20%;"><strong>Order Total</strong></td>
								</tr>
								<tr>
									<td colspan="1" style="text-align:left;width: 20%;">$'.$order_sub_total.'</td>
									<td colspan="1" style="text-align:left;width: 20%;">'.$rebate.'</td>
									<td colspan="1" style="text-align:left;width: 20%;">'.$order_shipping.'</td>
									<td colspan="1" style="text-align:left;width: 20%;">$'.$grand_total.'</td>
								</tr>
								</table></td></tr>';

							//Medical History Detail---------------------------
								$physicianname = $order->getData('physicianname');
								$physiciantelephone = $order->getData('physiciantelephone');
								$drug_allergies = $order->getData('drug_allergies');
								$current_medications = $order->getData('current_medications');
								$current_treatments = $order->getData('current_treatments');
								$smoke = $order->getData('smoke');
								$drink = $order->getData('drink');
								if($physicianname==''){$physicianname='-';}
								if($physiciantelephone==''){$physiciantelephone='-';}
								if($drug_allergies==''){$drug_allergies='No';}
								if($current_medications==''){$current_medications='No';}
								if($current_treatments==''){$current_treatments='No';}
								if($smoke==''){$smoke='No';}
								if($drink==''){$drink='No';}

								$HTML .='
								<tr>
									<td colspan="6"></td>
								</tr>
								<tr>
									<td colspan="6"></td>
								</tr>
								<tr>
									<td colspan="6"></td>
								</tr>
								<tr>
									<td colspan="6" align="center"><b>Medical Conditions</b></td>
								</tr>

								<tr>
									<td colspan="6"></td>
								</tr>
								<table border="1" cellspacing="0" cellpadding="10" >

								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Physicians Name :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$physicianname.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Physicians Telephone No :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$physiciantelephone.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Drug Allergies :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$drug_allergies.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Current Medications :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$current_medications.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Current Treatments :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$current_treatments.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Do you Smoke ?</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$smoke.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Do you Drink ?</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$drink.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>IP Address at which acceptance was recorded  :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$ipaddress[0].'</td>
								</tr>
								</table>
								<p align="justify">
									I certify that I am '."'over 18 years'".' and that I am under the supervision of a doctor.
									The ordered medication is for my own personal use and is strictly not meant for a re-sale.
									I also accept that I am taking the medicine /s at my own risk and that I am duly aware of
									all the effects / side effects of the medicine / s. If my order contain Tadalafil,
									I confirm that the same is not meant for consumption in the USA. I acknowledge that the drugs
									are as per the norms of the country of destination.
								</p>
								';

								//exit;


						$HTML .='</table>';
						//echo $HTML; exit;
					if ($invoices->getStoreId()) {
						Mage::app()->getLocale()->revert();
					}
					/*echo "<pre>";
					print_r($shipping->getData());exit;*/
					$pdf->writeHTML($HTML, true, false, false, false, '');
			}


		}
			Mage::getSingleton('core/session')->unsPdfsession();

		// $pdf->writeHTML($HTML, true, false, false, false, '');

		//Close and output PDF document
		//$fileName = 'invoice-'.date('d_m_Y').'.pdf';

		$fileName = 'invoice.pdf';
		$pdf->Output($fileName, 'D');
        return $pdf;
    }

    public function getInvoicePdf($invoiceArray = array())
    {

		$this->_beforeGetPdf();
        $this->_initRenderer('invoice');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetTitle('Invoice');
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(15,10,15,TRUE);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setLanguageArray($l);
		$pdf->SetFont('helvetica', 'B', 20);
		$this->_setPdf($pdf);
		foreach ($invoiceArray as $invoice)
		{
			  $pdfsession = Mage::getSingleton('core/session')->getPdfsession();
			  if($pdfsession != "1"){
				$invoice = $invoiceArray;
			  }
		    foreach ($invoice as $invoices) {
					if ($invoices->getStoreId()) {
						Mage::app()->getLocale()->emulate($invoices->getStoreId());
						Mage::app()->setCurrentStore($invoices->getStoreId());
					}
					$pdf->AddPage();
					$order = $invoices->getOrder();

					// $ipaddress = $order->getData('remote_ip');

					$x_forwarded_for_ip = $order->getData('x_forwarded_for');
					$ipaddress = explode(',',$x_forwarded_for_ip);

					$billing = $invoices->getBillingAddress();
					$bill_company_name = $billing->getCompany();
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
					$ship_company_name = $shipping->getCompany();
                    /*  Shipping Address*/
					$shippingAdd 	= $invoices->getShippingAddress()->getData();
					$s_name 		= $shipping->getName();
					$s_street1 		= $shipping->getStreet(1);
					$s_street2 		= $shipping->getStreet(2);
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

					if($order->getData('customer_order_increment_id')) {
						$customer_order_increment_id = $order->getData('customer_order_increment_id');
					}else {
						$customer_order_increment_id = '';
					}

					$pdf->SetFont('helvetica', '', 8);
					$HTML = 'Invoice<br/><br/><br/><br/><table border="0" style="background-color: #fff;width:100%">
						<tr><td colspan="4" style="text-align:center"><strong>Order Details</strong></td></tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Order id :</strong>'.$orderId.'</td>
							<td colspan="2" style="text-align:right"><strong>Date  </strong>'.$orderDate.'</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Custom Order id :</strong>'.$customer_order_increment_id.'</td>
						</tr>
						<tr><td colspan="4" style="text-align:center"></td></tr>
						<tr>
							<td colspan="2" style="text-align:left"><strong>Billing Address</strong></td>
							<td colspan="2" style="text-align:left"><strong>Shipping Address</strong></td>
						</tr>
						<tr>
							<td colspan="1" style="text-align:left"><strong>Name </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords(strtolower($b_name)).'</td>
							<td colspan="1" style="text-align:left"><strong>Name </strong></td>
							<td colspan="1" style="text-align:left">'.ucwords(strtolower($s_name)).'</td>
						</tr>';
						$bill_option =$bill_company_name ? $bill_company_name : "N/A";
						$ship_option =$ship_company_name ? $ship_company_name : "N/A";
						if($bill_company_name!='' || $ship_company_name!=''):
						$HTML .= '
							<tr>
							<td colspan="1" style="text-align:left">
									<strong>Company Name </strong>
							</td>
							<td colspan="1" style="text-align:left">'.$bill_option.'</td>
							<td colspan="1" style="text-align:left">
									<strong>Company Name </strong>
							</td>
							<td colspan="1" style="text-align:left">'.$ship_option.'</td>
							</tr>
							';
						endif;

						$HTML .= '<tr>
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
							<td colspan="4" style="text-align:center"></td>
						</tr>
						<tr>
						<td colspan="4">
						<table border="1px" style="background-color: #fff;width:100%;">
						<tr>
							<td colspan="1" style="width:100px;text-align:left;"><strong>Brand</strong></td>
							<td colspan="1" style="text-align:left;width: 11%;"><strong>US Brand</strong></td>
							<td colspan="1" style="text-align:left;width: 12%;"><strong>Active Ingredients</strong></td>
							<td colspan="1" style="text-align:left;width: 9%;"><strong>Strength</strong></td>
							<td colspan="1" style="text-align:left;width: 12%;"><strong>Package Size</strong></td>
							<td colspan="1" style="text-align:left;width: 8%;"><strong>Bonus</strong></td>
							<td colspan="1" style="text-align:left;width: 7%;"><strong>No Of Packs</strong></td>
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
										   $itemArr = $item->getData();

										  $product = Mage::getModel('catalog/product')->load($itemArr['product_id']);
										  $resource = Mage::getSingleton('core/resource');

										   $attr = $product->getResource()->getAttribute("pharmaceutical_form");
										   $pharmaceuticalformId = $product->getPharmaceuticalForm();
										   $pharm = $attr->getSource()->getOptionText($pharmaceuticalformId);

										   $itemName = $itemArr['name'];//name

										   //configureble product name
											 $simpleProductId = $item->getProductId();
											 $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($simpleProductId);
											 $config_product = Mage::getModel('catalog/product')->load($parentIds[0]);

											 if(strpos($itemName,'-') !== false){
										   	  	$itemNameArray=explode('-', $itemName);
										     	$itemName = $itemNameArray[0];
										   	 }
										    if($itemName==''){
										    	  $itemName=$config_product->getName();
										    }


										   $p_us_brand = $product->getUsBrandName();
										   $p_genericname = $product->getGenericName();
										   $p_strength = $product->getConfigurableAttribute();
										   $newPackSize = $product->getAttributeText('pack_size');
										   $item_sku = explode("-",$itemArr['sku']);
										   $newPackSizeExplode = explode("+", $newPackSize);
                						   $pack_size = array_sum($newPackSizeExplode);
											
										   $parent_sku = $item_sku[0];
										   $strength = $p_strength;

											$prod = Mage::getModel('catalog/product')->loadByAttribute('sku', $parent_sku);
											if($prod){
												$parentProd = $prod->getData();
												$strength = $parentProd['configurable_attribute'];
												if(empty($strength)) {
													$strength = "";
												}
											}
											
										   # end

										   if($product->getBonus() != "")
										   {
										     $p_bonus = $item_sku[1];
										   }
										   else
										   {
										     $p_bonus = 0;
										   }
										   if(strpos($newPackSize, '+') !== false)
										   {
										   $p_packagesize = $pack_size;
										   }else{
										   $p_packagesize = trim($item_sku[1])." ".$pharm;
										   }
										   //$p_packagesize = trim($item_sku[1])." ".$pharm;

										   $qty = number_format($itemArr['qty']);
										   if(strpos($newPackSize, '+') !== false)
										   {
										   $total_pills = ($p_packagesize + $p_bonus) * $itemArr['qty'];
										   }else{
										   $total_pills = (trim($item_sku[1]) + $p_bonus) * $itemArr['qty'];
										   }
										   $price = "$".sprintf ("%.2f", $itemArr['price']);
										   $total_price = "$".sprintf ("%.2f", $itemArr['row_total_incl_tax']);							
										   $productcode=$itemArr['sku'];//code

										   $attr =  Mage::getModel('catalog/product')->loadByAttribute('sku',$productcode);
										   $mrpValue = number_format($itemArr['price']);
										
										   if($product->getData('type_id')=='bundle'){
										   		$total_pills = $qty;
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
										   }

										   $HTML .='
										   <tr style="text-align:left;">
										   		<td style="width:100px;vertical-align:bottom;"> '.$itemName.' - '.$strength.'</td>
												<td style="vertical-align:bottom;">'.$p_us_brand.'</td>
												<td style="vertical-align:bottom;">'.$p_genericname.'</td>
												<td style="vertical-align:bottom;">'.$strength.'</td>
												<td style="vertical-align:bottom;">'.$p_packagesize.'</td>
												<td style="vertical-align:bottom;">'.$p_bonus.'</td>
												<td style="vertical-align:bottom;">'.$qty.'</td>
												<td style="vertical-align:bottom;">'.$total_pills." ".$pharm.'</td>
												<td style="vertical-align:bottom;">'.$price.'</td>
												<td style="vertical-align:bottom;">'.$total_price.'</td>

											</tr>';
											$count++;
								        }
								       //  exit;
						//end code
						$HTML .='</table> </td></tr>';
						$order_sub_total = sprintf ("%.2f", $invoices->getSubtotal());
						$shipping_cost = sprintf ("%.2f", $invoices->getBaseShippingAmount());
						$product_Total = $invoices->getSubtotal();
						$product_Total = sprintf ("%.2f", $product_Total);
						$p_order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
						$payment_method_code = $p_order->getPayment()->getMethodInstance()->getCode();
						//echo "method--".$payment_method_code;exit;
						$subtotal = $invoices->getSubtotal();
						$discount = $invoices->getDiscountAmount();
						$grand_total =  $invoices->getGrandTotal();
						$currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
						$totalExcludingShipping = $subtotal-$discount;

						if($discount== 0){
							$rebate = "N/A";
						}else{
							$rebate = $this->getPriceFormat($discount);
							// $rebate = '$'.sprintf ("%.2f",$discount);
						}

						$order_shipping = $this->getPriceFormat($invoices->getShippingAmount());

						$HTML .='<tr><td colspan="4" style="text-align:center"></td></tr>';
						//$HTML .='<tr><td colspan="4" style="text-align:left"><strong>Price</strong></td></tr>';
						$HTML .='<tr><td colspan="4"><table border="1px" style="background-color: #fff;width:80%">
								<tr>
									<td colspan="4" style="text-align:left;width: 30%;font-size:16px;"><strong>Order Total</strong></td>
								</tr>
								<tr>
									<td colspan="4" style="text-align:left;width: 30%;;font-size:16px;">$'.$order_sub_total.'</td>
								</tr>
								</table>
								</td>
								</tr>';

							//Medical History Detail---------------------------
								$physicianname = $order->getData('physicianname');
								$physiciantelephone = $order->getData('physiciantelephone');
								$drug_allergies = $order->getData('drug_allergies');
								$current_medications = $order->getData('current_medications');
								$current_treatments = $order->getData('current_treatments');
								$smoke = $order->getData('smoke');
								$drink = $order->getData('drink');
								if($physicianname==''){$physicianname='-';}
								if($physiciantelephone==''){$physiciantelephone='-';}
								if($drug_allergies==''){$drug_allergies='No';}
								if($current_medications==''){$current_medications='No';}
								if($current_treatments==''){$current_treatments='No';}
								if($smoke==''){$smoke='No';}
								if($drink==''){$drink='No';}

								$HTML .='
								<tr>
									<td colspan="6"></td>
								</tr>
								<tr>
									<td colspan="6"></td>
								</tr>
								<tr>
									<td colspan="6"></td>
								</tr>
								<tr>
									<td colspan="6" align="center"><b>Medical Conditions</b></td>
								</tr>

								<tr>
									<td colspan="6"></td>
								</tr>
								<table border="1" cellspacing="0" cellpadding="10" >

								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Physicians Name :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$physicianname.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Physicians Telephone No :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$physiciantelephone.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Drug Allergies :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$drug_allergies.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Current Medications :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$current_medications.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Current Treatments :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$current_treatments.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Do you Smoke ?</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$smoke.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>Do you Drink ?</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$drink.'</td>
								</tr>
								<tr>
									<td colspan="6" style="text-align:left;width: 40%;"><strong>IP Address at which acceptance was recorded  :</strong></td>
									<td colspan="6" style="text-align:left;width: 60%;">'.$ipaddress[0].'</td>
								</tr>
								</table>
								<p align="justify">
									I certify that I am '."'over 18 years'".' and that I am under the supervision of a doctor.
									The ordered medication is for my own personal use and is strictly not meant for a re-sale.
									I also accept that I am taking the medicine /s at my own risk and that I am duly aware of
									all the effects / side effects of the medicine / s. If my order contain Tadalafil,
									I confirm that the same is not meant for consumption in the USA. I acknowledge that the drugs
									are as per the norms of the country of destination.
								</p>
								';

					$HTML .='</table>';
	
					if ($invoices->getStoreId()) {
						Mage::app()->getLocale()->revert();
					}
					$pdf->writeHTML($HTML, true, false, false, false, '');
			}


		}
		Mage::getSingleton('core/session')->unsPdfsession();

		// $fileName = 'invoice.pdf';
		$fileName = 'invoice-'.date('d_m_Y').'.pdf';
		$pdf->Output($fileName, 'D');
        return $pdf;
    }

    public function getPriceFormat($_price)
    {
    	$formattedPrice = Mage::helper('core')->currency($_price, true, false);
    	return str_replace("US","",$formattedPrice);
    }


}
