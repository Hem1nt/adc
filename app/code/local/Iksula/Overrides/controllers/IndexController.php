<?php
require_once Mage::getModuleDir('controllers', 'Mage_Wishlist') . DS . 'IndexController.php';
class Iksula_Overrides_IndexController extends Mage_Core_Controller_Front_Action{

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * If true, authentication in this controller (wishlist) could be skipped
     *
     * @var bool
     */
    protected $_skipAuthentication = false;

    public function IndexAction() {

	    $this->loadLayout();
	    // $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
     //    $breadcrumbs->addCrumb("home", array(
     //            "label" => $this->__("Home Page"),
     //            "title" => $this->__("Home Page"),
     //            "link"  => Mage::getBaseUrl()
		   // ));

     //    $breadcrumbs->addCrumb("wislist", array(
     //            "label" => $this->__("Wishlist"),
     //            "title" => $this->__("Wishlist")
		   // ));

        $session = Mage::getSingleton('customer/session');
        $block   = $this->getLayout()->getBlock('customer.wishlist');
        $referer = $session->getAddActionReferer(true);
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
            if ($referer) {
                $block->setRefererUrl($referer);
            }
        }

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('wishlist/session');

        $this->renderLayout();
    }

    /**
     * Extend preDispatch
     *
     * @return Mage_Core_Controller_Front_Action|void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_skipAuthentication && !Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!Mage::getSingleton('customer/session')->getBeforeWishlistUrl()) {
                Mage::getSingleton('customer/session')->setBeforeWishlistUrl($this->_getRefererUrl());
            }
            Mage::getSingleton('customer/session')->setBeforeWishlistRequest($this->getRequest()->getParams());
        }
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Set skipping authentication in actions of this controller (wishlist)
     *
     * @return Mage_Wishlist_IndexController
     */
    public function skipAuthentication()
    {
        $this->_skipAuthentication = true;
        return $this;
    }

    /**
     * Retrieve wishlist object
     * @param int $wishlistId
     * @return Mage_Wishlist_Model_Wishlist|bool
     */
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }

        return $wishlist;
    }

    /**
     * Adding new item
     */
    public function addAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = Mage::getSingleton('customer/session');

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            // to stop duplicate entries of product start
            $_productCollection = Mage::helper('wishlist')->getWishlistItemCollection()
            ->addFieldToFilter('product_id', $productId);

            if($_productCollection->count() > 0) {
                $session->addError($this->__('Product Already exits in wishlist.'));
                $this->_redirect('*/');
                return;
            }
            // to stop duplicate entries of product ends

            $result = $wishlist->addNewItem($product, $buyRequest);
            if( $product->getTypeId()!='simple'):
                if (is_string($result)) {
                    Mage::throwException($result);
                }
                $wishlist->save();
            endif;

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.', $product->getName(), Mage::helper('core')->escapeUrl($referer));
            $session->addSuccess($message);
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist.'));
        }

        $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }

     protected function _processLocalizedQty($qty)
    {
        if (!$this->_localFilter) {
            $this->_localFilter = new Zend_Filter_LocalizedToNormalized(
                array('locale' => Mage::app()->getLocale()->getLocaleCode())
            );
        }
        $qty = $this->_localFilter->filter((float)$qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
    }


    /**
     * Add cart item to wishlist and remove from cart
     */
    public function fromcartAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');
        $session = Mage::getSingleton('checkout/session');

        try{
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item) {
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested cart item doesn't exist")
                );
            }

            $productId  = $item->getProductId();
            // $buyRequest = $item->getBuyRequest();

            // to stop duplicate entries of product start
            $_productCollection = Mage::helper('wishlist')->getWishlistItemCollection()
            ->addFieldToFilter('product_id', $productId);

            if($_productCollection->count() > 0) {
                $session->addError($this->__('Product Already exits in wishlist.'));
                $this->_redirect('*/');
                return;
            }
            // to stop duplicate entries of product ends

            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }

            $buyRequest = new Varien_Object($requestParams);

            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->getId() || !$product->isVisibleInCatalog()) {
                $session->addError($this->__('Cannot specify product.'));
                $this->_redirect('*/');
                return;
            }
            // $wishlist->addNewItem($productId, $buyRequest);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if( $product->getTypeId()!='simple'):
                if (is_string($result)) {
                    Mage::throwException($result);
                }
                $wishlist->save();
            endif;

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );


            Mage::helper('wishlist')->calculate();
            $productName = Mage::helper('core')->escapeHtml($item->getProduct()->getName());
            $session->addSuccess(
                Mage::helper('wishlist')->__("%s has been moved to wishlist", $productName)
            );
            // $wishlistName = Mage::helper('core')->escapeHtml($wishlist->getName());
            // $wishlist->save();

            // remove product from cart
            $productIds[] = $productId;
            $cart->getQuote()->removeItem($itemId);
            $cart->save();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('wishlist')->__('Cannot move item to wishlist'));
        }

        return $this->_redirectUrl(Mage::helper('checkout/cart')->getCartUrl());
    }


    /**
     * Share wishlist
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function sendAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $emails  = explode(',', $this->getRequest()->getPost('emails'));
        $message = nl2br(htmlspecialchars((string) $this->getRequest()->getPost('message')));
        $error   = false;
        if (empty($emails)) {
            $error = $this->__('Email address can\'t be empty.');
        }
        else {
            foreach ($emails as $index => $email) {
                $email = trim($email);
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    $error = $this->__('Please input a valid email address.');
                    break;
                }
                $emails[$index] = $email;
            }
        }
        if ($error) {
            Mage::getSingleton('wishlist/session')->addError($error);
            Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
            return;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        try {
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            /*if share rss added rss feed to email template*/
            if ($this->getRequest()->getParam('rss_url')) {
                $rss_url = $this->getLayout()
                    ->createBlock('wishlist/share_email_rss')
                    ->setWishlistId($wishlist->getId())
                    ->toHtml();
                $message .= $rss_url;
            }
            $wishlistBlock = $this->getLayout()->createBlock('wishlist/share_email_items')->toHtml();

            $emails = array_unique($emails);
            /* @var $emailModel Mage_Core_Model_Email_Template */
            $emailModel = Mage::getModel('core/email_template');

            $sharingCode = $wishlist->getSharingCode();
            foreach ($emails as $email) {
                $emailModel->sendTransactional(
                    Mage::getStoreConfig('wishlist/email/email_template'),
                    Mage::getStoreConfig('wishlist/email/email_identity'),
                    $email,
                    null,
                    array(
                        'customer'       => $customer,
                        'salable'        => $wishlist->isSalable() ? 'yes' : '',
                        'items'          => $wishlistBlock,
                        'addAllLink'     => Mage::getUrl('*/shared/allcart', array('code' => $sharingCode)),
                        'viewOnSiteLink' => Mage::getUrl('*/shared/index', array('code' => $sharingCode)),
                        'message'        => $message
                    )
                );
            }

            $wishlist->setShared(1);

            $translate->setTranslateInline(true);

            Mage::dispatchEvent('wishlist_share', array('wishlist' => $wishlist));
            Mage::getSingleton('customer/session')->addSuccess(
                $this->__('Your Wishlist has been shared.')
            );
            $this->_redirect('*/*', array('wishlist_id' => $wishlist->getId()));
        }
        catch (Exception $e) {
            $translate->setTranslateInline(true);

            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
        }
    }


    public function allcartAction()
    {
        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('noRoute');
            return ;
        }
        $isOwner    = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        $cart = Mage::getSingleton('checkout/cart');

        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();

        $qtys = $this->getRequest()->getParam('qty');

        foreach ($collection as $item) {
            /** @var Mage_Wishlist_Model_Item */
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);

                $cart->addProduct($item->getProduct());
                $cart->save();
            // if ($item->addToCart($cart, $isOwner)) {
                $addedItems[] = $item->getProduct();
            // }


            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
            }
        }

        if ($isOwner) {
            $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
        } else {
            $indexUrl = Mage::getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
        }
        if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', join(', ', $products));
        }

        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = Mage::getSingleton('wishlist/session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
                $redirectUrl = $indexUrl;
            }
        }

        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            }
            catch (Exception $e) {
                Mage::getSingleton('wishlist/session')->addError($this->__('Cannot update wishlist'));
                $redirectUrl = $indexUrl;
            }

            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            Mage::getSingleton('checkout/session')->addSuccess(
                Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), join(', ', $products))
            );
        }
        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        Mage::helper('wishlist')->calculate();

        $this->_redirectUrl($redirectUrl);
    }

    /**
    * Function used to set the session of the user which is coming from the one page website
    * @author Manoj Chaurasia
    * @param  Parameter captured Order id and Customer Name
    * @return save the value in session
    **/
    public function thankyouCCAction() {

      // $orderId = Mage::app()->getRequest()->getParam('orderid');
      // $CustomerName = Mage::app()->getRequest()->getParam('customer_name');
      // $this->loadLayout();
      // $this->renderLayout();
       $this->loadLayout();
       $this->getLayout()->getBlock("head")->setTitle($this->__("Overrides"));
       $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
       $breadcrumbs->addCrumb("home", array(
        "label" => $this->__("Home Page"),
        "title" => $this->__("Home Page"),
        "link"  => Mage::getBaseUrl()
        ));

       $breadcrumbs->addCrumb("overrides", array(
        "label" => $this->__("Overrides"),
        "title" => $this->__("Overrides")
        ));

       $this->renderLayout();

      // // print_r(Mage::app()->getRequest()->getParams());exit;
      // Mage::getSingleton('core/session')->unsCCOrderId();
      // Mage::getSingleton('core/session')->unsCCCustomerName();
      // Mage::getSingleton('core/session')->setCCOrderId($orderId);
      // Mage::getSingleton('core/session')->setCCCustomerName($CustomerName);
      // $url = "http://alldaychemist.com/thankyou.html";
      // Mage::app()->getFrontController()->getResponse()->setRedirect($url);

    }

    public function ordertotalAction(){
      $data = $this->getRequest()->getParams();
      if($data["order_amt"] != ""){
        $order = Mage::getModel("sales/order")->load($data["orderid"]);
        if(($order->getCustomGrandTotal() == 0 && $order->getCustomBaseGrandTotal() == 0) || ($order->getCustomGrandTotal() == '' && $order->getCustomBaseGrandTotal() == '')){
          $order->setCustomGrandTotal($order->getGrandTotal())
                      ->setCustomBaseGrandTotal($order->getBaseGrandTotal())
                      ->save();
        }
        $ordertotal = $order->getCustomGrandTotal()+$data["order_amt"];
        $orderbasetotal = $order->getCustomBaseGrandTotal()+$data["order_amt"];
        $order->setCustomCharges($data["order_amt"])
              ->setCustomChargesMessage($data["order_reason"])
              ->setGrandTotal($ordertotal)
              ->setBaseGrandTotal($orderbasetotal)
              ->setTotalPaid($ordertotal)
              ->save();
      }
    }
}
