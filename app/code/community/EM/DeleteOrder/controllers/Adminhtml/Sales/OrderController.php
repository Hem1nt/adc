<?php
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class EM_DeleteOrder_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{

	/**
     * Add order comment action
     */
    public function addCommentAction()
    {
    	
    	if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                // $order->addStatusHistoryComment($data['comment'], $data['status'])
                //     ->setIsVisibleOnFront($visible)
                //     ->setIsCustomerNotified($notify);

                //getting username
                $session = Mage::getSingleton('admin/session');
                $username = $session->getUser()->getUsername();
                $ipaddress=$_SERVER['REMOTE_ADDR'];
                // $append = " (by ".$username." | ".$ipaddress.") ";
                $append = " (by ".$username.") ";
                 
                //appending username with markup to comment
                $order->addStatusHistoryComment($data['comment'].$append, $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);
                    
                $comment = trim(strip_tags($data['comment']));

                $order->save();
                // echo "hello"; exit;
                // crm custom observer => updateStatus
                $sendToCrm = array($order->getId());
                Mage::dispatchEvent( 'insert_after_order_status_update',array('data'=>$sendToCrm));
                // crm custom observer => updateStatus end
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    public function deleteorderAction()
    { 
		
		$orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');	
		$query="show tables";
		$rsc_table=$write->fetchCol($query);	
		
		$table_sales_flat_order = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');						
		$table_sales_flat_creditmemo_comment= Mage::getSingleton('core/resource')->getTableName('sales_flat_creditmemo_comment');
		$table_sales_flat_creditmemo_item= Mage::getSingleton('core/resource')->getTableName('sales_flat_creditmemo_item');
		$table_sales_flat_creditmemo= Mage::getSingleton('core/resource')->getTableName('sales_flat_creditmemo');
		$table_sales_flat_creditmemo_grid= Mage::getSingleton('core/resource')->getTableName('sales_flat_creditmemo_grid');
		$table_sales_flat_invoice_comment= Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice_comment');
		$table_sales_flat_invoice_item= Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice_item');
		$table_sales_flat_invoice= Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice');
		$table_sales_flat_invoice_grid= Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice_grid');
		$table_sales_flat_quote_address_item= Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_address_item');
		$table_sales_flat_quote_item_option= Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_item_option');
		$table_sales_flat_quote= Mage::getSingleton('core/resource')->getTableName('sales_flat_quote');
		$table_sales_flat_quote_address= Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_address');
		$table_sales_flat_quote_item= Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_item');
		$table_sales_flat_quote_payment= Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_payment');
		$table_sales_flat_shipment_comment= Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_comment');
		$table_sales_flat_shipment_item= Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_item');
		$table_sales_flat_shipment_track= Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_track');
		$table_sales_flat_shipment= Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment');
		$table_sales_flat_shipment_grid= Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_grid');		
		$table_sales_flat_order_address= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_address');
		$table_sales_flat_order_item= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		$table_sales_flat_order_payment= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_payment');
		$table_sales_flat_order_status_history= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');					
		$table_sales_flat_order_grid= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_grid');						
		$table_log_quote= Mage::getSingleton('core/resource')->getTableName('log_quote');				
        $quoteId='';		
		if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
					$query=null;
					$order = Mage::getModel('sales/order')->load($orderId);					
					if($order->increment_id){
						/*$query="show tables like 'sales_flat_order'";
						$rs=$write->fetchAll($query);*/						
						$incId=$order->increment_id;
						if(in_array($table_sales_flat_order,$rsc_table)){
							$query='SELECT entity_id   FROM  '.$table_sales_flat_order.'    WHERE increment_id="'.mysql_escape_string($incId).'"';
							
							$rs=$write->fetchAll($query);												
						
							$query='SELECT quote_id    FROM   '.$table_sales_flat_order.'        WHERE entity_id="'.mysql_escape_string($orderId).'"';
							$rs1=$write->fetchAll($query);
							$quoteId=$rs1[0]['quote_id'];							
						}		
						
						$query='SET FOREIGN_KEY_CHECKS=1';
						$rs3=$write->query($query);
						//print_r($rsc_table);
						//echo $table_sales_flat_creditmemo_comment;
						if(in_array($table_sales_flat_creditmemo_comment,$rsc_table)){
						//echo "DELETE FROM ".$table_sales_flat_creditmemo_comment."    WHERE parent_id IN (SELECT entity_id FROM ".$table_sales_flat_creditmemo." WHERE order_id=".$orderId.")";
						//die;
						$write->query("DELETE FROM ".$table_sales_flat_creditmemo_comment."    WHERE parent_id IN (SELECT entity_id FROM ".$table_sales_flat_creditmemo." WHERE order_id='".mysql_escape_string($orderId)."')");
						}
						//die;
						
						
						if(in_array('sales_flat_creditmemo_item',$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_creditmemo_item."       WHERE parent_id IN (SELECT entity_id FROM ".$table_sales_flat_creditmemo." WHERE order_id='".mysql_escape_string($orderId)."')");
						}
						
						
						if(in_array($table_sales_flat_creditmemo,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_creditmemo."            WHERE order_id='".mysql_escape_string($orderId)."'");
						}
						
						
						
						if(in_array($table_sales_flat_creditmemo_grid,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_creditmemo_grid."        WHERE order_id='".mysql_escape_string($orderId)."'");
						}
						
						
						if(in_array($table_sales_flat_invoice_comment,$rsc_table)){
						
						$write->query("DELETE FROM ".$table_sales_flat_invoice_comment." WHERE parent_id IN (SELECT entity_id FROM ".$table_sales_flat_invoice." WHERE order_id='".mysql_escape_string($orderId)."')");
						}
						
						if(in_array($table_sales_flat_invoice_item,$rsc_table)){
						
						$write->query("DELETE FROM ".$table_sales_flat_invoice_item."     WHERE parent_id IN (SELECT entity_id FROM ".$table_sales_flat_invoice." WHERE order_id='".mysql_escape_string($orderId)."')");
						}
						
						
						if(in_array($table_sales_flat_invoice,$rsc_table)){
						
						$write->query("DELETE FROM ".$table_sales_flat_invoice."         WHERE order_id='".mysql_escape_string($orderId)."'");
						}
						
						if(in_array($table_sales_flat_invoice_grid,$rsc_table)){
						
						$write->query("DELETE FROM ".$table_sales_flat_invoice_grid."     WHERE order_id='".mysql_escape_string($orderId)."'");
						}	
						
						if($quoteId){						
							if(in_array($table_sales_flat_quote_address_item,$rsc_table)){							
							$write->query("DELETE FROM ".$table_sales_flat_quote_address_item."     WHERE parent_item_id  IN (SELECT address_id FROM ".$table_sales_flat_quote_address." WHERE quote_id=".$quoteId.")");
							}
							
							$table_sales_flat_quote_shipping_rate= Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_shipping_rate');
							if(in_array($table_sales_flat_quote_shipping_rate,$rsc_table)){
							$write->query("DELETE FROM ".$table_sales_flat_quote_shipping_rate."    WHERE address_id IN (SELECT address_id FROM ".$table_sales_flat_quote_address." WHERE quote_id=".$quoteId.")");
							}
							
							if(in_array($table_sales_flat_quote_item_option,$rsc_table)){
							$write->query("DELETE FROM ".$table_sales_flat_quote_item_option."     WHERE item_id IN (SELECT item_id FROM ".$table_sales_flat_quote_item." WHERE quote_id=".$quoteId.")");
							}
						
							
							if(in_array($table_sales_flat_quote,$rsc_table)){
							$write->query("DELETE FROM ".$table_sales_flat_quote."                 WHERE entity_id=".$quoteId);
							}
							
							if(in_array($table_sales_flat_quote_address,$rsc_table)){
							$write->query("DELETE FROM ".$table_sales_flat_quote_address."         WHERE quote_id=".$quoteId);
							}
							
							if(in_array($table_sales_flat_quote_item,$rsc_table)){
							$write->query("DELETE FROM ".$table_sales_flat_quote_item."             WHERE quote_id=".$quoteId);
							}
							
							if(in_array('sales_flat_quote_payment',$rsc_table)){
							$write->query("DELETE FROM ".$table_sales_flat_quote_payment."         WHERE quote_id=".$quoteId);
							}
							
						}
						
						
						if(in_array($table_sales_flat_shipment_comment,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_shipment_comment."    WHERE parent_id IN (SELECT entity_id FROM ".$table_sales_flat_shipment." WHERE order_id='".mysql_escape_string($orderId)."')");
						}
						
						if(in_array($table_sales_flat_shipment_item,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_shipment_item."         WHERE parent_id IN (SELECT entity_id FROM ".$table_sales_flat_shipment." WHERE order_id='".mysql_escape_string($orderId)."')");
						}
						
						
						if(in_array($table_sales_flat_shipment_track,$rsc_table)){						
						$write->query("DELETE FROM ".$table_sales_flat_shipment_track."         WHERE order_id  IN (SELECT entity_id FROM ".$table_sales_flat_shipment." WHERE order_id='".mysql_escape_string($orderId)."')");
						}
						
						
						if(in_array($table_sales_flat_shipment,$rsc_table)){
						
						$write->query("DELETE FROM ".$table_sales_flat_shipment."             WHERE order_id='".mysql_escape_string($orderId)."'");
						}
						
						
						if(in_array($table_sales_flat_shipment_grid,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_shipment_grid."         WHERE order_id='".mysql_escape_string($orderId)."'");
						}
						
						if(in_array($table_sales_flat_order,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_order."                     WHERE entity_id='".mysql_escape_string($orderId)."'");
						}
						
						if(in_array($table_sales_flat_order_address,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_order_address."            WHERE parent_id='".mysql_escape_string($orderId)."'");
						}
						
						if(in_array($table_sales_flat_order_item,$rsc_table)){						
						$write->query("DELETE FROM ".$table_sales_flat_order_item."                 WHERE order_id='".mysql_escape_string($orderId)."'");
						}
						if(in_array($table_sales_flat_order_payment,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_order_payment."             WHERE parent_id='".mysql_escape_string($orderId)."'");
						}
						if(in_array($table_sales_flat_order_status_history,$rsc_table)){
						$write->query("DELETE FROM ".$table_sales_flat_order_status_history."     WHERE parent_id='".mysql_escape_string($orderId)."'");
						}
						if($incId&&in_array($table_sales_flat_order_grid,$rsc_table)){						
							$write->query("DELETE FROM ".$table_sales_flat_order_grid."                 WHERE increment_id='".mysql_escape_string($incId)."'");
	
						}
						
						$query="show tables like '%".$table_log_quote."'";
						$rsc_table_l=$write->fetchCol($query);	
						if($quoteId&&$rsc_table_l){						
								$write->query("DELETE FROM ".$table_log_quote." WHERE quote_id=".$quoteId);							
						}
						$write->query("SET FOREIGN_KEY_CHECKS=1");						
					}					
			}	
		$this->_getSession()->addSuccess($this->__('Order(s) deleted.'));
		}else{
		$this->_getSession()->addError($this->__('Order(s) error.'));
		}		
		$this->_redirect('*/*/');		
    }
	
	public function indexAction()
    {
	   if($_REQUEST['e'] != "")
			$this->_getSession()->addError($this->__('Invalid Upload File.'));
	   else if($_REQUEST['s'] != "")
			$this->_getSession()->addSuccess($this->__('File Import Successfully.'));
			
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        $this->_initAction()
            ->renderLayout();
    }
	
	public function importcsvAction()
	{
	/*$html = $this->getLayout()
        ->createBlock('adminhtml/sales_order_importcsv')
        ->setTemplate('sales/order/importcsv/importcsv.phtml')
        ->toHtml();*/
		 $this->loadLayout();
		 $this->renderLayout();
	  
	}
	
	public function importtrackingAction()
	{
	
	/*$html = $this->getLayout()
        ->createBlock('adminhtml/sales_order_importcsv')
        ->setTemplate('sales/order/importcsv/importcsv.phtml')
        ->toHtml();*/
		 $this->loadLayout();
		 $this->renderLayout();
	  
	}
	public function bulkinvoiceAction()
	{
		// echo 'bulkinvoice';exit;
		$this->loadLayout();
		$this->renderLayout();
	}
	public function exportcustomerAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function bulkinvoicepdfAction()
	{
		// echo 'bulkinvoice';exit;
		$this->loadLayout();
		$this->renderLayout();
	}
	public function bulkshipmentAction()
	{	
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function customexporttocsvAction()
    {

    	$this->loadLayout();
    	$this->renderLayout();

    }

    public function itemexporttocsvAction()
    {

    	$this->loadLayout();
    	$this->renderLayout();

    }
    public function feedbackimportcsvAction()
    {
    	$this->loadLayout();
    	$this->renderLayout();

    }

    public function prescriptionexportcsvAction()
    {

    	$this->loadLayout();
    	$this->renderLayout();

    }

    public function descripterexportcsvAction()
    {

    	$this->loadLayout();
    	$this->renderLayout();

    }

    public function bulkDescripterExportCsvAction(){        
    	try {

            //save text file
    		$salesModel = Mage::getModel('sales/order');
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
    			array_shift($data);
    			// echo '<pre>';print_r($data);exit;
                //upload products data
    			$orderIds = array();
    			$errorids = array();
    			// Mage::getSingleton('core/session')->setBulkUploadUpload('bulkupload');
    			$exportEmailIds = $this->getEmailIdByIncrementIdAction($data);
    			// print_r($exportEmailIds);exit;
    			$orders = $exportEmailIds;//$this->getRequest()->getPost('order_ids', array());
    			$file = Mage::getModel('em_deleteorder/export_descripterexportcsv')->exportOrders($orders);
    			$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));

    		}
    	}catch(Exception $e){

    	}

    }

    public function bulkdescripterimportcsvAction(){
    	try{
    		$uploader = new Varien_File_Uploader('import_csvstatus');
    		$uploader->setAllowedExtensions(array('csv','xls'));
    		$path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
    		$uploader->save($path);
    		if ($uploadFile = $uploader->getUploadedFileName()) {
    			$filePath = $path . $uploadFile;
    			$lines = file($filePath);
    			$csv = new Varien_File_Csv();
    			$data = $csv->getData($filePath);
    			foreach ($data as $key => $value) {
    				if($key != 0){
    					$orderModel = Mage::getModel('sales/order')->loadByIncrementId($value[0]);
    					$orderModel->setDispatcherMessage($value[1])->save();
    				}
    			}
    			$message = 'File successfully imported';
				Mage::getSingleton('adminhtml/session')->addSuccess($message);
				Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index"));
				return;
    		}
    	}catch(Exception $e){

    	}
    }

    public function bulkPrescriptionExportAction(){        
    	try {

            //save text file
    		$salesModel = Mage::getModel('sales/order');
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
    			array_shift($data);
                //upload products data
    			$orderIds = array();
    			$errorids = array();
    			// Mage::getSingleton('core/session')->setBulkUploadUpload('bulkupload');
    			$exportorderIds = $this->getOrderIdByIncrementIdAction($data);
    			// print_r($exportorderIds);exit;
    			$orders = $exportorderIds;//$this->getRequest()->getPost('order_ids', array());
    			$file = Mage::getModel('em_deleteorder/export_prescriptioncsv')->exportOrders($orders);
    			$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));

    		}
    	}catch(Exception $e){

    	}

    }

	public function medicalHistoryAction()
    {
        $this->_initOrder();
        $html = $this->getLayout()->createBlock('medical/adminhtml_order_view_tab_medicalhistory')->toHtml();
        /* @var $translate Mage_Core_Model_Translate_Inline */
        $translate = Mage::getModel('core/translate_inline');
        if ($translate->isAllowed()) {
            $translate->processResponseBody($html);
        }
        $this->getResponse()->setBody($html);
    }
	

    public function feedbackImportAction(){        
		try {

	        //save text file
			$salesModel = Mage::getModel('sales/order');
			$uploader = new Varien_File_Uploader('import_feedback');
			$uploader->setAllowedExtensions(array('csv','xls'));
			$path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
			$uploader->save($path);

			if ($uploadFile = $uploader->getUploadedFileName()) {
				$filePath = $path . $uploadFile;
				$lines = file($filePath);
				$csv = new Varien_File_Csv();
				$data = $csv->getData($filePath);
				array_shift($data);
				foreach ($data as $order){
					$orderData = $this->getOrderDataByIncrementIdAction($order[0]);
					if ($orderData) {
						Mage::helper('trustedcompany/data')->sendEmail($orderData);
					}
				}

			}
		}catch(Exception $e){

		}
		// exit;
		$this->_getSession()->addSuccess($this->__('Feedback Emailer is send to Custmers'));
    	$this->_redirect('*/*/');

    }

    public function getOrderDataByIncrementIdAction($data)
    {
    	$order = Mage::getModel('sales/order')->loadByIncrementId($data);  
   		return $order;
    }

    public function bulkcsvAction(){        
		try {

	        //save text file
			$salesModel = Mage::getModel('sales/order');
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
				array_shift($data);
	            //upload products data
				$orderIds = array();
				$errorids = array();
				// Mage::getSingleton('core/session')->setBulkUploadUpload('bulkupload');
				$exportorderIds = $this->getOrderIdByIncrementIdAction($data);
				// print_r($exportorderIds);exit;
				$orders = $exportorderIds;//$this->getRequest()->getPost('order_ids', array());
				$file = Mage::getModel('em_deleteorder/export_csv')->exportOrders($orders);
				$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));

			}
		}catch(Exception $e){

		}

    }

    public function bulkitemcsvAction(){        
    	try {

            //save text file
    		$salesModel = Mage::getModel('sales/order');
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
    			array_shift($data);
                //upload products data
    			$orderIds = array();
    			$errorids = array();
    			// Mage::getSingleton('core/session')->setBulkUploadUpload('bulkupload');
    			$exportorderIds = $this->getOrderIdByIncrementIdAction($data);
    			// print_r($exportorderIds);exit;
    			$orders = $exportorderIds;//$this->getRequest()->getPost('order_ids', array());
    			$file = Mage::getModel('em_deleteorder/export_itemcsv')->exportOrders($orders);
    			$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));

    		}
    	}catch(Exception $e){

    	}

    }

	/**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction(){
		$orderIds = $this->getRequest()->getPost('order_ids');
		
			$flag = false;
			$shipmentArray=array();
			if (!empty($orderIds)) {
				foreach ($orderIds as $orderId) {
					$shipments = Mage::getResourceModel('sales/order_invoice_collection')
						->setOrderFilter($orderId)
						->load();
					if ($shipments->getSize()) {
						$flag = true;
						array_push($shipmentArray,$shipments);
					}
				}
				
				
					if($flag) {
						Mage::getSingleton('core/session')->setPdfsession('1');
						if (!isset($pdf)){
							$pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($shipmentArray);
						} 
						return $this->_prepareDownloadResponse(
							'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
							'application/pdf'
						);
					} else {
						$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
						$this->_redirect('*/*/');
					}
				
		}
		
        $this->_redirect('*/*/');
    } 

  	/* This function used to generated invoice pdf without shipping charges */

    public function massInvoicePdfAction(){
    	try {
    		
            //save text file
    		$uploader = new Varien_File_Uploader('import_bulk_invocie');
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
    			array_shift($data);
                //upload products data
    			$orderIds = array();
    			$errorids = array();
    			Mage::getSingleton('core/session')->setBulkUploadUpload('bulkupload');
    			$invoiceorderIds = $this->getOrderIdByIncrementIdAction($data);
    			foreach($invoiceorderIds as $key => $orderInvoice) {   
    				$order = Mage::getModel('sales/order')->load($orderInvoice);             	
    				if(!$order->canInvoice()){
    					$orderIds[]=$orderInvoice;
    				}else{
    					$errorids[]=$orderInvoice;
    				}
    			}
                // print_r($errorids);exit();
    			Mage::getSingleton('core/session')->unsBulkUploadUpload();
    			
    			if($orderIds != '1')
    			{
    				
    				Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
    			}
    			
    		}
    	}catch(Exception $e){

    	}
    	
    	$flag = false;
    	$invoiceArray=array();
    	if (!empty($orderIds)) {
    		foreach ($orderIds as $orderId) {
    			$order_invoice_collection = Mage::getResourceModel('sales/order_invoice_collection')
    			->setOrderFilter($orderId)
    			->load();
    			if ($order_invoice_collection->getSize()) {
    				$flag = true;
    				array_push($invoiceArray,$order_invoice_collection);
    			}
					
    		}
    		
    		if($flag) {
    			Mage::getSingleton('core/session')->setPdfsession('1');
    			if (!isset($pdf)){
    				$pdf = Mage::getModel('sales/order_pdf_invoice')->getInvoicePdf($invoiceArray);
    			} 
    			return $this->_prepareDownloadResponse(
    				'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
    				'application/pdf'
    				);
    		} else {
    			$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
    			$this->_redirect('*/*/');
    		}
    		
    	}
    	
    	$this->_redirect('*/*/');
    }


    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
		$shipmentArray=array();
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()) {
                    $flag = true;
					array_push($shipmentArray,$shipments);
                }
            }
			
				if ($flag) {
						Mage::getSingleton('core/session')->setPdfsession('1');
						if (!isset($pdf)){
							$pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipmentArray);
						} 
						return $this->_prepareDownloadResponse(
							'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
							'application/pdf'
						);
				} else {
					$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
					$this->_redirect('*/*/');
				}
			
        }
        $this->_redirect('*/*/');
    }
	
	// all print order
	
	public function pdfdocsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
		$invoiceArray = array();
		$shipmentArray=array();
		$creditmemosArray=array();
        if (!empty($orderIds)) {
		
				foreach ($orderIds as $orderId) {
                
					$invoice = Mage::getResourceModel('sales/order_invoice_collection')
						->setOrderFilter($orderId)
						->load();
					if ($invoice->getSize()) {
						$flag = true;
						array_push($invoiceArray,$invoice);
					}
					
					$shipments = Mage::getResourceModel('sales/order_shipment_collection')
						->setOrderFilter($orderId)
						->load();
					if ($shipments->getSize()) {
						$flag = true;
						array_push($shipmentArray,$shipments);
					}
					
					
				}
				
			    /*if (!isset($pdf)){
					$pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoiceArray);
				}
				if (!isset($pdf)){
					$pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipmentArray);
				} 
				if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemosArray);
                } */
            
				if ($flag){
				
					if (!isset($pdf)){
						$pdf = Mage::getModel('sales/order_pdf_shipment')->getallprintPdf($invoiceArray);
					} 
				   return $this->_prepareDownloadResponse(
						'docs'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf',
						$pdf->render(), 'application/pdf'
					);
				} else {
					$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
					$this->_redirect('*/*/');
				}
			
        }
        $this->_redirect('*/*/');
    }

    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getOrdersCsvFile());
    }

    public function bulkpdfinvoicesAction(){

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
    			array_shift($data);
                //upload products data
    			$orderIds = array();
    			$errorids = array();
    			Mage::getSingleton('core/session')->setBulkUploadUpload('bulkupload');
    			$invoiceorderIds = $this->getOrderIdByIncrementIdAction($data);
                // print_r($orderIds);exit();
    			foreach($invoiceorderIds as $key => $orderInvoice) {   
    				$order = Mage::getModel('sales/order')->load($orderInvoice);             	
    				if(!$order->canInvoice()){
    					$orderIds[]=$orderInvoice;
    				}else{
    					$errorids[]=$orderInvoice;
    				}
    			}
                // print_r($errorids);exit();
    			Mage::getSingleton('core/session')->unsBulkUploadUpload();
    			
    			if($orderIds != '1')
    			{
    				
    				Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
    			}
    			
    		}
    	}catch(Exception $e){

    	}
    	
    	$flag = false;
    	$invoiceArray=array();
    	if (!empty($orderIds)) {
    		foreach ($orderIds as $orderId) {
    			$order_invoice_collection = Mage::getResourceModel('sales/order_invoice_collection')
    			->setOrderFilter($orderId)
    			->load();
    			if ($order_invoice_collection->getSize()) {
    				$flag = true;
    				array_push($invoiceArray,$order_invoice_collection);
						//$invoiceArray = array_unique($invoiceArray);
    			}
					// print_r($shipmentArray);
    		}

    		
    		if($flag) {
    			Mage::getSingleton('core/session')->setPdfsession('1');
    			if (!isset($pdf)){
    				$pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoiceArray);
    			} 
    			return $this->_prepareDownloadResponse(
    				'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
    				'application/pdf'
    				);
    		} else {
    			$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
    			$this->_redirect('*/*/');
    		}
    		
    	}
    	
    	$this->_redirect('*/*/');
    }
    public function bulkpdfshipmentAction(){

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
    			array_shift($data);
                //upload products data
    			$orderIds = array();
    			$errorids = array();
    			Mage::getSingleton('core/session')->setBulkUploadUpload('bulkupload');
    			$shipmentorderIds = $this->getOrderIdByIncrementIdAction($data);
                // print_r($orderIds);exit();
    			foreach($shipmentorderIds as $key => $orderInvoice) {   
    				$order = Mage::getModel('sales/order')->load($orderInvoice);             	
    				if(!$order->canShip()){
    					$orderIds[]=$orderInvoice;
    				}else{
    					$errorids[]=$orderInvoice;
    				}
    			}
                // print_r($errorids);exit();
    			Mage::getSingleton('core/session')->unsBulkUploadUpload();
    			
    			if($orderIds != '1')
    			{
    				
    				Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/index?e=err"));
    			}
    			
    		}
    	}catch(Exception $e){

    	}
    	
    	$flag = false;
    	$shipmentArray=array();
    	if (!empty($orderIds)) {
    		foreach ($orderIds as $orderId) {
    			$shipments = Mage::getResourceModel('sales/order_shipment_collection')
    			->setOrderFilter($orderId)
    			->load();
    			if ($shipments->getSize()) {
    				$flag = true;
    				array_push($shipmentArray,$shipments);
						//$shipmentArray = array_unique($shipmentArray);
    			}
					// print_r($shipmentArray);
    		}

    		
    		if($flag) {
    			Mage::getSingleton('core/session')->setPdfsession('1');
    			if (!isset($pdf)){
    				$pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipmentArray);
    			} 
    			return $this->_prepareDownloadResponse(
    				'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
    				'application/pdf'
    				);
    		} else {
    			$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
    			$this->_redirect('*/*/');
    		}
    		
    	}
    	
    	$this->_redirect('*/*/');
    }
    
    public function getOrderIdByIncrementIdAction($data)
    {
    	foreach ($data as $key => $ordersvalue) {
    		$order = Mage::getModel('sales/order')->loadByIncrementId($ordersvalue[0]);  
    	  	if($order->getId()) {  		
    			$orderIds[] = $order->getId();	
    		}	

    	}
    	return $orderIds;
    }

    public function getEmailIdByIncrementIdAction($data)
    {

    	foreach ($data as $key => $ordersvalue) {
    		$order = Mage::getModel('sales/order')->loadByIncrementId($ordersvalue[0]);  
    	  	if($order->getId()) {  		
    			$emailIds[] = $order->getCustomerEmail();	
    		}
    	}
    	$emailIdsData = array_unique($emailIds);
    	return $emailIdsData;
    }
    public function exportCustomerCsvAction()	{

    try {
            //save text file
    		$salesModel = Mage::getModel('sales/order');
    		$uploader = new Varien_File_Uploader('export_customerdata');
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
    			array_shift($data);
    			$content = "Order id,Customer Name,Customer Email,Customer Behaviour,How did you find us?\n";
    			foreach (array_chunk($data,100) as $order) {
				$product = Mage::getModel('sales/order')->getCollection()
							->addAttributeToFilter('increment_id', array('in' => $order))
							->addFieldToSelect('increment_id')
							->addFieldToSelect('customer_firstname')
							->addFieldToSelect('customer_lastname')
							->addFieldToSelect('customer_email')
							->addFieldToSelect('customer_behavior')
							->addFieldToSelect('find_us');
				foreach ($product as $products) {
				/*For Attribute find us S*/
				$customerData = Mage::getModel('customer/customer')->loadByEmail($products->getData('customer_email'));
				$attr = $customerData->getResource()->getAttribute('find_us');
				if ($attr->usesSource()) {
					$find_us_label = $attr->getSource()->getOptionText($products->getData('find_us'));
					/*If some one selected others as heard from options S*/
					if(strtolower($find_us_label) == "others")
						{
							$attr = $customerData->getData('find_us_other');
							$find_us_label = "Others: ".$attr; 
						}
					}
					if (!$find_us_label){
						$find_us_label = $products->getFindUs();
					}				
					/*If some one selected others as heard from options E*/
				/*For Attribute find us E*/
					$product_data = array();
					$product_data['Order_id'] = $products->getData('increment_id');
					$product_data['customer_Name'] = $products->getCustomerName();
					$product_data['customer_email'] = $products->getData('customer_email');
					$return_behavior = json_decode($products->getData('customer_behavior'),true);
					$product_data['customer_behavior'] = $return_behavior['behavior_value'];
					$product_data['find_us'] = $find_us_label;
					$csvdata[] = $product_data;
					$content .= implode(',', $product_data)."\n";
						}
					}
				$fileName       = 'customer_data_'.time().'.csv';
				$this->_prepareDownloadResponse($fileName, $content);
    		}
    	}catch(Exception $e){
    		Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $e->getMessage()));
    		$this->_redirectReferer(); 
            return false;
    	}
	}

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
            $fname = $_FILES['import_csvstatus']['name']; //file name
            $adminuser = Mage::getSingleton('admin/session')->getUser()->getUsername();
            $ext = pathinfo($fname, PATHINFO_EXTENSION);
            $newFile = 'orderstatus_'.$adminuser.'_'.date('Y-m-d_H-i-s',time()).'.'.$ext;
            
            $uploader->save($path,$newFile);
            

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
