<?php
class Iksula_Configfile_Block_Adminhtml_System_Config_Dolist_Templatelist extends Mage_Core_Block_Html_Select
{

    private $_dolistemtTemplateList;
    protected function _getTemplateList(){
        if (is_null($this->_dolistemtTemplateList)) {
            $this->_dolistemtTemplateList = array();
            
            $collection = Mage::getModel('configfile/status_template')->toOptionArray();
            foreach ($collection as $id => $item) {
                $this->_dolistemtTemplateList[$id] = $item;
            }
        }
        return $this->_dolistemtTemplateList;
    }
    
    public function setInputName($value){
        return $this->setName($value);
    }

    protected function _toHtml(){
        if (!$this->getOptions()) {
            foreach ($this->_getTemplateList() as $id => $template) {
                $value = $template['value'];
                $label = $template['label'];
                if ($value != '' || $label == '') {
                    $this->addOption($value, addslashes($label));
                }
            }
        }
        return parent::_toHtml();
    }

    protected function _optionToHtml($option, $selected = false){
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
        }

        $params = '';
        if (!empty($option['params']) && is_array($option['params'])) {
            foreach ($option['params'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $keyMulti => $valueMulti) {
                        $params .= sprintf(' %s="%s" ', $keyMulti, $valueMulti);
                    }
                } else {
                    $params .= sprintf(' %s="%s" ', $key, $value);
                }
            }
        }

        return sprintf(
            '<option value="%s"%s %s>%s</option>',
            $this->htmlEscape($option['value']),
            $selectedHtml,
            $params,
            $this->htmlEscape($option['label'])
        );
    }

    public function calcOptionHash($optionValue){
        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }
}
