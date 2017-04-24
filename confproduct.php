<?php
require_once('app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
ini_set('display_errors', 1);
error_reporting(E_ALL);
// $file = fopen("confproducts.csv","w");

// echo "<pre>";
// $collectionConfigurable = Mage::getResourceModel('catalog/product_collection')
// ->addAttributeToFilter('type_id', array('eq' => 'configurable'));
// $i = 0;
// $customArr = array();
// $_helper = Mage::helper('frontend');
// echo 'type,sku,price<br/>';
// foreach ($collectionConfigurable as $_configurableproduct) {
// 	$data[$i]['sku'] = $_configurableproduct->getSku();
// 	$data[$i]['type'] = $_configurableproduct->getTypeId();
// 	$data[$i]['price']= $_helper->getFirstChildPrice($_configurableproduct);
// 	echo $data[$i]['type'].','.$data[$i]['sku'].','.$data[$i]['price'].'<br/>';
// 	$i++;
// }
// // print_r($data);
// exit;
$headers = parseRequestHeaders();
echo '<pre>';print_r($headers);
print_r($_SERVER);
function parseRequestHeaders() {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}	

?>

