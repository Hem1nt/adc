<?php
class Iksula_Drc_Block_Info_Drc extends Mage_Payment_Block_Info
{
	protected function _prepareSpecificInformation($transport = null)
	{
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		$info = $this->getInfo();
		$transport = new Varien_Object();
		$transport = parent::_prepareSpecificInformation($transport);
		// $transport->addData(array(
		// 	Mage::helper('payment')->__('Billedcc Issuing Bank') => $info->getBilledccCcBank()
		// ));
		return $transport;
	}
}
