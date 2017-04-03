<?php
class Iksula_Overrides_Block_Checkout_Links extends Mage_Checkout_Block_Links
{
    public function addCartLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Mage_Checkout')) {
            $count = $this->getSummaryQty() ? $this->getSummaryQty()
                : $this->helper('checkout/cart')->getSummaryCount();
            if ($count == 1) {
                $html = $this->__('My Shopping Cart <span class="shopping_count">(%s item)</span>', $count);
                $text = $this->__('My Shopping Cart (%s item)', $count);
            } elseif ($count > 0) {
                $text = $this->__('My Shopping Cart (%s items)', $count);
                $html = $this->__('My Shopping Cart <span class="shopping_count">(%s items)</span>', $count);
            } else {
                $text = $this->__('My Shopping Cart (%s item)', 0);
                $html = $this->__('My Shopping Cart <span class="shopping_count">(%s item)</span>', 0);
            }

            $parentBlock->removeLinkByUrl($this->getUrl('checkout/cart'));
            $parentBlock->addLink($html, 'checkout/cart',$text, true, array(), 50, null, 'class="top-link-cart"');
        }
        return $this;
    }

}
            