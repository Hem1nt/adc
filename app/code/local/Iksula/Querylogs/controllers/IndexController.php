<?php
class Iksula_Querylogs_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

	  $this->loadLayout();
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Querylogs"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("querylogs", array(
                "label" => $this->__("Querylogs"),
                "title" => $this->__("Querylogs")
		   ));

      $this->renderLayout();

    }

    // public function deleteAction() {
    //   $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
    //   $currentdate = date('Y-m-d H:i:s', $currentTimestamp);

    //   $requiredDate = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime($currentdate."-7 days"));
    //   $model = Mage::getModel("querylogs/information")->getCollection();

    //   try {
    //     $model->addFieldToFilter('date_of_query', array('lt' => $requiredDate ));
    //     $model->walk('delete');
    //     echo "Data deleted successfully.";

    //   } catch (Exception $e){
    //       echo $e->getMessage();
    //   }
    // }
}
