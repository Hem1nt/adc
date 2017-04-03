<?php
class Iksula_Overrides_Helper_Http extends Mage_Core_Helper_Http
{
    /**
     * Retrieve Client Remote Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getRemoteAddr($ipToLong = false)
    {
        if (is_null($this->_remoteAddr)) {
            $headers = $this->getRemoteAddrHeaders();
            foreach ($headers as $var) {
                if ($this->_getRequest()->getServer($var, false)) {
                    $this->_remoteAddr = $_SERVER[$var];
                    break;
                }
            }

            $HTTP_TRUE_CLIENT_IP = $this->_getRequest()->getServer('HTTP_TRUE_CLIENT_IP');

            if($HTTP_TRUE_CLIENT_IP) {
                $this->_remoteAddr = $HTTP_TRUE_CLIENT_IP;
            } else {
               if(!$this->_remoteAddr) {
                    $this->_remoteAddr = $this->_getRequest()->getServer('REMOTE_ADDR');
                }
            }
        }

        if (!$this->_remoteAddr) {
            return false;
        }

        return $ipToLong ? ip2long($this->_remoteAddr) : $this->_remoteAddr;
    }

    
}

