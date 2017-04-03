<?php

class TS_Reports_Block_Report_Filter_Form_Sales extends Mage_Sales_Block_Adminhtml_Report_Filter_Form { // Mage_Adminhtml_Block_Report_Filter_Form

	private function getShortcuts($shortcutName, $shortcutFunction, $clearShortcutText, $defaultTitle){
		$shortcuts = array();
		for($i = 1; $i < 3; $i++){
			$shortcut = Mage::helper('ts_reports')->getConfigData($shortcutName.'_select'.$i);
			if(!empty($shortcut)){
				$shortcutTitle = Mage::helper('ts_reports')->getConfigData($shortcutName.'_select'.$i.'_title');
				if(empty($shortcutTitle)) $shortcutTitle = Mage::helper('reports')->__('Shortcut #').$i;
				$shortcuts[] = '<a onclick="'.$shortcutFunction.'('.$shortcut.')">'.$shortcutTitle.'</a>';
			}
		}
		$shortcuts[] = '<a onclick="'.$shortcutFunction.'()">'.Mage::helper('reports')->__($clearShortcutText).'</a>';
		return Mage::helper('reports')->__($defaultTitle). ': '.implode(', ', $shortcuts);
	}
	
    protected function _prepareForm(){
		parent::_prepareForm();
		
        $htmlIdPrefix = 'ts_reports_';
		
		$form = $this->getForm();
		$form->setData('action', $this->getCurrentUrl());		
        $form->setHtmlIdPrefix($htmlIdPrefix);

		$fieldset = $form->getElement('base_fieldset');
		
		$this->unsetChild('form_after');
		if($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
			$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
				->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
				->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
				->addFieldDependence('order_statuses', 'show_order_statuses', '1')
			);
		}
		
		$fieldset->removeField('from');
		$fieldset->removeField('to');
		
		$to_datefield_name = 'to';
		$from_price_name = 'price_from';
		$to_price_name = 'price_to';
		$excl_tax_name = 'excl_tax';
		
		$data = $this->getFilterData()->getData();
		
		$priceTypes = array();		
		foreach(Mage::getModel('ts_reports/types')->getTypes() as $key => $value){
			$priceTypes[] = array(
				'label' => Mage::helper('ts_reports')->__($key),
				'value' => $value
			);
		}
		
		$dateFormatCaledar = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		$javascript_variables = '<script type="text/javascript">
			//<![CDATA[	
				var date_from 		= \''.$htmlIdPrefix.'from\';
				var date_to	  		= \''.$htmlIdPrefix.'to\';
				var date_format 	= \''. $dateFormatCaledar .'\';
				var priceTypeElemId 	= "'.$htmlIdPrefix.'price_types";
				var priceTypeShowElemId = "'.$htmlIdPrefix.'show_price_types";
				var orderStatusElemId 	= "'.$htmlIdPrefix.'order_statuses";
				var orderStatusShowElemId = "'.$htmlIdPrefix.'show_order_statuses";
				var categoryElemId		= "'.$htmlIdPrefix.'categories";
				var categoryShowElemId 	= "'.$htmlIdPrefix.'show_categories";
				var price_from 		= \''.$htmlIdPrefix.$from_price_name.'\';
				var price_to	  	= \''.$htmlIdPrefix.$to_price_name. '\';
				//]]>
			</script>';	
		
		$orderShortcuts 	= $this->getShortcuts('orderstatus', 'checkOrderStatus', 'Clear selected order statuses', 'Order status');
		$priceTypeShortcuts = $this->getShortcuts('pricetype', 	 'checkPriceType',	 'Clear selected price types',	  'Price types');
		$categoryShortcuts 	= $this->getShortcuts('category', 	 'checkCategory', 	 'Clear selected categories',	  'Category');		
		
		$fieldset->addField('shortcuts', 'note', array(
            'name'      => 'shortcuts',
            'label'     => Mage::helper('reports')->__('Shortcuts'),
            'text'		=> '<small>'.$orderShortcuts.'<br />'.$priceTypeShortcuts.'<br />'.$categoryShortcuts.'</small>',
            'required'  => false,
			'after_element_html' => $javascript_variables			
		), '^');
		
		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

		$toDate = new Varien_Data_Form_Element_Date;
        $toDate->setData(array(
            'name'      => $to_datefield_name,
            'html_id'   => $to_datefield_name,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
        ));
        if(isset($data[$to_datefield_name])) $toDate->setValue($data[$to_datefield_name], $dateFormatIso);
		else $toDate->setValue('', $dateFormatIso);
        $toDate->setFormat($dateFormatIso);
        $toDate->setClass('required-entry');
        $toDate->setForm($form);

