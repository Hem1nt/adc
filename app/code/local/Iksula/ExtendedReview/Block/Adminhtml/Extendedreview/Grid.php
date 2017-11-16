<?php

class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("extendedreviewGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{       
			    
				$collection = Mage::getModel("extendedreview/extendedreview")->getCollection();
			    //->addFieldToSelect('*');

				//$collection->getSelect()->group('id');
					//;
				$this->setCollection($collection);
			
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("extendedreview")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("review_id", array(
				"header" => Mage::helper("extendedreview")->__("Review Id"),
				"index" => "review_id",
				));
				$this->addColumn("comment_id", array(
				"header" => Mage::helper("extendedreview")->__("Comment Id"),
				"index" => "comment_id",
				));
				$this->addColumn("comment", array(
				"header" => Mage::helper("extendedreview")->__("Comment"),
				"index" => "comment",
				'renderer' => 'Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Renderer_Comment',
		
				
				));
				$this->addColumn("customer_id", array(
				"header" => Mage::helper("extendedreview")->__("Customer Id"),
				"index" => "customer_id",
				));
				$this->addColumn("approved_by", array(
				"header" => Mage::helper("extendedreview")->__("Approved By"),
				"index" => "approved_by",
				));
						$this->addColumn('status', array(
						'header' => Mage::helper('extendedreview')->__('Status'),
						'index' => 'status',
						'type' => 'options',
						'options'=>Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Grid::getOptionArray5(),				
						));
						
				$this->addColumn("created_at", array(
				"header" => Mage::helper("extendedreview")->__("Created At"),
				"index" => "created_at",
				));
				$this->addColumn("updated_at", array(
				"header" => Mage::helper("extendedreview")->__("updated At"),
				"index" => "updated_at",
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
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_extendedreview', array(
					 'label'=> Mage::helper('extendedreview')->__('Remove Extendedreview'),
					 'url'  => $this->getUrl('*/adminhtml_extendedreview/massRemove'),
					 'confirm' => Mage::helper('extendedreview')->__('Are you sure?')
				));
			$statuses = array('1'=>'Not Approved','2'=>'Approved');
			array_unshift($statuses, array('label'=>'', 'value'=>''));
			$this->getMassactionBlock()->addItem('changes_status', array(
					 'label'=> Mage::helper('extendedreview')->__('Change Comment Status'),
					 'url'  => $this->getUrl('*/adminhtml_extendedreview/changeStatus', array('_current'=>true)),
		             'additional' => array(
					                    'visibility' => array(
					                         'name' => 'status',
					                         'type' => 'select',
					                         'class' => 'required-entry',
					                         'label' => Mage::helper('extendedreview')->__('Status'),
					                         'values' => $statuses
					                     )
					)
				));			
			return $this;
		}
			
		static public function getOptionArray5()
		{
            $data_array=array(); 
			$data_array[1]='Not Approved';
			$data_array[2]='Approved';
            return($data_array);
		}
		static public function getValueArray5()
		{
            $data_array=array();
			foreach(Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Grid::getOptionArray5() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}