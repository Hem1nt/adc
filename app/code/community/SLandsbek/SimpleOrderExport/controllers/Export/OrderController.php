<?php
/**
 * NOTICE OF LICENSE
 *
 * The MIT License
 *
 * Copyright (c) 2009 S. Landsbek (slandsbek@gmail.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    SLandsbek_SimpleOrderExport
 * @copyright  Copyright (c) 2009 S. Landsbek (slandsbek@gmail.com)
 * @license    http://opensource.org/licenses/mit-license.php  The MIT License
 */

/**
 * Controller handling order export requests.
 */
class SLandsbek_SimpleOrderExport_Export_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Exports orders defined by id in post param "order_ids" to csv and offers file directly for download
     * when finished.
     */

    public function couponreportexportAction()
    {
      $orders = $this->getRequest()->getPost('order_ids', array());
      if(empty($orders) || (isset($orders[0]) && empty($orders[0]))) {
        Mage::getSingleton('adminhtml/session')->addError("Please select some order");
        $this->_redirectReferer();
        return;
      }
      $file = Mage::getModel('slandsbek_simpleorderexport/export_couponreportexport')->exportOrders($orders);
      $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
    }

    public function descriptorreportexportAction()
    {
      $orders = $this->getRequest()->getPost('order_ids', array());
      if(empty($orders) || (isset($orders[0]) && empty($orders[0]))) {
        Mage::getSingleton('adminhtml/session')->addError("Please select some order");
        $this->_redirectReferer();
        return;
      }
      $file = Mage::getModel('slandsbek_simpleorderexport/export_descriptorreportexport')->exportOrders($orders);
      $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
    }

    
    public function csvExportAction()
    {
        $orders = $this->getRequest()->getPost('order_ids', array());
        $file = Mage::getModel('slandsbek_simpleorderexport/export_csv')->exportOrders($orders);
        $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
    }
	/*public function invoiceshipAction()
   {
       if($this->getRequest()->getParam('order_id'))
       {
           $orders=$this->getRequest()->getParam('order_id');
       }
       else
	    {
           $orders = $this->getRequest()->getPost('order_ids', array());
       }
       $file = Mage::getModel('slandsbek_simpleorderexport/export_csv')->pdfinvoicesship($orders);
	    $this->_prepareDownloadResponse('Invship'.Mage::getSingleton('core/date')>date('Y-m-d_H-i-s').'.pdf', $file>render(), 'application/pdf');
     
   }*/
}
?>