<?php
class Iksula_Medical_Model_Observer
{

	public function medicalhistoryorderidset(Varien_Event_Observer $observer)
	{
		$event = $observer->getEvent();
		$order = $event->getOrder(); 
		$orderId = $order->getRealOrderId();

		$insertId = Mage::getSingleton('core/session')->getInsertId();
		
			//Mage::getSingleton('core/session')->unsInsertId();
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

          	$order->setData('physicianname',$physicianname);
			$order->setData('physiciantelephone',$physiciantelephone);
			$order->setData('drug_allergies',$drug_allergies);
			$order->setData('current_medications',$current_medications);
			$order->setData('current_treatments',$current_treatments);
			$order->setData('smoke',$smoke);
			$order->setData('drink',$drink);
			$order->save();           

		}

}
