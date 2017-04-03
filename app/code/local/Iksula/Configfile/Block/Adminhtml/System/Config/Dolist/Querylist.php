<?php
class Iksula_Configfile_Block_Adminhtml_System_Config_Dolist_Querylist extends Mage_Core_Block_Html_Select
{

    private $_QueryList;
    protected function _getQueryList(){
        if (is_null($this->_QueryList)) {
            $this->_QueryList = array();        
            $id = 0;
            foreach (unserialize(Mage::getStoreConfig("custom_snippet/snippet/query_type")) as $mapping) {
                $this->_QueryList[$id] = $mapping;
                $id++;
            }
        }
        // Mage::log($this->_QueryList,NULL,"system123.log");
        return $this->_QueryList;
    }
    
    public function setInputName($value){
        return $this->setName($value);
    }

    protected function _toHtml(){
        if (!$this->getOptions()) {
            foreach ($this->_getQueryList() as $id => $template) {
                $value = $template['query'];
                $label = $template['query'];
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
