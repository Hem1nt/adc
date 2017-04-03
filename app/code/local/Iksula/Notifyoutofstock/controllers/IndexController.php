<?php
class Iksula_Notifyoutofstock_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

	  $this->loadLayout();
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Notifyoutofstock"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("notifyoutofstock", array(
                "label" => $this->__("Notifyoutofstock"),
                "title" => $this->__("Notifyoutofstock")
		   ));

      $this->renderLayout();

    }

    // public function deleteAction() {
    //   $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
    //   $currentdate = date('Y-m-d H:i:s', $currentTimestamp);

    //   $requiredDate = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime($currentdate."-2 days"));
    //   $model = Mage::getModel("notifyoutofstock/notifyoutofstock")->getCollection();
    //   try {
    //     $model->addFieldToFilter('date', array('lt' => $requiredDate ));
    //     $model->walk('delete');
    //     echo "Data deleted successfully.";

    //   } catch (Exception $e){
    //       echo $e->getMessage();
    //   }
    // }
}
