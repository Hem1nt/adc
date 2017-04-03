<?php
class Iksula_Overrides_Block_GoogleAnalytics_Ga extends Mage_GoogleAnalytics_Block_Ga
{
	protected function _getPageTrackingCode($accountId)
{
	// echo $accountId;exit();
    $pageName   = trim($this->getPageName());
    $hostName = 'auto';
    $displayfeatures = 'displayfeatures';
    $optPageURL = '';
    if ($pageName && preg_match('/^\/.*/i', $pageName)) {
        $optPageURL = ", '{$this->jsQuoteEscape($pageName)}'";
    }
    return "
        ga('create', '".$this->jsQuoteEscape($accountId)."', '".$hostName."');
        ga('require', '".$displayfeatures."');
        ga('send', 'pageview' ".$optPageURL.");
    ";
}
protected function _getOrdersTrackingCode()
{
    $orderIds = $this->getOrderIds();
    if (empty($orderIds) || !is_array($orderIds)) {
        return;
    }
    $collection = Mage::getResourceModel('sales/order_collection')
        ->addFieldToFilter('entity_id', array('in' => $orderIds))
    ;
    $result = array("
        // Transaction code...
        ga('require', 'ecommerce', 'ecommerce.js');
    ");

    foreach ($collection as $order) {
        if ($order->getIsVirtual()) {
            $address = $order->getBillingAddress();
        } else {
            $address = $order->getShippingAddress();
        }

        $result[] = "
            ga('ecommerce:addTransaction', {
                id:          '".$order->getIncrementId()."', // Transaction ID
                affiliation: '".$this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName())."', // Affiliation or store name
                revenue:     '".$order->getBaseGrandTotal()."', // Grand Total
                shipping:    '".$order->getBaseShippingAmount()."', // Shipping cost
                tax:         '".$order->getBaseTaxAmount()."', // Tax

            });
        ";
        foreach ($order->getAllVisibleItems() as $item) {

            $result[] = "
            ga('ecommerce:addItem', {

                id:       '".$order->getIncrementId()."', // Transaction ID.
                sku:      '".$this->jsQuoteEscape($item->getSku())."', // SKU/code.
                name:     '".$this->jsQuoteEscape($item->getName())."', // Product name.
                category: '', // Category or variation. there is no 'category' defined for the order item
                price:    '".$item->getBasePrice()."', // Unit price.
                quantity: '".$item->getQtyOrdered()."' // Quantity.

            });
        ";

        }
        $result[] = "ga('ecommerce:send');";
    }
    return implode("\n", $result);
}

}
			