<?php
class Iksula_Trustedcompany_Block_Adminhtml_Reviews_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("trustedcompany_form", array("legend"=>Mage::helper("trustedcompany")->__("Item information")));

				
						$fieldset->addField("review_made_by", "text", array(
						"label" => Mage::helper("trustedcompany")->__("Reviewer Email "),
						"name" => "review_made_by",
						));
					
						$fieldset->addField("subject", "textarea", array(
						"label" => Mage::helper("trustedcompany")->__("Subject"),
						"name" => "subject",
						));
					
						$fieldset->addField("body", "textarea", array(
						"label" => Mage::helper("trustedcompany")->__("Body"),
						"name" => "body",
						));
					
						$fieldset->addField("rating", "text", array(
						"label" => Mage::helper("trustedcompany")->__("Rating"),
						"name" => "rating",
						));
					
						$fieldset->addField("reviewer", "text", array(
						"label" => Mage::helper("trustedcompany")->__("Reviewer"),
						"name" => "reviewer",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('date', 'date', array(
						'label'        => Mage::helper('trustedcompany')->__('Date'),
						'name'         => 'date',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

				if (Mage::getSingleton("adminhtml/session")->getReviewsData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getReviewsData());
					Mage::getSingleton("adminhtml/session")->setReviewsData(null);
				} 
				elseif(Mage::registry("reviews_data")) {
				    $form->setValues(Mage::registry("reviews_data")->getData());
				}
				return parent::_prepareForm();
		}
}
