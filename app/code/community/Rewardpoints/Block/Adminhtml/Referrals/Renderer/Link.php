
<?php 

Class Rewardpoints_Block_Adminhtml_Referrals_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		$rewardpoints_referral_id = $row->getData('rewardpoints_referral_id');
		$baseurl= Mage::helper("adminhtml")->getUrl("rewardpoints/adminhtml_referrals/sendemail", array('rewardpoints_referral_id'=>$rewardpoints_referral_id));
		return "<a href='".$baseurl."'>Send Link</a>";
	}
}

?>
