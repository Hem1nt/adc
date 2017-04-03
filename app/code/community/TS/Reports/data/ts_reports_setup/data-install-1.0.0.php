<?php

function initReportItems(){
	Mage::helper('ts_reports')->setRefreshDate(null);
	$reports = Mage::getResourceModel('ts_reports/reportitem')->init();
}

// ----------------------------------------------

if(Mage::helper('catalog/product_flat')->isEnabled()){
	$appEmulation = Mage::getModel('core/app_emulation');
	$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMINHTML);
	initReportItems();

	$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
} else {
	initReportItems();
}