        $fieldset->addField('from', 'date', array(
            'name'      => 'from',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('From').' - '.Mage::helper('reports')->__('To'),
            'title'     => Mage::helper('reports')->__('From'),
            'required'  => true,
			'after_element_html' => ' &mdash; '. $toDate->getElementHtml() .'<br />
							<small>Date shortcuts:
							<a onclick="changeMonths(-1)">'.Mage::helper('ts_reports')->__('Previous month').'</a>,'.'
							<a onclick="changeMonths(0)">'.Mage::helper('ts_reports')->__('Current month').'</a>,'.'
							<a onclick="changeMonths(1)">'.Mage::helper('ts_reports')->__('Next month').'</a>'.' / 
							<a onclick="fromYearToToday(-1)">'.Mage::helper('ts_reports')->__('Last year to today').'</a>,'.'
							<a onclick="fromYearToToday(0)">'.Mage::helper('ts_reports')->__('This year to today').'</a>'.'
							</small>',
        ), 'period_type');
		
		
		$fieldset->addField('show_price_types', 'select', array(
			'name'      => 'show_price_types',
			'label'     => Mage::helper('reports')->__('Price type'),
			'options'   => array(
					'0' => Mage::helper('reports')->__('Any'),
					'1' => Mage::helper('reports')->__('Specified'),
				),
			'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Price Types'),
		), 'order_statuses');

		$excl_pricetype_name = 'excl_pricetype';
		$fieldset->addField('price_types', 'multiselect', array(
			'name'      => 'price_types',
			'values'    => $priceTypes,
			'display'   => 'none',
			'note'		=> Mage::helper('reports')->__('Exclude selected price types?') .' <input id="'.$htmlIdPrefix.$excl_pricetype_name.'" name="'.$excl_pricetype_name.'" onclick="this.value = this.checked ? 1 : 0;" type="checkbox"'.((isset($data) && isset($data[$excl_pricetype_name]))? ' value="'.$data[$excl_pricetype_name].'"'.($data[$excl_pricetype_name] == '1'?' checked':''):'').'> '
		), 'show_price_types');

		if($this->getFieldVisibility('show_price_types') && $this->getFieldVisibility('price_types')) {
			$after_form = $this->getChild('form_after');
			if(!$after_form){
				$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'));
				$after_form = $this->getChild('form_after');
			}
			$after_form->addFieldMap("{$htmlIdPrefix}show_price_types", 'show_price_types')
				->addFieldMap("{$htmlIdPrefix}price_types", 'price_types')
				->addFieldDependence('price_types', 'show_price_types', '1');
		}
		
        if(is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            $fieldset->addField('show_actual_columns', 'select', array(
                'name'       => 'show_actual_columns',
                'options'    => array(
                    '1' => Mage::helper('reports')->__('Yes'),
                    '0' => Mage::helper('reports')->__('No')
                ),
                'label'      => Mage::helper('reports')->__('Show Actual Values'),
            ));
        }
		
		$categories = Mage::getModel('ts_reports/system_config_source_select_category')->toOptionArray();
		
		$fieldset->addField('show_categories', 'select', array(
			'name'      => 'show_categories',
			'label'     => Mage::helper('reports')->__('Category'),
			'options'   => array(
					'0' => Mage::helper('reports')->__('Any'),
					'1' => Mage::helper('reports')->__('Specified'),
				),
			'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Price Types'),
		), 'price_types');
			
		$excl_cat_name = 'excl_cat';
		$fieldset->addField('categories', 'multiselect', array(
			'name'      => 'categories',
			'values'    => $categories,
			'note'		=> Mage::helper('reports')->__('Exclude selected categories?') .' <input id="'.$htmlIdPrefix.$excl_cat_name.'" name="'.$excl_cat_name.'" onclick="this.value = this.checked ? 1 : 0;" type="checkbox"'.((isset($data) && isset($data[$excl_cat_name]))? ' value="'.$data[$excl_cat_name].'"'.($data[$excl_cat_name] == '1'?' checked':''):'').'> ',
			'display'   => 'none'
		), 'show_categories');

		if($this->getFieldVisibility('show_categories') && $this->getFieldVisibility('categories')) {
			$after_form = $this->getChild('form_after');
			if(!$after_form){
				$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'));
				$after_form = $this->getChild('form_after');
			}
			$after_form->addFieldMap("{$htmlIdPrefix}show_categories", 'show_categories')
				->addFieldMap("{$htmlIdPrefix}categories", 'categories')
				->addFieldDependence('categories', 'show_categories', '1');
		}
		
		$shortcuts = '';
		$priceRangeShortcuts = Mage::helper('ts_reports')->getConfigData('pricerange_defaults');
		if(!empty($priceRangeShortcuts)){
			$priceRangeShortcuts = preg_split('/\R/', $priceRangeShortcuts); 		// remove new lines
			foreach($priceRangeShortcuts as $shortcut){
				$shortcut = preg_replace('/\s+/', '', $shortcut);
				@list($priceFrom, $priceTo) = explode("-", $shortcut);
				if($priceFrom || $priceTo) $shortcuts[] = '<a onclick="changePriceFrom('.$priceFrom.');changePriceTo('.$priceTo.')">'.$priceFrom.'-'.$priceTo.'</a>';
			}			
			$shortcuts = implode(', ',$shortcuts);
		}
        $fieldset->addField($from_price_name, 'text', array(
            'name'      => $from_price_name,
            'label'     => Mage::helper('reports')->__('Price From').' - '.Mage::helper('reports')->__('To'),
            'title'     => Mage::helper('reports')->__('From'),
			'style'		=> 'width:120px !important;',
			'class'		=> 'validate-number',
            'after_element_html'	=> 
							' &nbsp;&mdash;&nbsp; <input id="'.$htmlIdPrefix.$to_price_name.'" name="'.$to_price_name.'" title="'.Mage::helper('reports')->__('To').'" type="text" class="input-text validate-number" style="width:120px !important;"'.((isset($data) && isset($data[$to_price_name]))? ' value="'.$data[$to_price_name].'"':'').'>',
			'note'		=> 
							Mage::helper('reports')->__('Prices exclude tax?') .' <input id="'.$htmlIdPrefix.$excl_tax_name.'" name="'.$excl_tax_name.'" onclick="this.value = this.checked ? 1 : 0;" type="checkbox"'.((isset($data) && isset($data[$excl_tax_name]))? ' value="'.$data[$excl_tax_name].'"'.($data[$excl_tax_name] == '1'?' checked':''):'').'> '
								
								.'<br />'.Mage::helper('reports')->__('Price range shortcuts:') .' '. $shortcuts,
        ), 'categories');
		
        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

}
