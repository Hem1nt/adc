<?php

class Vinagento_Vpager_Block_Catalog_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    public function getJsonConfig()
    {
        $attributes = array();
        $options    = array();
        $store      = $this->getCurrentStore();
        $taxHelper  = Mage::helper('tax');
        $currentProduct = $this->getProduct();
		$sku = $currentProduct->getSku();
		
        $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }

                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($currentProduct->getFinalPrice())
        );

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
			$product = $this->getProduct();
			//echo "<pre>";
			$pharma_form = $product->getResource()->getAttribute('pharmaceutical_form')->getFrontend()->getValue($product);
			$bonus = $product->getBonus();
			//exit;
			
			//add code
			
			$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
			$currency= Mage::getModel('directory/currency')->load($currency_code);
			$product = Mage::getModel('catalog/product')->load($product->getId());
			preg_match('/^\D*(?=\d)/', $product['name'], $new_name);
			if($product['configurable_attribute'] !="")
			{
				$similar_Products = Mage::getModel('catalog/product')->getCollection()
									->addAttributeToFilter('type_id', array('eq' => 'configurable'))
									->addAttributeToFilter('configurable_attribute', array('eq' => $product['configurable_attribute']))
									->addAttributeToFilter('generic_name', array('eq' => $product['generic_name']))
									->addAttributeToFilter('active_ingridients', array('eq' => $product['active_ingridients']))
									->addAttributeToFilter('us_brand_name', array('eq' => $product['us_brand_name']))
									//->addAttributeToFilter('manufacturer', array('eq' => $product['manufacturer']))
									->addAttributeToFilter('name', array('like' => $new_name[0].'%'));
			}
			else
			{
				$similar_Products = Mage::getModel('catalog/product')->getCollection()
									->addAttributeToFilter('type_id', array('eq' => 'configurable'))
									//->addAttributeToFilter('configurable_attribute', array('eq' => $product['configurable_attribute']))
									->addAttributeToFilter('generic_name', array('eq' => $product['generic_name']))
									->addAttributeToFilter('active_ingridients', array('eq' => $product['active_ingridients']))
									->addAttributeToFilter('us_brand_name', array('eq' => $product['us_brand_name']))
									//->addAttributeToFilter('manufacturer', array('eq' => $product['manufacturer']))
									->addAttributeToFilter('name', array('like' => $new_name[0].'%'));
			}
			$i=0; $j=0;				
			foreach($similar_Products as $similar_Product){
				$product = Mage::getModel('catalog/product')->load($similar_Product->getId());
				$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product); 
				
				foreach($childProducts as $childProduct){
					//$pharma_form=$this->getOptionId('pharmaceutical_form',$_product['pharmaceutical_form']);
					$childname=$childProduct->getData('name');
					$pos = strrpos($childname, '-');
					
							//ucwords(substr($childname,$pos+1)).' '.$pharma_form;
							$sku_new = $childProduct->getData('sku');
							$package_size = explode("-",$sku_new);
							//$rate[trim($package_size[1])] = sprintf("%.2f",$childProduct->getData('price'));
							//uncomment above line and comment below code for default
							//edited by nilesh
							if(($i==0 && trim($package_size[0] == $sku)) || $j==1)
							{
								if(trim($package_size[0] == $sku))
								{
									$rate[trim($package_size[1])] = sprintf("%.2f",$childProduct->getData('price'));
								}
								$j=1;
							}
							else
							{
								$rate[trim($package_size[1])] = sprintf("%.2f",$childProduct->getData('price'));
								$i++;
							}
							//edited by nilesh end
				}
				
				
			}
			
			//end code
			$i = 0;
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    $currentProduct->setConfigurablePrice(
                        $this->_preparePrice($value['pricing_value'], $value['is_percent'])
                    );
                    $currentProduct->setParentId(true);
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $currentProduct)
                    );
                    $configurablePrice = $currentProduct->getConfigurablePrice();
					
                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $productsIndex = array();
                    }
					  $size = $value['label'];
                    if($bonus != "")
					{
						$info['options'][] = array(
							'id'        => $value['value_index']."-".$productsIndex[0],
							'label'     => $value['label'] ." + ".$value['label']." ".$pharma_form." Free - US$ ".$rate[$size],
							'price'     => $configurablePrice,
							'oldPrice'  => $this->_prepareOldPrice($value['pricing_value'], $value['is_percent']),
							'products'  => $productsIndex,
						);
					}
					else
					{
						$info['options'][] = array(
							'id'        => $value['value_index']."-".$productsIndex[0],
							'label'     => $value['label'] ." ". $pharma_form." - US$ ".$rate[$size],
							'price'     => $configurablePrice,
							'oldPrice'  => $this->_prepareOldPrice($value['pricing_value'], $value['is_percent']),
							'products'  => $productsIndex,
						);
					}
					$i++;
                    $optionPrices[] = $configurablePrice;
                }
            }
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $_request = $taxCalculation->getRateRequest(false, false, false);
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $defaultTax = $taxCalculation->getRate($_request);

        $_request = $taxCalculation->getRateRequest();
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $currentTax = $taxCalculation->getRate($_request);

        $taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
        );

        $config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig
        );

        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return Mage::helper('core')->jsonEncode($config);
    }

  
}
