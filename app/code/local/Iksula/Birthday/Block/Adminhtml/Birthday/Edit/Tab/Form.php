<?php
class Iksula_Birthday_Block_Adminhtml_Birthday_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("birthday_form", array("legend"=>Mage::helper("birthday")->__("Item information")));

				
						$fieldset->addField("customer_id", "text", array(
						"label" => Mage::helper("birthday")->__("Customer Id"),
						"name" => "customer_id",
						));
					
						$fieldset->addField("first_name", "text", array(
						"label" => Mage::helper("birthday")->__("First Name"),
						"name" => "first_name",
						));
					
						$fieldset->addField("last_name", "text", array(
						"label" => Mage::helper("birthday")->__("Last Name"),
						"name" => "last_name",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("birthday")->__("Email"),
						"name" => "email",
						));
					
						$fieldset->addField("coupon", "text", array(
						"label" => Mage::helper("birthday")->__("Coupon"),
						"name" => "coupon",
						));
									
						$fieldset->addField('birthdate', 'image', array(
						'label' => Mage::helper('birthday')->__('Birth Date'),
						'name' => 'birthdate',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('anniversary', 'date', array(
						'label'        => Mage::helper('birthday')->__('Anniversary'),
						'name'         => 'anniversary',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$fieldset->addField("customer_group", "text", array(
						"label" => Mage::helper("birthday")->__("Customer Group"),
						"name" => "customer_group",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('coupon_created_date', 'date', array(
						'label'        => Mage::helper('birthday')->__('Coupon Created Date'),
						'name'         => 'coupon_created_date',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('coupon_expire_date', 'date', array(
						'label'        => Mage::helper('birthday')->__('Coupon Expire Date'),
						'name'         => 'coupon_expire_date',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));				
						 $fieldset->addField('coupon_status', 'select', array(
						'label'     => Mage::helper('birthday')->__('Coupon Status'),
						'values'   => Iksula_Birthday_Block_Adminhtml_Birthday_Grid::getValueArray11(),
						'name' => 'coupon_status',
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('date_mail_to_send', 'date', array(
						'label'        => Mage::helper('birthday')->__('Date Mail to be Send'),
						'name'         => 'date_mail_to_send',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$fieldset->addField("email_send", "text", array(
						"label" => Mage::helper("birthday")->__("Email send Status"),
						"name" => "email_send",
						));
					
						$fieldset->addField("no_of_email_send", "text", array(
						"label" => Mage::helper("birthday")->__("No of Email Send"),
						"name" => "no_of_email_send",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getBirthdayData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getBirthdayData());
					Mage::getSingleton("adminhtml/session")->setBirthdayData(null);
				} 
				elseif(Mage::registry("birthday_data")) {
				    $form->setValues(Mage::registry("birthday_data")->getData());
				}
				return parent::_prepareForm();
		}
}
