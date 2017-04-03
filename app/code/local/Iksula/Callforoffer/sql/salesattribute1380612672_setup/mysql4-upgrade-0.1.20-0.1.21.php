<?php
$this->startSetup();
// Add column to grid table
// $this->getConnection()->addColumn(
//     $this->getTable('sales/order_grid'),
//     'order_placed_country',
//     "varchar(255) not null default ''"
// );
// Add key to table for this field,
// it will improve the speed of searching & sorting by the field
$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'order_placed_country',
    'order_placed_country'
);
// Now you need to fullfill existing rows with data from address table
$select = $this->getConnection()->select();
$select->join(
    array('order'=>$this->getTable('sales/order')),
    $this->getConnection()->quoteInto(
        'order.entity_id = order_grid.entity_id'
    ),
    array(
        'order_placed_country' => 'order_placed_country',
        )
);
$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);
$this->endSetup();
