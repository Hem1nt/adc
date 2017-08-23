	<?php
class Iksula_Configurehomepage_Block_Category
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $this->addOption('', 'Select Category');
        $categories = Mage::helper('configurehomepage')->getCategoriesTreeView();
		foreach ($categories as $categorie) {
			$id = $categorie->getData('entity_id');
			$name = $categorie->getData('name');
			$this->addOption(addslashes($id.'-'.$name),addslashes($name));
		}
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}

