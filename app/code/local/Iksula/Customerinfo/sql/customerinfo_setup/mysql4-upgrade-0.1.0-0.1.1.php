<?php
$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'contact_number', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Contact Number',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 1,
    'source'=> 'profile/entity_contact_number',
));
$customer = Mage::getModel('customer/customer');
$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
$setup->addAttributeToSet('customer', $attrSetId, 'General', 'contact_number');
Mage::getSingleton('eav/config')
->getAttribute('customer', 'contact_number')
->setData('used_in_forms', array('adminhtml_customer','customer_account_edit'))
->save();

$installer->endSetup();
