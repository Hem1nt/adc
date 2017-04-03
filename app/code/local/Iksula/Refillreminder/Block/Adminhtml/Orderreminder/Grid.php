<?php

class Iksula_Refillreminder_Block_Adminhtml_Orderreminder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("orderreminderGrid");
				$this->setDefaultSort("order_inc_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("refillreminder/orderreminder")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("order_inc_id", array(
				"header" => Mage::helper("refillreminder")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "order_inc_id",
				));
                
				$this->addColumn("customer_email", array(
				"header" => Mage::helper("refillreminder")->__("Customer Email"),
				"index" => "customer_email",
				));
				$this->addColumn("product_sku", array(
				"header" => Mage::helper("refillreminder")->__("Product SKU"),
				"index" => "product_sku",
				));

				$this->addColumn("product_name", array(
				"header" => Mage::helper("refillreminder")->__("Product Name"),
				"index" => "product_name",
				));

				$this->addColumn("customer_telephone", array(
				"header" => Mage::helper("refillreminder")->__("Phone # "),
				"index" => "customer_telephone",
				));

				$this->addColumn("modified_date", array(
				"header" => Mage::helper("refillreminder")->__("Purchase Date"),
				"index" => "modified_date",
				'type'      => 'date',
				));

				$this->addColumn("last_mail_sent", array(
				"header" => Mage::helper("refillreminder")->__("Refill Due Order"),
				"index" => "last_mail_sent",
				'type'      => 'date',
				));

				$this->addColumn("next_mail_on", array(
				"header" => Mage::helper("refillreminder")->__("Next reminder on"),
				"index" => "next_mail_on",
				 "type" => "date",
				));

				$this->addColumn("order_id", array(
				"header" => Mage::helper("refillreminder")->__("Order Id"),
				"index" => "order_id",
				));

				$this->addColumn("order_total", array(
				"header" => Mage::helper("refillreminder")->__("Grand Total"),
				"index" => "order_total",
				"frame_callback" => array($this,'styleGrandTotal')
				));

				$this->addColumn("remind_flag", array(
				"header" => Mage::helper("refillreminder")->__("Status"),
				"index"  => "remind_flag",
				'type'   => 'options',
				'options' => array('0' => 'Disabled','1' => 'Enabled')
				));


				$this->addColumn("comment", array(
				"header" => Mage::helper("refillreminder")->__("Comment"),
				"index" => "comment",
				));
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
			$this->setMassactionIdField('order_inc_id');
			$this->getMassactionBlock()->setFormFieldName('order_inc_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_orderreminder', array(
					 'label'=> Mage::helper('refillreminder')->__('Remove Orderreminder'),
					 'url'  => $this->getUrl('*/adminhtml_orderreminder/massRemove'),
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
		
		public function styleGrandTotal($value)
		{
			return "US$ ".$value;
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