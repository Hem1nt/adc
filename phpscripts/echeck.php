<?php

$post_data['security_key'] = '4c61363cd88d59a5c7257390fe991de9';
$post_data['purchaser_firstname'] = 'TEST';
$post_data['purchaser_lastname'] = 'TEST';
$post_data['purchaser_address'] = '123 Jane Way';
$post_data['purchaser_city'] = 'Phoenix';
$post_data['purchaser_state'] = 'AZ';
$post_data['purchaser_zipcode'] = '54321';
$post_data['purchaser_phone'] = '(555)555-5555';
$post_data['purchaser_email'] = 'testemail@test.biz';
$post_data['transaction_amount'] = '30.00';
$post_data['purchaser_ip'] = "115.249.232.133";
$post_data['purchaser_account'] = "0032571228";
$post_data['purchaser_routing'] = "021000089"; // Valid routing number
//$post_data['purchaser_routing'] = "123456789"; // Invalid routing number
//traverse array and prepare data for posting 
foreach ( $post_data as $key => $value) {
$post_items[] = $key . '=' . $value;
}
//create the final string to be posted using implode()
$post_string = implode ('&', $post_items);
//create cURL connection
$curl_connection = curl_init('https://theecheck.com/backoffice/api.php');
//set options
curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
//set data to be posted
curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
//perform our request
$result = curl_exec($curl_connection);
echo "Transaction Result: " . $result . "<br><br>";
// uncomment below to show information regarding the request
// print_r(curl_getinfo($curl_connection));
// echo curl_errno($curl_connection) . '-' . curl_error($curl_connection);
//close the connection
curl_close($curl_connection);
?>