<?php

namespace Oxl\Delivery\Model\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * The private key of the shop in the settings. The field is "obscure", so the page shows ****** instead of the key.
 * When the field comes back as ****** or empty, the saved key is kept; a new key replaces it.
 * The key is stored as before (not encrypted), because Helper\Data::getConfig() reads it as it is.
 */
class PrivateKey extends Value
{
    /**
     * @return $this
     */
    public function beforeSave()
    {
        $value = trim((string)$this->getValue());
        if ($value === '' || preg_match('/^\*+$/', $value)) {
            $this->_dataSaveAllowed = false;
        } else {
            $this->setValue($value);
        }

        return parent::beforeSave();
    }
}
