<?php
class Iksula_Frontend_Block_Homesearch extends Mage_Core_Block_Template{

	public function __construct(){
		$this->addData(array(
         'cache_lifetime' => 3600, // Time to live
          // Tags to describe your block
         'cache_tags'     => array(Mage_Core_Model_Store::CACHE_TAG,Mage_Cms_Model_Block::CACHE_TAG)
         ));
	}

	public function getCacheKeyInfo()
	{
 		/// This is the unique identifier for your block cache file, used by magento when clearing the cache.
 		//  You can use any parameters to identify.
		return array(
			'allbrands',
			Mage::app()->getStore()->getId(),
			(int)Mage::app()->getStore()->isCurrentlySecure(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template')
			);
	}

	public function allbrands()
	{
		
		$mediaUrl = Mage::getBaseDir('media');
		$filePath = $mediaUrl.'/equivalent_search.json';
		$string = file_get_contents($filePath);
		$val = Mage::getBaseUrl();
		if(file_exists($filePath)){
			$json_a = json_decode($string, true);
			$html ='
			<input type="hidden" id="equivalentVal" value="'.$val.'">
			<li class="border_bot">
			<select id="dropdown1" onchange="equivalent()"> 
			<option value="Us">Equivalent Brands</option>';
			foreach($json_a['us_brand_name'] as $names):
				if($names != 'Latisse'):
					$name = Mage::helper('core/string')->truncate($names, 40);
					$html .='<option value="'.$this->__($names).'">'.$this->__($name).'</option>';
				endif;
			endforeach;

			$html .='</select></li>';

			$html .='<li>
			<select id="dropdown2" onchange="generic()"> 
			<option value="Us">Generic Search</option>';

			foreach($json_a['generic_name'] as $names):
				$name = Mage::helper('core/string')->truncate($names, 40);
				$html .='<option value="'.$this->__($names).'">'.$this->__($name).'</option>';
			endforeach;
			$html .='</select></li>';
			return $html;
		}

		$collection = Mage::getSingleton('catalog/product')
		->getCollection()
		->addAttributeToSort('name', 'ASC')
		->addAttributeToFilter(
			'status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
		$collection->addAttributeToSelect('name');
		$collection->addAttributeToSelect('us_brand_name');
		$collection->addAttributeToSelect('active_ingridients');
		$collection->addAttributeToSelect('generic_name');
		$collection->addAttributeToFilter('type_id', array('eq' => 'configurable'));

		$html ='<li>
		<select id="dropdown1">
			<option value="Us">Equivalent Brands</option>';
			$i=0;
			foreach ($collection as $product) {

				$data = $product->getData('us_brand_name');
				$data = str_replace('cream','',$data);
				$brand_active[]= trim($data);
				$finalBrand_active=array_unique(array_filter($brand_active));
			}

			asort($finalBrand_active);
			foreach($finalBrand_active as $names):
				if($names != 'Latisse'):
					$name = Mage::helper('core/string')->truncate($names, 40);
					$html .='<option value="'.$this->__($names).'">'.$this->__($name).'</option>';
				endif;
				endforeach;


				$html .='</select></li>';

				$html .='<li>
				<select id="dropdown2">
					<option value="Us">Generic Search</option>';

					foreach ($collection as $product) {
						$data = $product->getData('generic_name');
						$data = str_replace('cream','',$data);
						$active[]= trim($data);
					}
					$final_active=array_unique(array_filter($active));

					asort($final_active);
					foreach($final_active as $names):
						$name = Mage::helper('core/string')->truncate($names, 40);
						$html .='<option value="'.$this->__($names).'">'.$this->__($name).'</option>';
					endforeach;
					$html .='</select></li>';
					if(!file_exists($filePath)){
						$homeSearchJson['us_brand_name'] = $finalBrand_active;
						$homeSearchJson['generic_name'] = $final_active;
						$fp = fopen($filePath, 'w+');
						fwrite($fp, json_encode($homeSearchJson));
						fclose($fp);
					}
					return $html;
	}


}

