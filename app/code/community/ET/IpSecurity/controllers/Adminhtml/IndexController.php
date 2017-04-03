<?php
class ET_IpSecurity_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action{
	public function IndexAction() {
        if(isset($_GET['ipaddress'])) {
			Mage::getSingleton('core/session')->setIpAddress($_GET['ipaddress']);
		}
		echo Mage::getSingleton('core/session')->getIpAddress();
	}

	public function SendloginmailAction()
    {
        //echo "here";exit;
        if(empty($_GET['ip_address'])) {
            echo "Unable to find IP address."; exit;
        }
        $currentIp = $_GET['ip_address'];
        //echo $currentIp;exit;
        
        //$adminUser = $this->getRequest()->getParam('username');
        //$loginDate = $this->getRequest()->getParam('logtime');

        $admin = Mage::getSingleton('admin/session')->getUser();

        //echo '<pre>';print_r($admin->getData());exit;
        $adminUser = $admin->getData('username');
        $loginDate = $admin->getData('logdate');
        
        $to = "cs@alldaychemist.com,kundan@derricwood.com";
        //$to = "moiz.k@iksula.com,moizk007@gmail.com";
        $subject = "IP Security - ADC Admin Panel Login";
        
        /*****API for IP Address and Country Tracking******/
        $output = curl_init('http://api.ipinfodb.com/v3/ip-city/?key=d55371dfd1f8e6759102c298f8ebd9c9b50964bc3ad8c85cca48c9c10b1a72ce&format=xml&ip='.$currentIp);
        curl_setopt($output, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($output);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        //echo "<pre>"; print_r($array);exit;

        $regionName = $array['regionName'];
        $city = $array['cityName'];
        $country = $array['countryName'];

        if($regionName=="" || $city=="" || $country=="")
        {
            $url = "http://ip-api.com/json/".$currentIp;
            //echo $url;
            $url = curl_init($url);
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            $result_curl = curl_exec($url);
            $decode_json = json_decode($result_curl, true);
            //print_r($decode_json);
            
            //$isp = $decode_json['isp'];
            $regionName = $decode_json['regionName'];
            $city = $decode_json['city'];
            $country = $decode_json['country']; 
        }
        /*****End of API for IP Address and Country Tracking******/ 
        
        $msg = '<p><b>'.$adminUser.'</b> user has logged in</p>';
        $msg .= '<table border="1">
            <tr>
                <td>UserName</td>
                <td>IP Address</td>
                <td>City</td>
                <td>State</td>
                <td>Country</td>
                <td>Login time</td>
            </tr>
            <tr>
                <td>'.$adminUser.'</td>
                <td>'.$currentIp.'</td>
                <td>'.$city.'</td>
                <td>'.$regionName.'</td>
                <td>'.$country.'</td>
                <td>'.$loginDate.'</td>
            </tr>
        </table>';

        //echo $msg;exit;
        $headers = "From: noreply@alldaychemist.com\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";        
        //$msg = "Ip Address: ". $currentIp." UserName:". $adminUser." Login Time:".$loginDate;
        mail($to,$subject,$msg,$headers);
    }   
}
