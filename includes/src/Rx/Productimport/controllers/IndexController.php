<?php
 
class Rx_Productimport_IndexController extends Mage_Core_Controller_Front_Action{
    
	public $attrSetId = 4;
	public $i;
	public $simpleProducts = array(); 
	public $lowestPrice;
	public $configurable_attribute = "pack_size"; 
	public $attr_id = 140; 
	public $magento_categories = array(20);
	public $tempSku; // code add 
	
	public function IndexAction() {
    
	
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout(); 
	  
    }
	
	public function importProductsAction()
	{
	
	 //echo "u r here";exit;
		  $databasetable = "sample";
		  $fieldseparator = ",";
		  $lineseparator = "\n";
		  $csvfile = "product-import.csv";
		  echo '<pre>';
		  $row = 0;
			if (($handle = fopen($csvfile, "r")) !== FALSE) {
				$products = array();
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					
					$this->i++;
					$num = count($data);
					//echo "<p> $num fields in line $row: <br /></p>\n";
					$row++; 
					//echo"<pre>";
					//print_r($data);exit;
				//if($row > 6){print_r($products);exit;}
				
					if($row > 2){
						
						if($data[1] != ''){
							if(!empty($products['configurable'])){
							
								if(!is_object(Mage::getSingleton('catalog/product')->loadByAttribute('sku',$products['configurable']['sku']))){
									//echo "sdg--".Mage::getSingleton('catalog/product')->loadByAttribute('sku',$products['configurable']['sku'])->getSelect();
									//echo '<pre>'; print_r($products['configurable']);exit;
									echo 'Data Collected. Starting Creation...<br />';
									$this->createSimpleProduct($products['configurable']);
									echo 'Simple Created <br />';
									$this->createConfigurableProduct($products,$data);
									echo 'Configurable Created <br />';
								}else{
									echo 'SKU:'.$products['configurable']['sku'].' : ==> Already Exist, going to next <br />'; 
								}	
								
								echo 'SKU:'.$products['configurable']['sku'].' : ==> done <br />
								===================================NEXT=====================================
								<br />'; //exit;
								//reset variables
								$this->i = 0;
								$this->simpleProducts = array();
								$this->lowestPrice = 99999999;
								$products = array();
								
							}
							echo 'SKU:'.$data[1].' : ==> Collecting Configurable data set  <br />';
							$products['configurable'] = $this->getConfigurable($data);
							echo 'SKU:'.$data[1].' : ==> Collecting simples '.$data[1].'- option: '.$data[3].' <br />';
							$products['configurable']['simple'][] = $this->getSimple($data, $products['configurable']);
						}else{
							echo 'SKU:'.$products['configurable']['sku'].' : ==> Collecting simples '.$data[1].'- option : '.$data[3].' <br />';
							$products['configurable']['simple'][] = $this->getSimple($data, $products['configurable']);
						}

					}
				}
				//print_r($this->_ordersToCreate);exit; 
				fclose($handle);
			}
			
		
		echo 'Complete';	
	}
	
	public function getOptionId($attr, $attr_value){
		
		$configurableAttributeOptionId = $this->getAttributeOptionValue($attr, $attr_value); 
		if (!$configurableAttributeOptionId) { 
			$configurableAttributeOptionId = $this->addAttributeOption($attr, $attr_value); 
		}  
		return $configurableAttributeOptionId;
	}
	
	public function createSimpleProduct($main_product_data){
		// There's some more advanced logic above the foreach loop which determines how to define $configurable_attribute, 
		// which is beyond the scope of this article. For reference purposes, I'm hard coding a value for 
		// $configurable_attribute here, and it's associated numerical attribute ID... 
		
		$this->lowestPrice = 999999;
		
		//echo 'here';exit;
		// Loop through a pre-populated array of data gathered from the CSV files (or database) of old system.. 
		foreach ($main_product_data['simple'] as $simple_product_data) { 
			
			// check the minprice for configuirable options
			if ($simple_product_data['price'] < $this->lowestPrice) { 
				$this->lowestPrice = $simple_product_data['price']; 
			} 
		
			// Again, I have more logic to determine these fields, but for clarity, I'm still including the variables here hardcoded.. 
			$attr_value = trim($simple_product_data['pack_size']); 
			$this->attr_id = 140;   
			// We need the actual option ID of the attribute value ("XXL", "Large", etc..) so we can assign it to the product model later.. 
			// The code for getAttributeOptionValue and addAttributeOption is part of another article (linked below this code snippet) 
			
			$configurableAttributeOptionId = $this->getAttributeOptionValue($this->configurable_attribute, $attr_value); 
			if (!$configurableAttributeOptionId) { 
				$configurableAttributeOptionId = $this->addAttributeOption($this->configurable_attribute, $attr_value); 
			}  
			if(!is_object(Mage::getSingleton('catalog/product')->loadByAttribute('sku',$simple_product_data['sku']. " - " . $attr_value))){
				//print_r($configurableAttributeOptionId);exit;
				// Create the Magento product model 
				

				$manufacturer = $this->getOptionId('manufacturer',$main_product_data['manufacturer']);
				$generic_code = $this->getOptionId('generic_code',$main_product_data['generic_code']);
				$pharmaceutical_form = $this->getOptionId('pharmaceutical_form',$main_product_data['pharmaceutical_form']);
				$active_ingridients = $this->getOptionId('active_ingridients',$main_product_data['active_ingridients']);
				//$licence_holder = $this->getOptionId('licence_holder',$main_product_data['licence_holder']);
				$us_brand_name = $main_product_data['us_brand_name'];
				//$origin = $this->getOptionId('origin',$main_product_data['origin']);
				
				//$source_country = $this->getOptionId('source_country',$main_product_data['source_country']);
				$strength = $main_product_data['configurable_attribute'];
				$brand_code = $main_product_data['brand_code'];
				$bonus = $main_product_data['bonus'];
				
				
				$sProduct = Mage::getModel('catalog/product'); 
				$sProduct ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) 
						->setWebsiteIds(array(1)) 
						->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED) 	
						->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) 
						->setTaxClassId(0) 
						->setAttributeSetId($this->attrSetId) 
						->setCategoryIds($main_product_data['categories'])// Populated further up the script 
						->setSku($simple_product_data['sku']. " - " . $attr_value) // $main_product_data is an array created as part of a wider foreach loop, which this code is inside of 
						->setName($main_product_data['name'] . " - " . $attr_value) 
						->setDescription($main_product_data['description']) 
						
						->setBrandGeneric($generic_code) 
						->setPharmaceuticalForm($pharmaceutical_form) 
						//->setUnitPrice($main_product_data['unit_price']) 
						->setactive_ingridients($active_ingridients) 
						->setManufacturer($manufacturer) 
						//->setLicenceHolder($licence_holder) 
						->setus_brand_name($us_brand_name) 
						->setbrand_code($brand_code) 
						->setbonus($bonus) 
						->setconfigurable_attribute($strength) 
						->setgeneric_name($main_product_data['active_ingridients'])
						->setgeneric_code($main_product_data['generic_code']) 						
						->setindian_brand($main_product_data['name'] . " - " . $attr_value) 						
						//->setOrigin($origin) 
						//->setInfo($main_product_data['info']) 
						//->setSourceCountry($source_country) 
						->setPrice(sprintf("%0.2f", $simple_product_data['price'])) 
						->setData($this->configurable_attribute, $configurableAttributeOptionId) ;   
				// Set the stock data. Let Magento handle this as opposed to manually creating a cataloginventory/stock_item model.. 
						$sProduct->setStockData(array( 'is_in_stock' => 1, 'qty' => 99999 ));   
						$sProduct->save();   

				// Store some data for later once we've created the configurable product, so we can 
				// associate this simple product to it later.. 
						array_push( 
							$this->simpleProducts, 
								array( 
									"id" => $sProduct->getId(), 
									"price" => $sProduct->getPrice(), 
									"attr_code" => $this->configurable_attribute, 
									"attr_id" => $this->attr_id, 
									"value" => $configurableAttributeOptionId, 
									"label" => $attr_value 
								) 
						);   
				if ($simple_product_data['price'] < $this->lowestPrice) { 
					$this->lowestPrice = $simple_product_data['price']; 
				} 
				
			}else{
				echo $simple_product_data['sku']. " - " . $attr_value .' :Skipping Already exist simple';
			}
		}
	}
	
	public function createConfigurableProduct($main_product_data,$data){
		
		$main_product_data = $main_product_data['configurable'];
		
		if(!is_object(Mage::getSingleton('catalog/product')->loadByAttribute('sku',$main_product_data['sku']))){
			
			$generic_code = $this->getOptionId('generic_code',$main_product_data['generic_code']);
			$pharmaceutical_form = $this->getOptionId('pharmaceutical_form',$main_product_data['pharmaceutical_form']);
			$active_ingridients = $this->getOptionId('active_ingridients',$main_product_data['active_ingridients']);
			$manufacturer = $this->getOptionId('manufacturer',$main_product_data['manufacturer']);
			//$licence_holder = $this->getOptionId('licence_holder',$main_product_data['licence_holder']);
			$us_brand_name = $main_product_data['us_brand_name'];
			$brand_code = $main_product_data['brand_code'];
			$strength = $main_product_data['configurable_attribute'];
			$bonus = $main_product_data['bonus'];
			//$origin = $this->getOptionId('origin',$main_product_data['origin']);
			//$source_country = $this->getOptionId('source_country',$main_product_data['source_country']);
			
			
			$cProduct = Mage::getModel('catalog/product');
			
			$cProduct
				->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
				->setTaxClassId(0)
				->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
				->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
				->setWebsiteIds(array(1))
				->setCategoryIds($main_product_data['categories'])
				->setAttributeSetId($this->attrSetId) // You can determine this another way if you need to.
				->setSku($main_product_data['sku'])
				->setName($main_product_data['name'])
				
				
				->setBrandGeneric($generic_code) 
				->setPharmaceuticalForm($pharmaceutical_form) 
				//->setUnitPrice($main_product_data['unit_price']) 
				->setactive_ingridients($active_ingridients) 
				->setManufacturer($manufacturer) 
				//->setLicenceHolder($licence_holder) 
				->setus_brand_name($us_brand_name) 
				->setbonus($bonus) 
				->setbrand_code($brand_code) 
				->setconfigurable_attribute($strength) 
				->setgeneric_name($main_product_data['active_ingridients'])  
				->setgeneric_code($main_product_data['generic_code']) 
				->setindian_brand($main_product_data['name'])
				//->setOrigin($origin) 
				//->setInfo($main_product_data['info']) 
				//->setSourceCountry($source_country) 
				
				
				->setShortDescription($main_product_data['description']) 
				->setDescription($main_product_data['description']) 
				
				->setPrice(sprintf("%0.2f", $this->lowestPrice))
				->setUrlKey($this->getProductUrlKey($main_product_data['product_name']))  
			;
		
			$cProduct->setCanSaveConfigurableAttributes(true);
			$cProduct->setCanSaveCustomOptions(true);
			 
			$cProductTypeInstance = $cProduct->getTypeInstance();
			// This array is is an array of attribute ID's which the configurable product swings around (i.e; where you say when you 
			// create a configurable product in the admin area what attributes to use as options) 
			// $_attributeIds is an array which maps the attribute(s) used for configuration so their numerical counterparts. 
			// (there's probably a better way of doing this, but i was lazy, and it saved extra db calls); 
			$_attributeIds = array("pack_size" => 140); 
			// etc..  
			$cProductTypeInstance->setUsedProductAttributeIds(array($_attributeIds[$this->configurable_attribute]));


			// Now we need to get the information back in Magento's own format, and add bits of data to what it gives us..
			$attributes_array = $cProductTypeInstance->getConfigurableAttributesAsArray(); 
			foreach($attributes_array as $key => $attribute_array) { 
				$attributes_array[$key]['use_default'] = 1; 
				$attributes_array[$key]['position'] = 0;   
				if (isset($attribute_array['frontend_label'])) { 
					$attributes_array[$key]['label'] = $attribute_array['frontend_label']; 
				} else { 
					$attributes_array[$key]['label'] = $attribute_array['attribute_code']; 
				} 
			}
			
			// Add it back to the configurable product..
			$cProduct->setConfigurableAttributesData($attributes_array);
			
			// Remember that $simpleProducts array we created earlier? Now we need that data..
			
			$dataArray = array(); 
			foreach ($this->simpleProducts as $simpleArray) { 
				$dataArray[$simpleArray['id']] = array(); 
				foreach ($attributes_array as $attrArray) { 
					array_push( $dataArray[$simpleArray['id']], 
						array( 
							"attribute_id" => $simpleArray['attr_id'], 
							"label" => $simpleArray['label'], 
							"is_percent" => false, 
							"pricing_value" => $simpleArray['price'] 
						) 
					); 
				} 
			}
			
			// This tells Magento to associate the given simple products to this configurable product.. 
			$cProduct->setConfigurableProductsData($dataArray);   
			// Set stock data. Yes, it needs stock data. No qty, but we need to tell it to manage stock, and that it's actually 
			// in stock, else we'll end up with problems later.. 
			$cProduct->setStockData(
						array( 
							'use_config_manage_stock' => 1, 
							'is_in_stock' => 1, 
							'is_salable' => 1 
							)
					);   
			// Finally...! 
			//print_r($cProduct->getData());exit;	
			$cProduct->save();
		}



	}
	
	public function getProductUrlKey($name){
		$name =  strtolower($name);
		return $name = str_replace(' ','-', $name);
		
	}
	
	//$optionValue = $this->getAttributeOptionValue("size", "XL");
	public function getAttributeOptionValue($arg_attribute, $arg_value) { 
		if(trim($arg_value) == '' || trim($arg_value) == '-'){
			$arg_value = 'NA';
		}
		
		$attribute_model = Mage::getModel('eav/entity_attribute'); 
		$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;   
		$attribute_code = $attribute_model->getIdByCode('catalog_product', $arg_attribute); 
		$attribute = $attribute_model->load($attribute_code);   
		$attribute_table = $attribute_options_model->setAttribute($attribute); 
		$options = $attribute_options_model->getAllOptions(false);   
		
		foreach($options as $option) { 
			if (trim($option['label']) == trim($arg_value)) { 
				return $option['value']; 
				//echo trim($option['label']) .'=='. trim($arg_value);
			}
		}   
		return false; 
	}
		
	// $optionValue = $this->addAttributeOption("size", "XXL");
	public function addAttributeOption($arg_attribute, $arg_value) {
		$arg_value = trim($arg_value);
		
		if(trim($arg_value) == '' || trim($arg_value) == '-'){
			$arg_value = 'NA';
		}
		
		$attribute_model        = Mage::getModel('eav/entity_attribute');
		$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
	 
		$attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
		$attribute              = $attribute_model->load($attribute_code);
	 
		$attribute_table        = $attribute_options_model->setAttribute($attribute);
		$options                = $attribute_options_model->getAllOptions(false);
	 
		$value['option'] = array($arg_value,$arg_value);
		$result = array('value' => $value);
		
		$attribute->setData('option',$result);
		$attribute->save();
	 
		return $this->getAttributeOptionValue($arg_attribute, $arg_value);
	}



	
	private function getConfigurable($row){
		$product['generic_code'] = $row[0];
		$product['sku'] = str_replace(' ', '-',strtolower($row[1]));
		$product['name'] = $row[2];
		$product['description'] = $row[17];
		//$product['pack_size'] = $row[3];
		$product['pharmaceutical_form'] = str_replace('?', 'i',$row[4]);
		//$product['unit_price'] = $row[5];
		$product['price'] = $row[6];
		$product['currency'] = $row[7];
		$product['active_ingridients'] = $row[8];
		$product['manufacturer'] = $row[9];
		$product['licence_holder'] = $row[10];
		$product['us_brand_name'] = $row[11];
		$product['configurable_attribute'] = $row[18];		
		$product['brand_code'] = $row[19];
		$product['bonus'] = $row[20];
		$product['origin'] = $row[12];
		$product['last_updated'] = $row[13];
		$product['info'] = $row[14];
		$product['source_country'] = $row[15];
		
		$product['categories'] = explode(',',$row[16]);
		
		//echo "<pre>";
		//print_r($product);
		//exit;
		
		return $product;
		
	}
	
	private function getSimple($row, $configurable){
	$tempSku="";
		$product['generic_code'] = $configurable['generic_code'];
		$product['sku'] = str_replace(' ', '-',strtolower($configurable['sku']));
		$product['name'] = $configurable['name'];
		$product['description'] = $configurable['description'];
		$product['pack_size'] = $row[3];
		$product['pharmaceutical_form'] = str_replace('?', 'i',$row[4]);
		$product['unit_price'] = $row[5]+($row[5]/2);
		
		//print_r($row[1]);print_r(strtolower($configurable));exit;
		//// code to implement extra price according to conditions///////////
		
			
			switch($configurable['price'])
				{
					case $configurable['price']< 1:
						$percent	= 20 ;
					  break;
					case $configurable['price'] > 1 &&  $configurable['price'] < 3:
						$percent	= 10 ;
					  break;
					case $configurable['price'] > 3 &&  $configurable['price'] < 5:
						$percent	= 7;
					  break;	
					 case $configurable['price'] > 5 :
						$percent	= 5 ;
					  break;	
					default:
						//code to be executed if n is different from both label1 and label2;
				}
				$tempSku = trim($configurable['sku']);
		
		
		if( $tempSku == trim($configurable['sku']))
		{
			$inc_price  =   ($row[6] * $percent)/100;
			//var_dump($inc_price); 
		}
		
		
			
			//// code to implement extra price according to conditions///////////
		//ECHO  $price;
		
		//$product['price'] = $row[6] + $price/*+(($row[6]*50)/100)*/;
		$product['price'] = $row[6] + $inc_price;
		
		$product['currency'] = $configurable['currency'];
		$product['active_ingridients'] = $configurable['active_ingridients'];
		$product['manufacturer'] = $configurable['manufacturer'];
		$product['licence_holder'] = $configurable['licence_holder'];
		$product['us_brand_name'] = $configurable['us_brand_name'];
		$product['configurable_attribute'] = $configurable['configurable_attribute'];
		$product['bonus'] = $configurable['bonus'];
		$product['brand_code'] = $configurable['brand_code'];
		$product['origin'] = $configurable['origin'];
		$product['last_updated'] = $configurable['last_updated'];
		$product['info'] = $configurable['info'];
		$product['source_country'] = $configurable['source_country'];
		$product['categories'] = explode(',',$row[16]);
		
		//echo "<pre>";
		//print_r($product);
		//exit;
		return $product;
	}
	public function csvstatusAction()
	{
	
		//$transfer = mage::getModel('AdvancedStock/StockTransfer')->load($this->getRequest()->getParam('st_id'));
        //$transferId = $this->getRequest()->getParam('st_id');
        $data = $this->getRequest()->getPost();
        //if (empty($transfer))
           // exit();
        try {
            
            //save text file
            $uploader = new Varien_File_Uploader('import_csvstatus');
            $uploader->setAllowedExtensions(array('csv','xls'));
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);

            //If file is uploaded
            if ($uploadFile = $uploader->getUploadedFileName()) {
                //load file
                $filePath = $path . $uploadFile;
                $lines = file($filePath);
                $csv = new Varien_File_Csv();
                $data = $csv->getData($filePath);
				

                //upload products data
                $result = $this->uploadStatus($data);
				
				if($result != '1')
				{
				    
					 Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
				}
				
            }
            else
                throw new Exception('Unable to load file');
            if ($result == '1'){
              Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Products Status Successfully Updated'));
			  
			 // $this->_redirect('adminhtml/sales_order/index');
			  Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?s=su"));
			
			}
        } catch (Exception $ex) {
			Mage::getSingleton('core/session')->unsErrormessage();
            //Mage::getSingleton('adminhtml/session')->addError('Products Status Not Updated');
			Mage::getSingleton('adminhtml/session')->setErrormessage('test error');
			//echo Mage::getSingleton('adminhtml/session')->getErrormessage();exit;
			//$this->_redirect('adminhtml/sales_order/importcsv/key/1e0dde6eec9800af184a4c91cc43efe2/');
		   Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
        }
         
        //confirm & redirect
        //$this->_redirect('adminhtml/sales_order/importcsv');
		
	
	}
	
	public function uploadStatus($data) {
        $debug = '';
 
        //parse lines
        try {
            foreach ($data as $index => $values) {

                //explode fields
                // $fields = explode(';', $line);
                if($index == 0)continue;

                //get Data

                $orderid = trim($data[$index][0]);
				//echo "##";
                $orderstatus = trim($data[$index][1]);
				//echo "<br>"; exit;
				/* if(trim($data[$index][6]) == "")
				{
					$orderstatus = trim($data[$index][5]);
					if(trim($data[$index][5]) == "")
					{
					//echo "afaf";exit;
					 return '2';
					}
				} */
			
                //process
                $order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
				$order_exist_status = $order->getStatus();
				if($orderstatus == "Awaiting Check/MoneyOrder/Wire Transfer")
				{
					$orderstatus = "awaiting_check_transfer";
				}
				elseif($orderstatus == "Payment Accepted")
				{
					$orderstatus = "payment_accepted";
				}
				elseif($orderstatus == "Transaction Declined")
				{
					$orderstatus = "transaction_declined";
				}
                  if(trim($order_exist_status) != trim($orderstatus))
				  {
                    //Add Products for the current stock transfer
                    $order->setStatus($orderstatus)->save();
					    
						
					$order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
					    $order_Id = array();
						$order_Id[0] = $order->getEntityId();
						if($orderstatus == "Awaiting Check/MoneyOrder/Wire Transfer")
						{
							$orderstatus = "awaiting_check_transfer";
						}
						
					    $statusObj = new Amasty_Oaction_Model_Command_Status();
					    $success=$statusObj->execute($order_Id,$orderstatus);
					// print_r($order->getData());exit;
					//exit;
				}
					
                
            }
           // $write->commit();

            return true;
        } catch (Exception $ex) {
            //$write->rollback();
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
            return false;
        }
    }
	
	//for prescription upload files--------------------------
	
	public function uploadAction()
    {
	   $type = 'file';
	  // echo "filename----";
	   //echo $_FILES[$type]['name'];exit;
        Mage::log($_FILES);
        if ($data = $this->getRequest()->getPost()) {
                 
            $type = 'file';
            if(isset($_FILES[$type]['name']) && $_FILES[$type]['name'] != '') {
                try {
                    /*$uploader = new Varien_File_Uploader($type);
                    //$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $path = Mage::getBaseDir('media') . DS . 'uploads' . DS;
                    $uploader->save($path, $_FILES[$type]['name'] );
                    $filename = $uploader->getUploadedFileName();
					*/
					$path = Mage::getBaseDir('media').DS.'uploads'.DS;  //desitnation directory     
					$fname = $_FILES[$type]['name']; //file name                        
					$uploader = new Varien_File_Uploader($type); //load class
					$uploader->setAllowedExtensions(array('doc','pdf','txt','docx')); //Allowed extension for file
					$uploader->setAllowCreateFolders(true); //for creating the directory if not exists
					$uploader->setAllowRenameFiles(false); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
					$uploader->setFilesDispersion(false);
					$uploader->save($path,$fname); //save the file on the specified path
                    $filename = $uploader->getUploadedFileName();
                         
                } catch (Exception $e) {
					echo 'Error Message: '.$e->getMessage();
                }
				
            }
			 Mage::getSingleton('checkout/session')->setPrescription($filename);
			
            echo $filename;
        }
    }
	
	
	public function importTrackinnumberAction()
	{
	    try {
            
            //save text file
            $uploader = new Varien_File_Uploader('import_csv');
            $uploader->setAllowedExtensions(array('csv','xls'));
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);

            //If file is uploaded
            if ($uploadFile = $uploader->getUploadedFileName()) {
                //load file
				
                $filePath = $path . $uploadFile;
                $lines = file($filePath);
                $csv = new Varien_File_Csv();
                $data = $csv->getData($filePath);
				
                
                //upload products data
                $result = $this->saveTraking($data);
				//echo "--sdfs--".$result;exit;
				if($result != '1')
				{
					 Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
				}
				
            }
            else
                throw new Exception('Unable to load file');
            if ($result == '1'){
			
                Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?s=su"));
			
			}
        } catch (Exception $ex) {
		    Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
        }
     
	}
	
	public function saveTraking($data)
	{
	    //print_r($data);exit;
		
		try {
            foreach ($data as $index => $values) {

                //explode fields
                // $fields = explode(';', $line);
                if($index == 0)continue;

                //get Data

                $orderid = trim($data[$index][0]);
				$title = trim($data[$index][1]);
                $track_no = trim($data[$index][2]);
				$date = trim($data[$index][3]);
				if(trim($data[$index][2]) == "" && $data[$index][3] = "")
				{
					//echo "afaf";exit;
					 return '2';
				}
			
                //process
                $order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
				$order_id = $order->getId();
				$shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
				$val = $shipment_collection->addAttributeToFilter('order_id', $order_id);
				//echo "<pre>";
				//print_r($val->getData());exit;
				$flag = 0;
				foreach($val as $sc) {
				//echo "<pre>";
				//print_r($sc);exit;
					$shipment = Mage::getModel('sales/order_shipment');
					$shipment->load($sc->getId());
					if($shipment->getId() != '') {

						if(strpos($track_no, ',')) {

							$trackNumbers = explode(',', $track_no);
							foreach($trackNumbers as $ind => $trackNumber) {

								if(strpos($date, ',')) {

									$dates = explode(',', $date);
									$track = Mage::getModel('sales/order_shipment_track')
										->setShipment($shipment)
										->setData('title', $title)
										->setData('number', $trackNumber)
										->setData('carrier_code', 'usps')
										->setData('assign_date', $dates[$ind])
										->setData('order_id', $shipment->getData('order_id'))
										->save();

								} else {

									$track = Mage::getModel('sales/order_shipment_track')
										->setShipment($shipment)
										->setData('title', $title)
										->setData('number', $trackNumber)
										->setData('carrier_code', 'usps')
										->setData('assign_date', $date)
										->setData('order_id', $shipment->getData('order_id'))
										->save();

								}

							}

						} else {

							$track = Mage::getModel('sales/order_shipment_track')
								->setShipment($shipment)
								->setData('title', $title)
								->setData('number', $track_no)
								->setData('carrier_code', 'usps')
								->setData('assign_date', $date)
								->setData('order_id', $shipment->getData('order_id'))
								->save();
						
						}
						$flag = 1;
					}
					
						
				}
				
				if($flag == '1')
				{
					$orderId[0] = $order_id;
					$orderstatus = "Shipped With tracking Number";
					$statusObj = new Amasty_Oaction_Model_Command_Status();
					$success=$statusObj->execute($orderId,$orderstatus);	
				}
                
            }
           // $write->commit();

            return true;
        } catch (Exception $ex) {
            //$write->rollback();
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
            return false;
        }
		
	  
		//echo "success";exit;
	}
	
}