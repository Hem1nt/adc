<?php
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('sales/order_grid')}` CHANGE `browser_details` `browser_details` varchar(255) NOT NULL;
");
// Add column to grid table
// $this->getConnection()->addColumn(
//     $this->getTable('sales/order_grid'),
//     'customer_order_increment_id',
//     "varchar(255) not null default ''"
// );
// Add key to table for this field,
// it will improve the speed of searching & sorting by the field
$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'browser_details',
    'browser_details'
);
// Now you need to fullfill existing rows with data from address table
$select = $this->getConnection()->select();
$select->join(
    array('order'=>$this->getTable('sales/order')),
    $this->getConnection()->quoteInto(
        'order.entity_id = order_grid.entity_id'
    ),
    array('browser_details' => 'browser_details')
);
$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);
$this->endSetup();
