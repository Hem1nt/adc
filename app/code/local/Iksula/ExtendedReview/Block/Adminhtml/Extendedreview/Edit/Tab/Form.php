<?php
class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("extendedreview_form", array("legend"=>Mage::helper("extendedreview")->__("Item information")));

				
						$fieldset->addField("review_id", "text", array(
						"label" => Mage::helper("extendedreview")->__("Review Id"),
						"name" => "review_id",
						));

						$fieldset->addField("reviewer_name", "text", array(
						"label" => Mage::helper("extendedreview")->__("Review Name"),
						"name" => "reviewer_name",
						));

						$fieldset->addField("product_name", "text", array(
						"label" => Mage::helper("extendedreview")->__("Product Name"),
						"name" => "product_name",
						));
					
						$fieldset->addField("comment_id", "text", array(
						"label" => Mage::helper("extendedreview")->__("Comment Id"),
						"name" => "comment_id",
						));
					
						$fieldset->addField("comment", "text", array(
						"label" => Mage::helper("extendedreview")->__("Comment"),
						"name" => "comment",
						));
					
						$fieldset->addField("customer_id", "text", array(
						"label" => Mage::helper("extendedreview")->__("Customer Id"),
						"name" => "customer_id",
						));
					
						$fieldset->addField("approved_by", "text", array(
						"label" => Mage::helper("extendedreview")->__("Approved By"),
						"name" => "approved_by",//Mage::getSingleton('admin/session')->getUser()->getUserId(),
						));
									
						//  $fieldset->addField('status', 'label', array(
						// 'label'     => Mage::helper('extendedreview')->__('Status'),
						// 'values'   => Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Grid::getValueArray5(),
						// 'name' => 'status',
						// ));
				if (Mage::registry('extendedreview_data')->getApprovedBy() == "") {
				    Mage::registry('extendedreview_data')->setApprovedBy(Mage::getSingleton('admin/session')->getUser()->getUsername());
				}

				if (Mage::getSingleton("adminhtml/session")->getExtendedreviewData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getExtendedreviewData());
					Mage::getSingleton("adminhtml/session")->setExtendedreviewData(null);
				} 
				elseif(Mage::registry("extendedreview_data")) {
				    $form->setValues(Mage::registry("extendedreview_data")->getData());
				}
				return parent::_prepareForm();
		}
}
