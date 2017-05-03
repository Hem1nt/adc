<?php
// header('Content-type: application/json');
require_once('app/Mage.php');
Mage::app('default');

$countryList = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore()
                ->toOptionArray(true);

$countries = json_encode($countryList);
// echo "<pre>";
// print_r($countryList);
foreach($countryList as $k=>$v) {
	if($v['value']) {
		/*echo $v['value'];
		echo "<br/>";*/
		if ($v['value']=='US')
		{
			echo '<option value="'.$v['value'].'" selected>'.$v['label'].'</option>';
		}
		else
		{
			echo '<option value="'.$v['value'].'">'.$v['label'].'</option>';
		}
	}
}