
<?php

Class Iksula_Reports_Block_Adminhtml_Renderer_Group extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		$group_id = $row->getData('customer_group_id');
		$groupname = Mage::getModel('customer/group')->load($group_id)->getCustomerGroupCode();
        return $groupname;

	}

}

?>
