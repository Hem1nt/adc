<?php
class Iksula_Notifyoutofstock_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function notifyOutofStock($product)
	{
		$model = Mage::getModel("notifyoutofstock/notifyoutofstock");
		$this->checkProduct($product);
		// $data['product_id'] = $product->getId();
		// $data['count'] =1;
		// $data['date'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		// $model->setData($data)->save();
	}
	public function sendNotification()
	{

		$currentTimestamp = Mage::getModel('core/date')->timestamp(time());
		$first = date('Y-m-d 00:00:00', $currentTimestamp);
		$last = date('Y-m-d 23:59:59', $currentTimestamp);

		$model = Mage::getModel("notifyoutofstock/notifyoutofstock")->getCollection();
		$model->addFieldToFilter('date', array(
       		 'from' => $first,
       		 'to' => $last,
       	 	 'date' => true,
        ));

   //       echo $model->getSelect()->group('product_id');



		// foreach ($model as $key => $value) {
		// 		print_r($model->getData());
		// }
		// exit();
	}

	public function checkProduct($product)
	{
		$currentTimestamp = Mage::getModel('core/date')->timestamp(time());
		$first = date('Y-m-d 00:00:00', $currentTimestamp);
		$last = date('Y-m-d 23:59:59', $currentTimestamp);
		$notifyoutofstockModel = Mage::getModel("notifyoutofstock/notifyoutofstock");

		$model = Mage::getModel("notifyoutofstock/notifyoutofstock")->getCollection();
		$model->addFieldToFilter('product_id',$product->getId());
		$model->addFieldToFilter('date', array(
       		 'from' => $first,
       		 'to' => $last,
       	 	 'date' => true,
        ));

		$collection = $model->getSize();
		$firstItem = $model->getFirstItem();


		if($collection > 0) {
			$data['product_sku'] = $product->getSku();
			$data['product_name'] = $product->getName();
			$data['count'] =$firstItem->getCount() + 1;
			$data['date'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			$notifyoutofstockModel->load($firstItem->getNotifyId())->addData($data)->save();
		} else {
			$data['product_sku'] = $product->getSku();
			$data['product_name'] = $product->getName();
			$data['product_id'] = $product->getId();
			$data['count'] =1;
			$data['date'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			$notifyoutofstockModel->setData($data)->save();
		}
	}

}

