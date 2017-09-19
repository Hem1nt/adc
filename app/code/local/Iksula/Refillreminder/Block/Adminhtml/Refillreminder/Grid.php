<?php

class Iksula_Refillreminder_Block_Adminhtml_Refillreminder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("refillreminderGrid");
				$this->setDefaultSort("reminder_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("refillreminder/refillreminder")->getCollection();
				// echo "<pre>";
				// var_dump($collection);die;
				//echo $collection->getSelect(); exit;
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("reminder_id", array(
				"header" => Mage::helper("refillreminder")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "reminder_id",
				));

				//order Id
				$this->addColumn("order_Id", array(
				"header" => Mage::helper("refillreminder")->__("Order Id"),
				"index" => "order_Id",
				));

				//customer name
				$this->addColumn("customer_name", array(
				//"align" =>"right",
				"width" => "50px",
				"header" => Mage::helper("refillreminder")->__("Customer Name"),
				"index" => "customer_name",
				));
                //customer email
				$this->addColumn("customer_email", array(
				"header" => Mage::helper("refillreminder")->__("Customer Email"),
				"index" => "customer_email",
				));
				
				
				//orderIds
				
				
			/*	$this->addColumn("product_sku", array(
				"header" => Mage::helper("refillreminder")->__("Product SKU"),
				"index" => "product_sku",
				));*/

			/*	$this->addColumn("product_name", array(
				"header" => Mage::helper("refillreminder")->__("Product Name"),
				"index" => "product_name",
				));
*/

				$this->addColumn("customer_telephone", array(
				"header" => Mage::helper("refillreminder")->__("Phone # "),
				"index" => "customer_telephone",
				));

				/*$this->addColumn("modified_date", array(
				"header" => Mage::helper("refillreminder")->__("Purchase Date"),
				"index" => "modified_date",
				'type'      => 'date',
				));*/
				$this->addColumn("created_date", array(
				"header" => Mage::helper("refillreminder")->__("Created at"),
				"index" => "created_date",
				'type'      => 'date',
				));
				
				$this->addColumn("reminder_days", array(
				"header" => Mage::helper("refillreminder")->__("Remind in"),
				"index" => "reminder_days",
				//'type'      => 'date',
				));
				
				
				$this->addColumn("next_mail_on", array(
				"header" => Mage::helper("refillreminder")->__("Next reminder on"),
				"index" => "next_mail_on",
				 "type" => "date",
				));

				$this->addColumn("status", array(
				"header" => Mage::helper("refillreminder")->__("Status"),
				"index"  => "remind_flag",
				'type'   => 'options',
				'options' => array('0' => 'Disabled','1' => 'Enabled')
				));

				/*$this->addColumn("comment", array(
				"header" => Mage::helper("refillreminder")->__("Comment"),
				"index" => "comment",
				));*/
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}
		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('reminder_id');
			$this->getMassactionBlock()->setFormFieldName('reminder_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_refillreminder', array(
					 'label'=> Mage::helper('refillreminder')->__('Remove Refillreminder'),
					 'url'  => $this->getUrl('*/adminhtml_refillreminder/massRemove'),
					 'confirm' => Mage::helper('refillreminder')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='Yes';
			$data_array[1]='No';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(Iksula_Refillreminder_Block_Adminhtml_Refillreminder_Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		public function styleDate( $value,$row,$column,$isExport )
		{
			$locale = Mage::app()->getLocale();
			//$date = $locale->date( $value, $locale->getDateFormat(), $locale->getLocaleCode(), false )->toString( $locale->getDateFormat() ) ;
			//$date = $row->getLastMailSent()."#".$row->getReminderDays();
			$date = date('M d, Y', strtotime($row->getLastMailSent(). ' + '.$row->getReminderDays().' days'));
			return $date;
		}

}