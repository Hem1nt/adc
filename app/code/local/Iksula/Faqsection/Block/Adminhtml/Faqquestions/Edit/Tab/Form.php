<?php
class Iksula_Faqsection_Block_Adminhtml_Faqquestions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("faqsection_form", array("legend"=>Mage::helper("faqsection")->__("Item information")));

				
						// $fieldset->addField("sections_id", "text", array(
						// "label" => Mage::helper("faqsection")->__("Section Id"),
						// "name" => "sections_id",
						// ));
						
						$fieldset->addField("sections_id", "select", array(
							"label" => Mage::helper("faqsection")->__("faqsection"),					
						// "class" => "required-entry",
							"required" => false,
							"name" => "sections_id",
							'values'   => Iksula_Faqsection_Block_Adminhtml_Faqquestions_Grid::getValueArray5(),
							));

						$fieldset->addField("question", "textarea", array(
						"label" => Mage::helper("faqsection")->__("Question"),
						"name" => "question",
						));
					
						$fieldset->addField("answer", "editor", array(
						"label" => Mage::helper("faqsection")->__("Answer"),
						"name" => "answer",
						'config' => $wysiwygConfig,
						'style'     => 'width:700px; height:500px;',
						'wysiwyg'   => true,
						));
					

				if (Mage::getSingleton("adminhtml/session")->getFaqquestionsData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getFaqquestionsData());
					Mage::getSingleton("adminhtml/session")->setFaqquestionsData(null);
				} 
				elseif(Mage::registry("faqquestions_data")) {
				    $form->setValues(Mage::registry("faqquestions_data")->getData());
				}
				return parent::_prepareForm();
		}
}
