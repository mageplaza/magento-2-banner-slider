<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BannerSlider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BannerSlider\Ui\Component\Listing\Column\CustomerGroup;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 * @package Mageplaza\BannerSlider\Ui\Component\Listing\Column\CustomerGroup
 */
class Options implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_customerGroupColFact;

    /**
     * Options constructor.
     *
     * @param CollectionFactory $customerGroupColFact
     */
    public function __construct(CollectionFactory $customerGroupColFact)
    {
        $this->_customerGroupColFact = $customerGroupColFact;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_generateCustomerGroupOptions();
    }

    /**
     * Get customer group options
     *
     * @return array
     */
    protected function _generateCustomerGroupOptions()
    {
        $options                 = [];
        $customerGroupCollection = $this->_customerGroupColFact->create();

        if (count($customerGroupCollection)) {
            foreach ($customerGroupCollection as $item) {
                $options[] = [
                    'label' => $item->getCustomerGroupCode(),
                    'value' => $item->getCustomerGroupId(),
                ];
            }
        }

        return $options;
    }
}
