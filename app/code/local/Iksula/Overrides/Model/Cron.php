<?php
class Iksula_Overrides_Model_Cron{
	public function updateOrderCountry(){
        $time = time();
        echo $to = date('Y-m-d H:i:s', $time);
        $lastTime = $time - 600;
        echo $from = date('Y-m-d H:i:s', $lastTime);
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->load();
        foreach ($orders as $order) {
                $this->updatecountry($order);
        }

	}
    public function updatecountry($_order){
        $ipaddress = $_order->getRemoteIp();
        $country = '';
        if(!empty($ipaddress)){
            $_order = Mage::getModel('sales/order')->load($_order->getId());
            $url = "http://ipinfo.io/{$ipaddress}";
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
            $results = curl_exec($ch);
            $details = json_decode($results);
            if (curl_errno($ch)) {
                $contents = '';
            } else {
                curl_close($ch);
            }
            
            $country = $details->country;

            $_order->setData('order_placed_country',$country);
            $_order->save();
        }
        return;
    }

    public function elasticstatus() {
        $ch = curl_init();
        $Url = 'http://iksulabeta.com:9200/alldaychemist/short/_search';
        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $Url);

        // User agent
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'GET');

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // Close the cURL resource, and free system resources
        curl_close($ch);
        $result = json_decode($output,true);

        $searchSwitch = new Mage_Core_Model_Config();
        if($result['hits']['total'] <= 0) {

            $searchSwitch ->saveConfig('custom_snippet/elasticsearch_tabs/elasticsearch_options', "mysql", 'default', 'elastic');
        } else {
            $searchSwitch ->saveConfig('custom_snippet/elasticsearch_tabs/elasticsearch_options', "elastic", 'default', 'elastic');
        }
    }
}
