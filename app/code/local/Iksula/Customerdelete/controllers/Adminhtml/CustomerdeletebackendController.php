<?php
class Iksula_Customerdelete_Adminhtml_CustomerdeletebackendController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed(){
        return true;
        //return Mage::getSingleton('admin/session')->isAllowed('customerdelete');  
    }
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Ratio Calculator"));
	   $this->renderLayout();
    }
     public function productwiseqtyAction()
    {
    	$this->loadLayout();
    	$this->_title($this->__("Reports"));
    	$this->renderLayout();

    }

	public function reportstockAction()
	 {	//echo $this->getRequest()->getParam('fromdatestock');
		$fromdate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('fromdatestock'));
		$todate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('todatestock'));
		// echo 'manojn';
		// exit;
		$fromdate = $fromdate->format('Y-m-d H:i:s');
		$todate = $todate->format('Y-m-d H:i:s');
		$filename = 'report.csv';
		$content = Mage::helper('customerdelete')->generateMlnList($fromdate,$todate);
		 // $filename = 'report.csv';
	     // $content = Mage::helper('customerdelete')->generateMlnList();
		$this->_prepareDownloadResponse($filename, $content);
	}

	public function reportstockstatementAction(){
		$filename = 'customer_report.csv';
		$content = Mage::helper('customerdelete')->generateCustomerreport();
		$this->_prepareDownloadResponse($filename, $content);
	}

	public function customerreportAction(){
		//print_r($this->getRequest()->getParams());
		//exit;
		$fromdate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('fromdate'));
		$todate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('todate'));
		$fromdate = $fromdate->format('Y-m-d H:i:s');
		$todate = $todate->format('Y-m-d H:i:s');

		$filename = 'customer_report_withfilters.csv';
		$content = Mage::helper('customerdelete')->generateCustomerreportwithfilters($fromdate,$todate);
	 // echo $content;
		$this->_prepareDownloadResponse($filename, $content);
	}
	public function savesupplyissueAction(){
		$supply_issue_data = $this->getRequest()->getParam('supply_issue_data');
		$order_id = $this->getRequest()->getParam('orderid');

		$order = Mage::getModel('sales/order')->load($order_id);
		$order->setSupplyIssueMessage($supply_issue_data)->save();
		$data = array('entity_id'=>$order_id,'supply_issue_message'=>$supply_issue_data);
		//print_r($data);
		Mage::dispatchEvent('insert_supply_issue_data',array('data'=>$data));
		//exit;
	}

	public function savedispatcherAction(){
		$dispatcher_data = $this->getRequest()->getParam('dispatcher_data');
		$order_id = $this->getRequest()->getParam('orderid');
		$order = Mage::getModel('sales/order')->load($order_id);
		$order->setDispatcherMessage($dispatcher_data)->save();
		$data = array('entity_id'=>$order_id,'dispatcher_message'=>$dispatcher_data);
		Mage::dispatchEvent('insert_dispatcher_message',array('data'=>$data));
	}

	public function savecommentAction(){
		$major_comment = $this->getRequest()->getParam('major_comment');
		$order_id = $this->getRequest()->getParam('orderid');
		$order = Mage::getModel('sales/order')->load($order_id);
		$orderGrid =  Mage::getResourceModel('sales/order_grid_collection')->addFieldToFilter('entity_id',$order_id);
		$order->setMajorComment($major_comment)->save();
		$orderGrid->getFirstitem()->setMajorComment($major_comment)->save();
	}



	public function savefromdateAction(){
		$fromdate_data = $this->getRequest()->getParam('fromdate_data');
		$order_id = $this->getRequest()->getParam('orderid');
		$order = Mage::getModel('sales/order')->load($order_id);
		$order->setFromdateMessage($fromdate_data)->save();
		$data = array('entity_id'=>$order_id,'fromdate_message'=>$fromdate_data);
		Mage::dispatchEvent('insert_fromdate_message',array('data'=>$data));
	}

	public function savetodateAction(){
		$todate_data = $this->getRequest()->getParam('todate_data');
		$order_id = $this->getRequest()->getParam('orderid');
		$order = Mage::getModel('sales/order')->load($order_id);
		$order->setTodateMessage($todate_data)->save();
		$data = array('entity_id'=>$order_id,'todate_message'=>$todate_data);
		Mage::dispatchEvent('insert_todate_message',array('data'=>$data));
	}

	public function savepaymentinfoAction(){
		$paymentinfo_data = $this->getRequest()->getParam('paymentinfo_data');
		$order_id = $this->getRequest()->getParam('orderid');
		$order = Mage::getModel('sales/order')->load($order_id);
		$order->setPaymentinfoMessage($paymentinfo_data)->save();
		// echo "<pre>";print_r($order->getData()); exit;

		$data = array('entity_id'=>$order_id,'paymentinfo_message'=>$paymentinfo_data);
		Mage::dispatchEvent('insert_paymentinfo_message',array('data'=>$data));
	}

	public function saveemailsAction(){
		$order_id = $this->getRequest()->getParam('orderid');
		$email_address = $this->getRequest()->getParam('email');
		// print_r($this->getRequest()->getParams());
		if($email_address!='' && $order_id!=''):
			$order = Mage::getModel('sales/order')->load($order_id);
			$order->getBillingAddress()->setData('email',$email_address);
			$order->setCustomerEmail($email_address)->save();

		endif;
		// return;
		// custom observer for crm
		Mage::dispatchEvent( 'insert_after_order_email_update',array('data'=>$this->getRequest()->getParams()));
		echo $email_address;
		// return;

	}

	public function saveCustomerBehaviorAction(){
		$order_id = $this->getRequest()->getParam('orderid');
		$behavior_id = $this->getRequest()->getParam('behavior_id');
		$behavior_value = $this->getRequest()->getParam('behavior_value');
		$customerId = $this->getRequest()->getParam('customerId');
		$customer_email = $this->getRequest()->getParam('email');//Mage::getModel('customer/customer')->load($customerId)->getData('email');
		//if($behavior_id!='' && $order_id!='' && $customerId != ''):
		if($behavior_id != '' && $order_id != ''):
			$data = json_encode(array('behavior_id'=>$behavior_id,'behavior_value'=>$behavior_value));
			//order table
			$orders = Mage::getModel('sales/order')->getCollection()
    		->addAttributeToFilter('customer_email',$customer_email);
			foreach($orders as $order)
			{ 
		       $order->setCustomerBehavior($data)->save();
		    }
 		endif;
	}

	/************ kyc custom code start ***********/

    public function savekycAction()
    {

    	//print_r($this->getRequest()->getParams()); exit();
    	$order_id = $this->getRequest()->getParam('orderid');
		$kyc_id = $this->getRequest()->getParam('kyc_id');
		$kyc_value = $this->getRequest()->getParam('kyc_value');
		$customerId = $this->getRequest()->getParam('customerId');
		$customer_email = $this->getRequest()->getParam('email');//Mage::getModel('customer/customer')->load($customerId)->getData('email');
		if($kyc_id != '' && $order_id != ''):
			$data = json_encode(array('kyc_id'=>$kyc_id,'kyc_value'=>$kyc_value));
			//order table
			$orders = Mage::getModel('sales/order')->getCollection()
    		->addAttributeToFilter('customer_email',$customer_email);
			foreach($orders as $order)
			{ 
		       $order->setKyc($data)->save();
		    }
 		endif;
    }

    /************ kyc custom code end ***********/
}
