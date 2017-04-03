<?php
class Iksula_Outofstocksubscription_Model_Cron{

	public function Outofstocksubscriptiondelete()
	{
	   	$model = Mage::getModel('outofstocksubscription/info')->getCollection();
		try {
		    $model->addFieldToFilter('is_active','deactive');
		    $model->walk('delete');
		    echo "Data deleted successfully.";

		} catch (Exception $e){
		    echo $e->getMessage();
		}
	}

}


