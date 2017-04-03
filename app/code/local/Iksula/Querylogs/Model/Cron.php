<?php
class Iksula_Querylogs_Model_Cron{
	public function Querylogsdelete() {
      $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
      $currentdate = date('Y-m-d H:i:s', $currentTimestamp);

      $requiredDate = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime($currentdate."-7 days"));
      $model = Mage::getModel("querylogs/information")->getCollection();

      try {
        $model->addFieldToFilter('date_of_query', array('lt' => $requiredDate ));
        $model->walk('delete');
        echo "Data deleted successfully.";

      } catch (Exception $e){
          echo $e->getMessage();
      }
    }
}
