<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 *
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Oxl\Delivery\Api\Data;

/**
 * Marketplace product interface.
 *
 * @api
 */
interface OxlDeliveryInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const DESC = 'description';

    public const ECONT_CUSTOMR_INFO_URL = 'url';

    public const ECONT_SHOP_ID = 'id_shop';

    /**
     * Get desc.
     *
     * @return string|null
     */
    public function getPrivateKey();

    /**
     * Set Desc.
     *
     * @param bool $key
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setPrivateKey($key);

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string
     **/
    public function getEcontCustomerInfoUrl();

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param bool $key
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontCustomerInfoUrl($key);

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string
     **/
    public function getEcontShopId();

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param bool $key
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontShopId($key);

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param mixed $var
     * @return \Oxl\Delivery\Model\OxlDelivery
     **/
    public function prepareModel($var);

    /**
     * Undocumented function summary
     *
     * Undocumented function long description
     *
     * @param mixed $var
     * @return \Oxl\Delivery\Model\OxlDelivery
     **/
    public function setModel($var);
}
