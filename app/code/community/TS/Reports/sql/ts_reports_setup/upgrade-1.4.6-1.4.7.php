<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
	->addColumn(
		$installer->getTable('ts_reports/reportitem'), 
		'tz_offset',  
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'nullable'  => false,
			'default'	=> '0',
			'comment'   => 'Timezone offset (in seconds)'
		)
	);	
$installer->getConnection()->addColumn(
		$installer->getTable('ts_reports/reportitem'), 
		'tz_name',  
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
			'length'    => '255',
			'nullable'  => false,
			'default'	=> '',
			'comment'   => 'Timezone name'
		)
	);

$installer->endSetup();
