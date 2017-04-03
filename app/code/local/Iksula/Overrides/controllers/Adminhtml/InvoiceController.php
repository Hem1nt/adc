<?php
Class Iksula_Overrides_Adminhtml_InvoiceController extends Mage_Adminhtml_Controller_Action{
	public function deleteAction(){
		// $order_id = $this->getRequest()->getPost('order_ids');
		// $orderIds = explode(',',$order_id);
		// $invoiceEntityIds = array();
		// foreach($orderIds as $orderId){
		// 	$orderCollection = Mage::getModel('sales/order')->load($orderId);
		// 	if ($orderCollection->hasInvoices()) {
		// 	    foreach ($orderCollection->getInvoiceCollection() as $invoice) {
		// 	        $invoiceEntityIds[] = $invoice->getEntityId();
		// 	    }
		// 	}
		// }
		$invoiceIds = $this->getRequest()->getPost('invoice_ids');
		$invoiceIdsCounter = count($invoiceIds);
		if($invoiceIdsCounter == 0){
			$message = 'Please select invoice to delete';
			Mage::getSingleton('adminhtml/session')->addError($message);
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_invoice/index"));
			return;
		}
		// $invoiceEntityIds = explode(',',$invoiceIds);
		$errorConuter = 0;
		$db_write1 = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix(); 
		foreach ($invoiceIds as $invoiceEntityId) {
			$query = 'SELECT *  FROM `' . $tablePrefix . 'sales_flat_invoice` WHERE entity_id='.$invoiceEntityId;
			$results = $db_write1->fetchAll($query);
			if(empty($results)){
				$errorConuter++;
			}else{
				$sql1 = 'DELETE FROM `' . $tablePrefix . 'sales_flat_invoice_grid` WHERE entity_id='.$invoiceEntityId;
				$sql2 = 'DELETE FROM `' . $tablePrefix . 'sales_flat_invoice` WHERE entity_id='.$invoiceEntityId;
				$sql3 = 'DELETE FROM `' . $tablePrefix . 'sales_flat_invoice_item` WHERE parent_id='.$invoiceEntityId;
				$sql4 = 'DELETE FROM `' . $tablePrefix . 'sales_flat_invoice_comment` WHERE parent_id='.$invoiceEntityId;
	 			$db_write1->query($sql1); 
	 			$db_write1->query($sql2);
	 			$db_write1->query($sql3);
	 			$db_write1->query($sql4);
			}
		}
		if($errorConuter){
			$message = 'Please provide valide invoice id to delete';
			Mage::getSingleton('adminhtml/session')->addError($message);
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_invoice/index"));
			return;
		}else{
			$message = 'Invoice successfully deleted';
			Mage::getSingleton('adminhtml/session')->addSuccess($message);
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_invoice/index"));
			return;
		}
	}
} 