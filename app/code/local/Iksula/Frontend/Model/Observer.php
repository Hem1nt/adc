<?php
class Iksula_Frontend_Model_Observer
{
	public function catalogProductCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        $collection->getSelect()->joinLeft(
            array('_inventory_table'=>$collection->getTable('cataloginventory/stock_item')),
            "_inventory_table.product_id = e.entity_id",
            array('is_in_stock', 'manage_stock')
        );
        $collection->addExpressionAttributeToSelect(
            'on_top',
            '(CASE WHEN (((_inventory_table.use_config_manage_stock = 1) AND (_inventory_table.is_in_stock = 1)) OR  ((_inventory_table.use_config_manage_stock = 0) AND (1 - _inventory_table.manage_stock + _inventory_table.is_in_stock >= 1))) THEN 1 ELSE 0 END)',
            array()
        );
        $collection->getSelect()->order('on_top DESC');
        // Make sure on_top is the first order directive
        $order = $collection->getSelect()->getPart('order');
        array_unshift($order, array_pop($order));
        $collection->getSelect()->setPart('order', $order);
    }

    public function changeUserGroup(Varien_Event_Observer $observer) {}
    public function changeUserGroupOLD(Varien_Event_Observer $observer) {
            $event = $observer->getEvent();
            $order = $event->getOrder();
            $orderHelper = Mage::helper('frontend/order');
            $guestUserEmail =$order->getBillingAddress()->getEmail();
            $guestOrdersCountByEmail = $orderHelper->getGuestOrdersCountByEmail($guestUserEmail);
            $_customer = Mage::getSingleton('customer/session')->getCustomer();
            $userEmail =$_customer->getData('email');
            $ordersCountByEmail = $orderHelper->getOrdersCountByEmail($userEmail);
            // $selectedgroup = 6;
            // $notLoggedIn = 0;
            $regularGroupId = 6;
            $generalGroupId = 1;
            $customerGroupId = $_customer->getGroupId();

            $customersOrdersCount = $orderHelper->getCustomersOrdersCount($userEmail);
            $guestOrdersCount = $orderHelper->getCustomersOrdersCount($guestUserEmail);
            // echo $customersOrdersCount;
            $orderCollection = $orderHelper->_orderCollection();
            $orderCollection->addFieldToFilter('customer_email',$guestUserEmail);
 
            /* Start of logic-->Customer group change of register customer and its orders*/
            if($customersOrdersCount >= 2){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($regularGroupId);
                    $_order->save();
                }

                $_customer->setGroupId($regularGroupId);
                $_customer->save();

            }
            /* End of logic-->Customer group change of register customer and its orders*/
            
            /* Start of logic-->Customer group change of guest customer's orders*/

            if($guestOrdersCount >= 2){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($regularGroupId);
                    $_order->save();
                } 
            }

            /* End of logic-->Customer group change of guest customer's orders*/

            /* Start of logic-->Customer group change of register customer and its orders*/
            
            if($customersOrdersCount == 1){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($generalGroupId);
                    $_order->save();
                }

                $_customer->setGroupId($generalGroupId);
                $_customer->save(); 
            }

            /* End of logic-->Customer group change of register customer and its orders*/

            /* Start of logic-->Customer group change of guest customer's orders*/
            
            if($guestOrdersCount == 1){
                foreach ($orderCollection as $_order) {
                    $_order->setCustomerGroupId($generalGroupId);
                    $_order->save();
                }
            }

            /* End of logic-->Customer group change of guest customer's orders*/
            

            ///dont change customer group if it is already regular.///////////
            //---------------------------------------------------------//
            // if($customerGroupId == 1 && $ordersCountByEmail>1) {
            //     $orderCollection = Mage::getModel('sales/order')->getCollection();
            //     $orderCollection->addFieldToFilter('customer_email',$userEmail);
            //     foreach ($orderCollection as $_order) {
            //         $_order->setCustomerGroupId($selectedgroup);
            //         $_order->save();
            //     }

            //     $_customer->setGroupId($selectedgroup);
            //     $_customer->save();
            // }
            // if($guestOrdersCountByEmail>1){
            //  $orderCollection = Mage::getModel('sales/order')->getCollection();
            //     $orderCollection->addFieldToFilter('customer_email',$guestUserEmail);
            //     foreach ($orderCollection as $_order) {
            //         $_order->setCustomerGroupId($selectedgroup);
            //         $_order->save();
            //     }
            // }
            //---------------------------------------------------------//

			////Mark old orders as regular customer order of current customer...........

        }

        public function checkCustomerGroup(Varien_Event_Observer $observer)
        {
        	$customer=$observer->getEvent()->getCustomer();
        	$selectedgroup = $customer->getData('group_id');
        	
        	if($customer->getId()){
        		if($customer->getOrigData('group_id')!=$customer->getData('group_id')){
        			$userEmail = $customer->getEmail();
        			$orderHelper = Mage::helper('frontend/order');
        			$ordersCountByEmail = $orderHelper->getOrdersCountByEmail($userEmail);
        			if($ordersCountByEmail>1){
        				$orderCollection = Mage::getModel('sales/order')->getCollection();
        				$orderCollection->addFieldToFilter('customer_email',$userEmail);
        				foreach ($orderCollection as $_order) {
        					$_order->setCustomerGroupId($selectedgroup);
        					$_order->save();
        				}
        			}
        		}
        	}
        }
    
}