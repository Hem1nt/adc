<?php
class Iksula_Homepagebanner_Block_Adminhtml_Homeslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("homepagebanner_form", array("legend"=>Mage::helper("homepagebanner")->__("Item information")));

		
		$fieldset->addField("name", "text", array(
			"label" => Mage::helper("homepagebanner")->__("Name"),
			"name" => "name",
			));
		
		$fieldset->addField('image', 'image', array(
			'label' => Mage::helper('homepagebanner')->__('Upload image'),
			'name' => 'image',
			'note' => '(*.jpg, *.png, *.gif)',
			));
		$fieldset->addField("sortorder", "text", array(
			"label" => Mage::helper("homepagebanner")->__("Sort Order"),
			"name" => "sortorder",
			));
		
		$fieldset->addField("url", "text", array(
			"label" => Mage::helper("homepagebanner")->__("Url"),
			"name" => "url",
			));

						// $fieldset->addField("status", "text", array(
						// "label" => Mage::helper("homepagebanner")->__("status"),
						// "name" => "status",
						// ));
		
		$fieldset->addField("status", "select", array(
			"label" => Mage::helper("homepagebanner")->__("Status"),					
						// "class" => "required-entry",
			"required" => false,
			"name" => "status",
			'values' => array(
				array('value'=>'0','label'=>'Disabled'),
				array('value'=>'1','label'=>'Enabled'),
				),
			));

						/*$fieldset->addField("website", "select", array(
						"label" => Mage::helper("homepagebanner")->__("Website"),					
						// "class" => "required-entry",
						"required" => false,
						"name" => "website",
       				    'values' => array(
                            array('value'=>'0','label'=>'Desktop'),
                            array('value'=>'1','label'=>'Mobile'),
                       ),
                       ));*/
                       $fieldset->addField('active_date', 'date', array(
                       	'name'               => 'active_date',
                       	'label'              => Mage::helper('homepagebanner')->__('Active Date'),
                       	'tabindex'           => 1,
                       	'image'              => $this->getSkinUrl('images/grid-cal.gif'),
                       	'format'             => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) ,
                       	'value'              => date( Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                       		strtotime('next weekday') ),
                       	'required' => 'true',
                       	));

                       $fieldset->addField('deactive_date', 'date', array(
                       	'name'               => 'deactive_date',
                       	'label'              => Mage::helper('homepagebanner')->__('De-Active Date'),
                       	'tabindex'           => 1,
                       	'image'              => $this->getSkinUrl('images/grid-cal.gif'),
                       	'format'             => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) ,
                       	'value'              => date( Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                       		strtotime('next weekday') )
                       	));

                       if (Mage::getSingleton("adminhtml/session")->getHomesliderData())
                       {
                       	$form->setValues(Mage::getSingleton("adminhtml/session")->getHomesliderData());
                       	Mage::getSingleton("adminhtml/session")->setHomesliderData(null);
                       } 
                       elseif(Mage::registry("homeslider_data")) {
                       	$form->setValues(Mage::registry("homeslider_data")->getData());
                       }
                       return parent::_prepareForm();
                   }
               }
