<?php

class Iksula_Echecksteps_Block_Adminhtml_Echecksteps_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("echeckstepsGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("echecksteps/echecksteps")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
				print_r($collection);
				exit();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("echecksteps")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("steps_order", array(
				"header" => Mage::helper("echecksteps")->__("Steps"),
				"index" => "steps_order",
				));
				$this->addColumn('image_name', array(
				'header' => Mage::helper('echecksteps')->__('Image'),
				'index' => 'image_name',
				'renderer' => 'Iksula_Echecksteps_Block_Adminhtml_Echecksteps_Renderer_Image',				
				));
				$this->addColumn("created_at", array(
				"header" => Mage::helper("echecksteps")->__("Created At"),
				"index" => "created_at",
				));
				$this->addColumn("updated_at", array(
				"header" => Mage::helper("echecksteps")->__("Updated At"),
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
			$this->getMassactionBlock()->addItem('remove_echecksteps', array(
					 'label'=> Mage::helper('echecksteps')->__('Remove Echecksteps'),
					 'url'  => $this->getUrl('*/adminhtml_echecksteps/massRemove'),
					 'confirm' => Mage::helper('echecksteps')->__('Are you sure?')
				));
			return $this;
		}
			

}