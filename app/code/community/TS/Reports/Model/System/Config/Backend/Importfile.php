<?php

class TS_Reports_Model_System_Config_Backend_ImportFile extends Mage_Adminhtml_Model_System_Config_Backend_File {
	
	private function csvToArray($filename = '', $delimiter = ',', $enclosure = "'"){ 
		if(!file_exists($filename) || !is_readable($filename)) return FALSE;
		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE){
			while (($row = fgetcsv($handle, 1000, $delimiter, $enclosure)) !== FALSE){
				if(!$header) $header = $row;
				else $data[] = array_combine($header, $row);
			}
			fclose($handle);
		}
		return $data;
	}
	
	private function formatCSV(&$data){
		foreach($data as &$element){
			$element['date_from'] .= ' 00:00:00';
			$element['date_to']   .= ' 23:59:59';
			$element['price'] = str_replace(',', '.', $element['price']);	
			$element['categories'] = array_filter(explode('|',$element['categories']));
		}	
		return $data;
	}
	
	protected function _beforeSave(){
		$helper = Mage::helper('ts_reports');
		$this->setValue('');		
        if(isset($_FILES['groups']['tmp_name']) && $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']){
            try {
                $file = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];		
				$data = $this->csvToArray($file);
				if(!empty($data)){
					$data = $this->formatCSV($data, $helper->getConfigData('csv_delimiter'), $helper->getConfigData('csv_enclosure'));
					$count = Mage::getModel('ts_reports/init_reportitems')->import($data);
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ts_reports')->__('Total of %d record(s) were imported.', $count));
				} else Mage::throwException("Not a valid CSV file or the CSV file is not properly formatted.");
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }
        }
        return $this;
    }

}
