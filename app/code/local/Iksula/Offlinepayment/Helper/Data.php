<?php
class Iksula_Offlinepayment_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function gspay_check_order($transaction_id) {

		$url="https://secure.redirect2pay.com/payment/api.php";
		$values=array(
			'request'=>"
			<xml>
				<request>
					<transaction>
						<transactionType>transactionStatus</transactionType>
						<transactionTransactionID>".$transaction_id."</transactionTransactionID>
					</transaction>


				</request>
			</xml>

			",
			);
		$params='';

		foreach ($values as $key=>$value) {
			$params.="$key=".urlencode($value)."&";
		}


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		$result=curl_exec($ch);
		if (curl_errno($ch)>0) {
			exit("CURL ERROR:".curl_errno($ch).":".curl_error($ch));
		}
		curl_close($ch);


		$requestxml=$result;
		$p = xml_parser_create();
		if (!  xml_parse_into_struct($p, $requestxml, $vals, $index)) {
			exit("can't parse gspay result");
		}
		xml_parser_free($p);
		$value = array();

		if (is_array($vals)) foreach ($vals as $key=>$value) {
			if ($value['type']=='complete') {
				$transinfo[$value['tag']]=trim($value['value']);
			}
		}
		return $transinfo;
	}
}
