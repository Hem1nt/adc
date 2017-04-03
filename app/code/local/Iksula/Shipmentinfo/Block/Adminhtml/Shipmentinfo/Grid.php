<?php

class Iksula_Shipmentinfo_Block_Adminhtml_Shipmentinfo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("shipmentinfoGrid");
				$this->setDefaultSort("entity_id");
				$this->setDefaultDir("DESC");
				// $this->setUseAjax(true);
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				// $filter = $this->getParam($this->getVarNameFilter(), null);
				//  if (!is_null($filter))  {
			 //         $filter = $this->helper('adminhtml')->prepareFilterString($filter);
			 //         if ($filter['total_shipped_qty']) {
			 //              $isFilter = true;
			 //         }
			 //    }
				$collection = Mage::getModel("sales/order")->getCollection();
				$collection->getSelect()->joinLeft( array('shipment'=> 'sales_flat_shipment'), 'main_table.entity_id=shipment.order_id', array('total_shipped_qty' => new Zend_Db_Expr('IFNULL(SUM(shipment.total_qty),0)'),'non_shipped_qty' => 'IFNULL((main_table.total_qty_ordered-sum(shipment.total_qty)),0)'))->group('main_table.entity_id');
				// if ($isFilter) {
			 //        $collection->addFieldToFilter('total_shipped_qty', $filter['total_shipped_qty']);
			 //    }
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("entity_id", array(
				"header" => Mage::helper("shipmentinfo")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "entity_id",
				'filter_index'=>'main_table.entity_id',
				));
                
				$this->addColumn("increment_id", array(
				"header" => Mage::helper("shipmentinfo")->__("Order Id"),
				"index" => "increment_id",
				'filter_index'=>'main_table.increment_id',
				));

				$this->addColumn("customer_order_increment_id", array(
				"header" => Mage::helper("shipmentinfo")->__("Custom Order Id"),
				"index" => "customer_order_increment_id",
				));

				$this->addColumn('created_at', array(
					'header'    => Mage::helper('shipmentinfo')->__('Date'),
					'index'     => 'created_at',
					'type'      => 'datetime',
					'filter_index'=>'main_table.created_at',
				));
				$this->addColumn("total_items", array(
				"header" => Mage::helper("shipmentinfo")->__("Total Items Qty"),
				"index" => "total_qty_ordered",
				'filter_index'=>'main_table.total_qty_ordered',
				));
				$this->addColumn("total_shipped_qty", array(
				"header" => Mage::helper("shipmentinfo")->__("Shipped Items"),
				'type'  => 'text',
				'index' =>"total_shipped_qty",
				'filter_index' =>"shipment.total_shipped_qty",
				'filter' => false,
				'sortable'  => false,
				// 'renderer' => 'Iksula_Shipmentinfo_Block_Adminhtml_ShippedItem'
				// 'filter_condition_callback' => array($this, 'shippedItemTotalFilter')
				));
				$this->addColumn("shipped_item_sku", array(
				"header" => Mage::helper("shipmentinfo")->__("Shipped Item Sku"),
				'filter' => false,
				'sortable'  => false,
				'renderer' => 'Iksula_Shipmentinfo_Block_Adminhtml_ShippedItemSku'
				));
				$this->addColumn("non_shipped_items", array(
				"header" => Mage::helper("shipmentinfo")->__("Non Shipped Items"),
				// 'index' => 'non_shipped_qty',
				'filter' => false,
				'sortable'  => false,
				'renderer' => 'Iksula_Shipmentinfo_Block_Adminhtml_NonShippedItem'
				));
				$this->addColumn("non_shipped_item_sku", array(
				"header" => Mage::helper("shipmentinfo")->__("Non Shipped Item Sku"),
				'filter' => false,
				'sortable'  => false,
				'renderer' => 'Iksula_Shipmentinfo_Block_Adminhtml_NonShippedItemSku'
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   
			   //
			   return Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$row->getEntityId()));
		}

			// protected function shippedItemTotalFilter($collection, $column)
			// {
			// 	if (!$value = $column->getFilter()->getValue()) 
			// 	{
			// 	    return $this;
			// 	}
			// 	$this->getCollection()->getSelect()->where('total_shipped_qty like ?', "1");

			// 	return $this;
			// }
		
		protected function _prepareMassaction()
		{
			// $this->setMassactionIdField('entity_id');
			// $this->getMassactionBlock()->setFormFieldName('entity_ids');
			// $this->getMassactionBlock()->setUseSelectAll(true);
			// $this->getMassactionBlock()->addItem('remove_shipmentinfo', array(
			// 		 'label'=> Mage::helper('shipmentinfo')->__('Remove Shipmentinfo'),
			// 		 'url'  => $this->getUrl('*/adminhtml_shipmentinfo/massRemove'),
			// 		 'confirm' => Mage::helper('shipmentinfo')->__('Are you sure?')
			// 	));
			// return $this;
		}
			

}