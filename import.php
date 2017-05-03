<?php 
$con = mysql_connect("localhost", "root", "Allday@123");
//$db="allday_31may";
mysql_select_db("allday_final", $con);
//mysql_select_db($db);

$fp = fopen("file.csv", "w");
$num = 1;
//for ($c=0; $c < $num; $c++)
//{
     $query = "SELECT ad.id_order_detail,ad.id_order,ad.product_id,ad.product_name,ad.product_quantity,ad.product_price FROM allday_orders as ao JOIN allday_order_detail as ad ON ao.id_order = ad.id_order where ao.date_add >= '2013-05-25'";
     $result = mysql_query($query);
     //$row = mysql_fetch_array($result);
	 $csv="";
	 $fp = fopen('order_detail_final.csv', 'w');
      while($row = mysql_fetch_assoc($result))
	  {
	       $data = $row;
		   /* echo "<pre>";
		   print_r($data); */
		   $csv.=$data['id_order_detail'].",".$data['id_order'].",".$data['product_id'].",".$data['product_name'].",".$data['product_quantity'].",".$data['product_price'].",\n";
	  }
	  fwrite($fp, $csv);
	  fclose($fp);
	  echo $csv;exit;
     fputcsv($fp, $data);
//} 
fclose($fp);

/*
$filename= "oredr_mearch.csv";
$fp = fopen($filename, "w");

$res = mysql_query("",$con);

// fetch a row and write the column names out to the file
//$row = mysql_fetch_assoc($res);

$row = mysql_fetch_assoc($res);
	if($row) {
		fputcsv($fp, array_keys($row));
		// reset pointer back to beginning
		mysql_data_seek($res, 0);
	}
	
       
        fclose($fp);
			
*/

?>