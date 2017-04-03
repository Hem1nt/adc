<?php
 
class Rx_Productimport_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action{

	public function importTrackinnumberAction()	{
	    try {
            
            //save text file
            $uploader = new Varien_File_Uploader('import_csv');
            $uploader->setAllowedExtensions(array('csv','xls'));
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);

            //If file is uploaded
            if ($uploadFile = $uploader->getUploadedFileName()) {
                //load file
				
                $filePath = $path . $uploadFile;
                $lines = file($filePath);
                $csv = new Varien_File_Csv();
                $data = $csv->getData($filePath);
				
                
                //upload products data
                $result = $this->saveTraking($data);
				//echo "--sdfs--".$result;exit;
				Mage::dispatchEvent('upload_tracking_info',$data);
				if($result != '1')
				{
					 Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
				}
				
            }
            else
                throw new Exception('Unable to load file');
            if ($result == '1'){
			
                Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?s=su"));
			
			}
        } catch (Exception $ex) {
		    Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
        }
     
	}


	public function uploadStatus($data) {
        $debug = '';
        $error = '';
        $errorCounter = 0;
        //parse lines
        try {
            foreach ($data as $index => $values) {
                if($index == 0)continue;
                $orderid = trim($data[$index][0]);
                $orderstatus = trim($data[$index][1]);
                if(empty($orderstatus)){
                	$error .= 'Please provide corresponding status for order id '.trim($data[$index][0]).'<br>';
                	// $errorCounter++;
                	continue;
                }
                $order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
				$order_exist_status = $order->getStatus();
				// if($orderstatus == "Awaiting Check/MoneyOrder/Wire Transfer")
				// {
				// 	$orderstatus = "awaiting_check_transfer";
				// }
				// elseif($orderstatus == "Payment Accepted")
				// {
				// 	$orderstatus = "payment_accepted";
				// }
				// elseif($orderstatus == "Transaction Declined")
				// {
				// 	$orderstatus = "transaction_declined";
				// }

				$orderstatuscollection = Mage::getSingleton('sales/order_status')->getCollection()->getData();
				foreach($orderstatuscollection as $collection){
					if($orderstatus == $collection['label'])
						$orderstatus = $collection['status'];
				}
                  if(trim($order_exist_status) != trim($orderstatus))
				  {
                    //Add Products for the current stock transfer
                    $order->setStatus($orderstatus)->save();
					    
						
					$order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
					    $order_Id = array();
						$order_Id[0] = $order->getEntityId();
						if($orderstatus == "Awaiting Check/MoneyOrder/Wire Transfer")
						{
							$orderstatus = "awaiting_check_transfer";
						}
						
					    $statusObj = new Amasty_Oaction_Model_Command_Status();
					    $success=$statusObj->execute($order_Id,$orderstatus);
						
						 /* start of admin order status update log management*/
					    $data_to_save =array();
					    $data_to_save['order_id'] = $order->getData('increment_id');
					    $data_to_save['status'] = $orderstatus;
						$data_to_save['user'] = Mage::getSingleton('admin/session')->getUser()->getUsername();
						Mage::getModel('orderlog/orderstatus')->setData($data_to_save)->save();    
					    /* end of admin order status update log management*/
					    
				}
					
                
            }
            if($errorCounter){
            	Mage::getSingleton('core/session')->addError($error);
            	Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('admin/sales_order/index'));
            	return;
            }
           // $write->commit();

            return true;
        } catch (Exception $ex) {
            //$write->rollback();
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
            return false;
        }
    }




    public function csvstatusAction()	{ 	
        $data = $this->getRequest()->getPost();
        //if (empty($transfer))
           // exit();
        try {
            
            //save text file
            $uploader = new Varien_File_Uploader('import_csvstatus');
            $uploader->setAllowedExtensions(array('csv','xls'));
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);

            //If file is uploaded
            if ($uploadFile = $uploader->getUploadedFileName()) {
                //load file
                $filePath = $path . $uploadFile;
                $lines = file($filePath);
                $csv = new Varien_File_Csv();
                $data = $csv->getData($filePath);
				

                //upload products data
                $result = $this->uploadStatus($data);
				
				if($result != '1')
				{
				    
					 Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
				}
				
            }
            else
                throw new Exception('Unable to load file');
            if ($result == '1'){
              Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Products Status Successfully Updated'));
			  
			 // $this->_redirect('adminhtml/sales_order/index');
			  Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?s=su"));
			
			}
        } catch (Exception $ex) {
			Mage::getSingleton('core/session')->unsErrormessage();
            //Mage::getSingleton('adminhtml/session')->addError('Products Status Not Updated');
			Mage::getSingleton('adminhtml/session')->setErrormessage('test error');
			//echo Mage::getSingleton('adminhtml/session')->getErrormessage();exit;
			//$this->_redirect('adminhtml/sales_order/importcsv/key/1e0dde6eec9800af184a4c91cc43efe2/');
		   Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
        }
         
        //confirm & redirect
        //$this->_redirect('adminhtml/sales_order/importcsv');
		
	
	}
	
	public function saveTraking($data)
	{
	    // print_r($data);exit;
		
		try {
            foreach ($data as $index => $values) {

                //explode fields
                // $fields = explode(';', $line);
                if($index == 0)continue;

                //get Data

                $orderid = trim($data[$index][0]);
				// $title = trim($data[$index][1]);
                $track_no = trim($data[$index][1]);
				$date = trim($data[$index][2]);
				$code = trim($data[$index][3]);
				$shippingParts = trim($data[$index][4]);
				if(trim($data[$index][1]) == "" && $data[$index][2] = "")
				{
					//echo "afaf";exit;
					 // return '2';	
					 return '2';
				}
			
                //process
                $order = Mage::getModel('sales/order')->loadByIncrementId($orderid);

                if($data[$index][1]){
                	/* start of admin order tracking update log management*/
                	$data_to_save =array();
                	$data_to_save['order_id'] = $order->getData('increment_id');
                	$data_to_save['track_id'] = $data[$index][1];
                	$data_to_save['shipping_part'] = $data[$index][4];
                	$data_to_save['user'] = Mage::getSingleton('admin/session')->getUser()->getUsername();
                	Mage::getModel('orderlog/ordertracking')->addData($data_to_save)->save();     
                	/* end of admin order tracking update log management*/
                }

				$order_id = $order->getId();
				$shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
				$val = $shipment_collection->addAttributeToFilter('order_id', $order_id);
				//echo "<pre>";
				//print_r($val->getData());exit;
				$flag = 0;
				$mailFlag = 0;
				$duplicateTrackingNo = array();
				$allShipmentTrackingNumber = array();
				foreach($val as $sc) {
						$shipment = Mage::getModel('sales/order_shipment');
						$shipment->load($sc->getId());
						$shipmentTrack = Mage::getModel('sales/order_shipment_track')->getCollection()->addFieldToFilter('parent_id',$sc->getId())->load();
						foreach ($shipmentTrack as $key => $value) {
							$allShipmentTrackingNumber[] = $value['track_number'];
						}
						
						if($shipment->getId() != '') {

							if(strpos($track_no, ',')) {
								$trackNumber = explode(',', $track_no);
								$trackNumberCount = count($trackNumber);

								$trackNumberCount = count($trackNumber);

								$codes = explode(',', $code);
								$codesCount = count($codes);
								if($codesCount == 1){
									$codes = $this->getAllCodes($trackNumberCount,$codesCount,$codes);
									array_shift($codes);
								}

								$dates = explode(',', $date);
								$datesCount = count($dates);
								if($datesCount == 1){
									$dates = $this->getAllDates($trackNumberCount,$datesCount,$dates);
									array_shift($dates);
								}

								foreach($trackNumber as $ind => $newtrackNumber) {

									if(in_array($newtrackNumber,$allShipmentTrackingNumber)){
										$mailFlag++;
										continue;
									}
									$mailFlag = 0;

									$title = '';
									$title = $this->getTitle($codes,$ind);

							        if(empty($title)){
							        	$title = Mage::getStoreConfig("amoaction/shipping_url/default_lable");
							        	$codes[$ind] = Mage::getStoreConfig("amoaction/shipping_url/default_code");
							        }
							        if(!in_array($newtrackNumber, $duplicateTrackingNo))
							        {
										$this->setAllTrackingNumbers($title,$newtrackNumber,$codes[$ind],$dates[$ind],$shipment);
							        }
							        array_push($duplicateTrackingNo,$newtrackNumber);
								}

							}elseif(strpos($code, ',')) {
								$codes = explode(',', $code);
								$codesCount = count($codes);
								$trackNumber = explode(',', $track_no);
								$trackNumberCount = count($trackNumber);

								if($trackNumberCount == 1){
									$trackNumber = $this->getAllTrackNumbers($trackNumberCount,$codesCount,$trackNumber);
									array_shift($trackNumber);
								}

								$dates = explode(',', $date);
								$datesCount = count($dates);
								if($datesCount == 1){
									$dates = $this->getAllDatesByCodes($codesCount,$datesCount,$dates);
									array_shift($dates);
								}

								foreach($codes as $ind => $newCode) {
									$title = '';
							        $title = $this->getTitle($codes,$ind);
							        
							        if(empty($title)){
								        $title = $this->getDefaultTitle();
								        $code = $this->getTrackingCode();
									}

									if(in_array($trackNumber[$ind],$allShipmentTrackingNumber)){
										$mailFlag++;
										continue;
									}
									$mailFlag = 0;
									$this->setAllTrackingNumbers($title,$trackNumber[$ind],$newCode,$dates[$ind],$shipment);
								}

							}elseif(strpos($date, ',')) {
								$trackNumber = explode(',', $track_no);
								$trackNumberCount = count($trackNumber);
								$dates = explode(',', $date);
								$datesCount = count($dates);
								$codes = explode(',', $code);
								$codesCount = count($codes);
								$trackNumberCount = count($trackNumber);

								if($trackNumberCount == 1){
									if($trackNumberCount != $datesCount){
										for($i=0;$i<$datesCount;$i++){
											array_push($trackNumber, $trackNumber[0]);		
										}
										array_shift($trackNumber);
									}
								}

								if($codesCount == 1){
									if($datesCount != $codesCount){
										for($i=0;$i<$datesCount;$i++){
											array_push($codes, $codes[0]);		
										}
										array_shift($codes);
									}
								}

								foreach($dates as $ind => $newdate) {
									$title = '';
									$title = $this->getTitle($codes,$ind);

							        if(empty($title)){
								        $title = $this->getDefaultTitle();
								        $code = $this->getTrackingCode();
									}
							        
							        if(in_array($trackNumber[$ind],$allShipmentTrackingNumber)){
							        	$mailFlag++;
										continue;
									}
									$mailFlag = 0;
									$this->setAllTrackingNumbers($title,$trackNumber[$ind],$codes[$ind],$newdate,$shipment);
								}

							} 
							else {
								$title = '';
						        $title = $this->getTitleByCode($code);
						        if(empty($title)){
								        $title = $this->getDefaultTitle();
								        $code = $this->getTrackingCode();
								}

								if(in_array($track_no,$allShipmentTrackingNumber)){
									$mailFlag++;
									continue;
								}
								$mailFlag = 0;
								$this->setAllTrackingNumbers($title,$track_no,$code,$date,$shipment);
							}
							// if(!$mailFlag){
							// 	$flag = 1;
							// }
							$flag = 1;
						}
						
					array_push($orderIdArray,$orderid);	
					}
				if($flag == '1')
				{
					$orderId[0] = $order_id;
					$orderstatus = "Shipped With tracking Number";
					$statusObj = new Amasty_Oaction_Model_Command_Status();
					$success=$statusObj->execute($orderId,$orderstatus);	
				}
                
            }
           // $write->commit();

            return true;
        } catch (Exception $ex) {
            //$write->rollback();
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
            return false;
        }
		
	  
		//echo "success";exit;
	}

	public function getDefaultTitle(){
		return Mage::getStoreConfig("amoaction/shipping_url/default_lable");
	}

	public function getTrackingCode(){
		return Mage::getStoreConfig("amoaction/shipping_url/default_code");
	}

	public function getTitle($codes,$ind){
		$carriers = Mage::getsingleton("shipping/config")->getAllCarriers();
        foreach($carriers as $codex => $method){
            if($codes[$ind] == $codex){
            	return $title = Mage::getStoreConfig("carriers/$codex/title");
            }
        }
	}

	public function getTitleByCode($code){
		$carriers = Mage::getsingleton("shipping/config")->getAllCarriers();
        foreach($carriers as $codex => $method){
            if($code == $codex){
            	return $title = Mage::getStoreConfig("carriers/$codex/title");
            }
        }

	}

	public function getAllDates($trackNumberCount,$datesCount,$dates){
		if($trackNumberCount != $datesCount){
			for($i=0;$i<$trackNumberCount;$i++){
				array_push($dates, $dates[0]);		
			}
			return $dates;
		}	
	}

	public function getAllDatesByCodes($codesCount,$datesCount,$dates){
		if($codesCount != $datesCount){
			for($i=0;$i<$codesCount;$i++){
				array_push($dates, $dates[0]);		
			}
			return $dates;
		}	
	}

	public function getAllCodes($trackNumberCount,$codesCount,$codes){
		if($trackNumberCount != $codesCount){
			for($i=0;$i<$trackNumberCount;$i++){
				array_push($codes, $codes[0]);		
			}
			return $codes;
		}
	}

	public function getAllTrackNumbers($trackNumberCount,$codesCount,$trackNumber){
		if($trackNumberCount != $codesCount){
			for($i=0;$i<$codesCount;$i++){
				array_push($trackNumber, $trackNumber[0]);		
			}
			return $trackNumber;	
		}
	}

	public function setAllTrackingNumbers($title,$trackNumber,$code,$newdate,$shipment){
		$track = Mage::getModel('sales/order_shipment_track')
				->setShipment($shipment)
				->setData('title', $title)
				->setData('number', $trackNumber)
				->setData('carrier_code', $code)
				->setData('assign_date', $newdate)
				->setData('order_id', $shipment->getData('order_id'))
				->save();
	}
	
}
	
	
	
	
	