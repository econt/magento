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
    const ENTITY_ID = 'entity_id';

    const TITLE = 'title';

    const DESC = 'description';

    const ECONT_CUSTOMR_INFO_URL = 'url';

    const ECONT_SHOP_ID = 'id_shop';
    /**#@-*/

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setId($id);

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     */
    public function setTitle($title);

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
    public function setPrivateKey( $key );

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string
     **/
    public function getEcontCustomerInfoUrl();

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     * @param bool $key
     *
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontCustomerInfoUrl( $key );

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string
     **/
    public function getEcontShopId();

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     * 
     * @param bool $key
     * 
     * @return \Oxl\Delivery\Api\Data\OxlDeliveryInterface
     **/
    public function setEcontShopId( $key );

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param mixed $var
     * @return \Oxl\Delivery\Model\OxlDelivery
     **/
    public function prepareModel( $var );

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param mixed $var
     * @return \Oxl\Delivery\Model\OxlDelivery
     **/
    public function setModel( $var );
}