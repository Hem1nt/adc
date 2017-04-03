<?php
class Iksula_Medical_Model_Observer
{

	public function medicalhistoryorderidset(Varien_Event_Observer $observer)
	{
		$event = $observer->getEvent();
		$order = $event->getOrder(); 
		$orderId = $order->getRealOrderId();
		/* start Browser detail in order */
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";
	    if (preg_match('/linux/i', $u_agent)) 
	        $platform = 'linux';
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent))
	        $platform = 'mac';
	    elseif (preg_match('/windows|win32/i', $u_agent))
	        $platform = 'windows';
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    }
	    elseif(preg_match('/Firefox/i',$u_agent)){
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    }
	    elseif(preg_match('/Chrome/i',$u_agent)){
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    }
	    elseif(preg_match('/Safari/i',$u_agent)){
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    }
	    elseif(preg_match('/Opera/i',$u_agent)){
	        $bname = 'Opera';
	        $ub = "Opera";
	    }
	    elseif(preg_match('/Netscape/i',$u_agent)){
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub))
	            $version= $matches['version'][0];
	        else
	            $version= $matches['version'][1];
	    }
	    else 
	        $version= $matches['version'][0];
	    if ($version==null || $version=="") {$version="?";}
	    $tablet_browser = 0;
		$mobile_browser = 0;
		 
		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($u_agent))) {
		    $tablet_browser++;
		}
		 
		if (preg_match('/(up.browser|up.link|mmp|BB|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($u_agent))) {
		    $mobile_browser++;
		}
		 
		if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		    $mobile_browser++;
		}
		 
		$mobile_ua = strtolower(substr($u_agent, 0, 4));
		$mobile_agents = array(
		    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		    'newt','noki','palm','pana','pant','phil','play','port','prox',
		    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		    'wapr','webc','winw','winw','xda ','xda-');
		 
		if (in_array($mobile_ua,$mobile_agents)) {
		    $mobile_browser++;
		}
		 
		if (strpos(strtolower($u_agent),'opera mini') > 0) {
		    $mobile_browser++;
		    //Check for tablets on opera mini alternative headers
		    $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
		    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
		      $tablet_browser++;
		    }
		}
		 
		if ($tablet_browser > 0) {$devicename = 'Tablet';}
		else if ($mobile_browser > 0) {$devicename = 'Mobile';}
		else {$devicename = 'Desktop';} 
	    // $name= array(
	    //     'name'      => $bname,
	    //     'version'   => $version,
	    //     'platform'  => $platform,
	    //     'pattern'    => $pattern
	    // );

	    $mobilename = explode("(",$_SERVER['HTTP_USER_AGENT']);
		$mobilename = explode(")",$mobilename[1]);

	   $name = $devicename." - ".$mobilename[0]." - ".$bname."/".$version;
		/* end Browser detail in order */

		$insertId = Mage::getSingleton('core/session')->getInsertId();		
		if($insertId):
			$model = Mage::getModel('medical/medical')->load($insertId)->addData($orderId);
			try {

				$model->setOrderid($orderId)->save();

			} catch (Exception $e){
				echo $e->getMessage();
			}

			$dataArr = Mage::getModel('medical/medical')->load($insertId);

			Mage::getSingleton('core/session')->unsInsertId();
			$physicianname = $dataArr->getData('physicianname');
			$physiciantelephone = $dataArr->getData('physiciantelephone');
			$drug_allergies = $dataArr->getData('drugallergies');
			$current_medications = $dataArr->getData('currentmedications');
			$current_treatments = $dataArr->getData('currenttreatments');
			$smoke = $dataArr->getData('smoke');
			$drink = $dataArr->getData('drink');
     
			$order->setPhysicianname($physicianname);
			$order->setPhysiciantelephone($physiciantelephone);
			$order->setDrugAllergies($drug_allergies);
			$order->setCurrentMedications($current_medications);
			$order->setCurrentTreatments($current_treatments);
			$order->setSmoke($smoke);
			$order->setDrink($drink);
			$order->save(); 
		endif; 
			$order->setData('browser_details',$name);
			$order->save(); 
	}

}
