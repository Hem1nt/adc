<?php
class Iksula_Configurehomepage_Block_Serializer
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $catdata;

    public function _prepareToRender()
    {

        $this->addColumn('category_id', array(
            'label' => Mage::helper('configurehomepage')->__('Category'),
            'renderer' => $this->_getCategory(),
            'style' => 'width:50px',

        ));
        $this->addColumn('product_sku', array(
            'label' => Mage::helper('configurehomepage')->__('Product skus'),
            'style' => 'width:300px',
        ));
        

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('configurehomepage')->__('Add');
    }

  

    protected function  _getCategory() 
    {
        if (!$this->catdata) {
            $this->catdata = $this->getLayout()->createBlock(
                'configurehomepage/category', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->catdata;
    }



    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getCategory()
                ->calcOptionHash($row->getData('category_id')),
            'selected="selected"'
        );
    }
}